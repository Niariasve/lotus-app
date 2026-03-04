import { type ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableColumnHeader from "@/components/ui/data-table/DataTableColumnHeader.vue";
import { type Product } from "./products";

export const columns: ColumnDef<Product>[] = [
    {
        accessorKey: 'sku',
        enableSorting: false,
        header: ({ column }) => {
            return h(DataTableColumnHeader<Product>, {
                column: column,
                title: 'SKU',
            })
        }
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Product>, {
                column: column,
                title: 'Name',
            })
        }
    },
    {
        accessorKey: 'brand',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Product>, {
                column: column,
                title: 'Brand',
            })
        }
    },
    {
        accessorKey: 'line',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Product>, {
                column: column,
                title: 'Line',
            })
        }
    },
    {
        accessorKey: 'height',
        header: 'Height',
    },
    {
        accessorKey: 'weight_est',
        header: 'Weight Est.',
    },
    {
        accessorKey: 'weight_real',
        header: 'Real Weight',
    },
    {
        accessorKey: 'release_date',
        header: 'Release Date',
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'text-center' }, 'Actions'),
        enableHiding: false,
    }
]