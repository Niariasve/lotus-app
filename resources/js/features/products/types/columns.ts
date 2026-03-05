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
        },
        cell: ({ row }) => {
            if (row.original.brand) return row.original.brand
            else return h('span', { class: 'font-bold' }, 'N/A')
        }
    },
    {
        accessorKey: 'line',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Product>, {
                column: column,
                title: 'Line',
            })
        },
        cell: ({ row }) => {
            if (row.original.line) return row.original.line
            else return h('span', { class: 'font-bold' }, 'N/A')
        }
    },
    {
        accessorKey: 'height',
        header: 'Height (cm)',
        cell: ({ row }) => {
            if (row.original.height) return row.original.height
            else return h('span', { class: 'font-bold text-xs' }, 'unknown')
        }
    },
    {
        accessorKey: 'weight_est',
        header: 'Weight Est. (g)',
        cell: ({ row }) => {
            if (row.original.weight_est) return row.original.weight_est
            else return h('span', { class: 'font-bold text-xs' }, 'unknown')
        }
    },
    {
        accessorKey: 'weight_real',
        header: 'Real Weight (g)',
        cell: ({ row }) => {
            if (row.original.weight_real) return row.original.weight_real
            else return h('span', { class: 'font-bold text-xs' }, 'unknown')
        }
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