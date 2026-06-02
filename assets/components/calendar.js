import Alpine from "alpinejs";

/**
 * @param {{
 * urlParams: Object
 * mapEvent: (item) => Object,
 * onAdd?: (self: this) => void,
 * popoverTitle?: (data: Object) => string,
 * popoverContent?: (data: Object) => string,
 * onPopoverHover?: (popoverElement: HTMLElement, data: Object, self: this) => void,
 * id?: number|string,
 * }}
 */
export function calendarComponent({
    urlParams,
    mapEvent,
    onAdd = () => null,
    popoverTitle = () => ``,
    popoverContent = () => ``,
    onPopoverHover = (el) => null,
    id = null,
} = {}) {
    return {
        calendar: null,
        popover: null,
        popoverTimeout: null,
        popoverEventId: null,

        init() {
            const params = new URLSearchParams(urlParams).toString();
            const element = this.$el;

            this.calendar = new FullCalendar.Calendar(element, {
                themeSystem: 'bootstrap5',
                locale: "es",
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: "addEvent dayGridMonth,dayGridWeek,dayGridDay",
                },
                initialView: "dayGridWeek",
                events: `?${params}`,
                eventDataTransform: (item) => ({
                    extendedProps: item,
                    ...mapEvent(item),
                }),
                eventDidMount: (info) => this.bindEventHover(info),
                datesSet: () => {
                    // Colocar iconos de Font Awesome
                    const prevIcon = element.querySelector('.fc-prev-button span');
                    if (prevIcon) prevIcon.className = 'fa-solid fa-chevron-left';

                    const nextIcon = element.querySelector('.fc-next-button span');
                    if (nextIcon) nextIcon.className = 'fa-solid fa-chevron-right';

                    const prevYearIcon = element.querySelector('.fc-prevYear-button span');
                    if (prevYearIcon) prevYearIcon.className = 'fa-solid fa-angles-left';

                    const nextYearIcon = element.querySelector('.fc-nextYear-button span');
                    if (nextYearIcon) nextYearIcon.className = 'fa-solid fa-angles-right';
                },
                eventClassNames: (info) => {
                    const start = new Date(info.event.start).getDate();
                    const now = new Date().getDate();

                    // Si el evento ya ha pasado, difuminarlo
                    if (start < now) {
                        return ['opacity-50', 'fc-event-past']; 
                    }
                    
                    return [];
                },
                customButtons: {
                    addEvent: {
                        text: 'Agregar evento',
                        hint: 'Crea un nuevo evento',
                        click: () => onAdd(this),
                    },
                },
            });
            
            // El click global se registra una sola vez aquí
            document.addEventListener('click', (e) => {
                if (this.popover && !e.target.closest('.popover') && !e.target.closest('.fc-event')) {
                    this.destroyPopover();
                }
            });

            this.calendar.render();
        },

        // Escuchadores del elemento del calendario
        bindEventHover(info) {
            const el = info.el;
            el.addEventListener('mouseenter', () => this.showPopover(info));
            el.addEventListener('mouseleave', () => this.startHideTimeout());
            el.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.showPopover(info); 
            });
        },

        destroyPopover() {
            if (this.popover) {
                this.popover.dispose();
                this.popover = null;
                this.popoverEventId = null;
            }
            if (this.popoverTimeout) {
                clearTimeout(this.popoverTimeout);
                this.popoverTimeout = null;
            }
        },

        startHideTimeout() {
            if (this.popoverTimeout) clearTimeout(this.popoverTimeout);
            this.popoverTimeout = setTimeout(() => this.destroyPopover(), 200);
        },

        showPopover(info) {
            const event = info.event;

            if (this.popoverTimeout) clearTimeout(this.popoverTimeout);
            if (this.popoverEventId === event.id) return;
            this.popoverEventId = event.id;

            // Si hay un popover abierto de otra clase, lo destruimos inmediatamente
            if (this.popover) this.destroyPopover();

            // Inicializar Popover
            this.popover = new bootstrap.Popover(info.el, {
                title: popoverTitle(event.extendedProps),
                content: popoverContent(event.extendedProps),
                html: true,
                trigger: 'manual',
                placement: 'auto',
                container: 'body',
                sanitize: false
            });

            this.popover.show();

            // Configurar acciones internas cuando el popover aparece en el DOM
            info.el.addEventListener('shown.bs.popover', () => {
                const openPopoverEl = document.querySelector('.popover:last-child');
                if (!openPopoverEl) return;

                // Mantener abierto si el mouse entra al popover
                openPopoverEl.addEventListener('mouseenter', () => {
                    if (this.popoverTimeout) clearTimeout(this.popoverTimeout);
                });
                openPopoverEl.addEventListener('mouseleave', () => this.startHideTimeout());

                // Clicks de acciones
                onPopoverHover(openPopoverEl, { id: event.id, ...event.extendedProps}, this);
            }, { once: true });
        },

        handleFormSucess({ id: eventId = null }) {
            if (eventId === id) {
                this.calendar.refetchEvents();
            }
        },
    }
}

Alpine.data("calendar", calendarComponent);