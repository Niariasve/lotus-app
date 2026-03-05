import { type ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableColumnHeader from "@/components/ui/data-table/DataTableColumnHeader.vue";
import TableDropdown from "../components/ProductDataTableDropdown.vue";
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
        cell: ({ row }) => {
            const date = row.original.release_date;

            if (!date) return '-';

            const [year, month, day] = date.split('-');

            return h('span', `${day}/${month}/${year}`);
        }
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'text-center' }, 'Actions'),
        enableHiding: false,
        cell: ({ row }) => {
            const product = row.original;
            return h('div', { class: 'relative flex justify-center' }, h(TableDropdown, { product }))
        }
    }
]