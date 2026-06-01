<?php
$xData ??= 'crudTable';

$this->pushJs('components/crudTable.js');
?>

<div
    x-data="<?= $xData ?>"
    x-ref="table"
    @form-success.window="handleFormSuccess($event.detail)"
    class="CrudTable"></div>

<style>
    .CrudTable {
        overflow: auto;

        .crud-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;

            button {
                width: 50px;
            }
        }

        .gridjs-head {
            display: flex;
            justify-content: space-between;
            flex-direction: row-reverse;
            align-items: stretch;
            min-height: 52px;

            &::after {
                content: unset;
            }

            >* {
                margin: 0 5px;
            }

            button {
                background-color: var(--bs-btn-bg);
                color: var(--bs-btn-color);
                padding: var(--bs-btn-padding-y) 1rem;

                &:hover {
                    background-color: var(--bs-btn-hover-bg);
                }
            }
        }

        .gridjs-search {
            width: 100%;
        }

        .gridjs-search-input {
            /** Estilos para que la busqueda se parezca a un input de Bootstrap */

            /* Box model */
            display: block;
            width: 100%;
            height: 100%;
            padding: var(--bs-input-padding-y, 0.375rem) var(--bs-input-padding-x, 0.75rem);
            font-size: var(--bs-input-font-size, 1rem);
            font-family: inherit;
            /* match the rest of the page */
            line-height: 1.5;

            /* Colours */
            color: var(--bs-body-color, #212529);
            background-color: var(--bs-body-bg, #fff);

            /* Border */
            border: var(--bs-border-width, 1px) solid var(--bs-border-color, #dee2e6);
            border-radius: var(--bs-border-radius, 0.375rem);

            /* Placeholder */
            &::placeholder {
                color: var(--bs-input-placeholder-color, #6c757d);
                opacity: 1;
            }

            /* Transitions (optional, but smooths focus) */
            transition: border-color 0.15s ease-in-out,
            box-shadow 0.15s ease-in-out;

            /* Focus state – using Bootstrap’s focus ring variables */
            &:focus {
                color: var(--bs-body-color, #212529);
                background-color: var(--bs-body-bg, #fff);
                border-color: var(--bs-focus-ring-color, rgba(13, 110, 253, 0.25));
                /* fallback tint */
                outline: 0;
                box-shadow: 0 0 0 var(--bs-focus-ring-width, 0.25rem) var(--bs-focus-ring-color, rgba(13, 110, 253, 0.25));
            }
        }

        .gridjs-wrapper,
        .gridjs-footer {
            box-shadow: unset;
        }
    }
</style>