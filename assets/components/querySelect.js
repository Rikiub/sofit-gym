import Alpine from "alpinejs";
import { fetchApi } from "@/js/api.js";

Alpine.data("querySelect", ({
    params = {},
    searchParam = "search",
    itemKey = "id",
} = {}) => {
    return {
        items: [],
        search: null,
        popover: null,
        selected: null,
        itemKey,
        
        async init() {
            document.addEventListener('click', (e) => {
                if (!this.popover) return;
                const clickedInsidePopover = e.target.closest('.popover');
                const clickedTriggerButton = this.$refs.selectButton.contains(e.target);

                if (!clickedInsidePopover && !clickedTriggerButton) {
                    this.hidePopover();
                }
            });

            this.$watch('selected', () => {
                this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
            })

            await this.handleSearch(); 
        },

        togglePopover() {
            this.popover ? this.hidePopover() : this.showPopover();
        },
        showPopover() {
            if (!this.popover) {
                this.$refs.popoverContent.style.display = 'block';

                this.popover = new bootstrap.Popover(this.$refs.selectButton, {
                    content: () => this.$refs.popoverContent,
                    html: true,
                    trigger: 'manual',
                    placement: 'auto',
                    container: this.$refs.selectButton.closest('[x-data]') || 'body',
                    customClass: 'select-popover',
                    sanitize: false
                });

                this.$refs.selectButton.addEventListener('hidden.bs.popover', () => {
                    if (this.popover) {
                        this.popover.dispose(); 
                        this.popover = null;
                        this.$refs.popoverContent.style.display = 'none';
                        this.$refs.popoverContainer.appendChild(this.$refs.popoverContent);
                    }
                }, { once: true });
            }

            this.popover.show();
        },
        hidePopover() {
            if (this.popover) this.popover.hide();
        },

        setSelected(item) {
            this.search = null;
            this.selected = item[this.itemKey];
            this.$dispatch("item-selected", item);
            this.hidePopover();
        },
        
        async handleSearch() {
            let newParams = params;
            if (this.search) {
                newParams = { ...params, [searchParam]: this.search };
            }
            this.items = await fetchApi(newParams);
            
            if (this.popover) this.popover.update();
        },
    };
});