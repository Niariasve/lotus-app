import { type ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableColumnHeader from "@/components/ui/data-table/DataTableColumnHeader.vue";
import { type Customer } from "./customers";

export const columns: ColumnDef<Customer>[] = [
    {
        accessorKey: 'id',
        header: 'Id',
    },
    {
        accessorKey: 'full_name',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Customer>, {
                column: column,
                title: 'Full Name',
            })
        },
        cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('full_name'))
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Customer>, {
                column: column,
                title: 'Email',
            }) 
        },
        cell: ({ row }) => {
            if (row.original.email) return row.original.email
            else return h("span", { class: 'font-bold '}, "N/A")
        }
    }
]