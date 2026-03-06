import { type ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableColumnHeader from "@/components/ui/data-table/DataTableColumnHeader.vue";
import { type Supplier } from "./suppliers";

export const columns: ColumnDef<Supplier>[] = [
    {
        accessorKey: 'id',
        header: 'Id'
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Supplier>, {
                column: column,
                title: 'Name',
            })
        }
    },
    {
        accessorKey: 'priority',
        header: 'Priority',
    },
    {
        accessorKey: 'tax_policy',
        header: 'Tax Policy',
    },
    {
        accessorKey: 'shipping_policy',
        header: 'Shipping Policy',
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'text-center' }, 'Actions'),
    }
];