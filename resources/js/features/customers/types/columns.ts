import { type ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableColumnHeader from "@/components/ui/data-table/DataTableColumnHeader.vue";
import TableDropdown from "../components/CustomerDataTableDropdown.vue";
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
        id: 'primary_contact_platform',
        header: ({ column }) => {
            return h(DataTableColumnHeader<Customer>, {
                column: column,
                title: 'Contact Platform',
            })
        },
        accessorFn: (customer) => {
            if (!customer.primary_contact_platform) return null

            return `${customer.primary_contact_platform.contact_platform.name} ${customer.primary_contact_platform.contact_identifier}`
        },
        cell: ({ row }) => {
            const customer = row.original;
            if (customer.primary_contact_platform) {
                return h('div', [
                    h('span', { class: 'font-semibold mr-1' }, customer.primary_contact_platform.contact_platform.name),
                    h('span', customer.primary_contact_platform.contact_identifier),
                ]);
            }
        }
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
            else return h("span", { class: 'font-bold ' }, "N/A")
        }
    },
    {
        accessorKey: 'city',
        header: 'City',
        cell: ({ row }) => {
            if (row.original.city) return row.original.city
            else return h("span", { class: 'font-bold ' }, "N/A")
        }
    },
    {
        accessorKey: 'phone',
        header: 'Phone',
        cell: ({ row }) => {
            if (row.original.phone) return row.original.phone
            else return h("span", { class: 'font-bold ' }, "N/A")
        }
    },
    {
        id: 'accions',
        header: () => h('div', { class: 'text-center' }, 'Actions'),
        enableHiding: false,
        cell: ({ row }) => {
            const customer = row.original;
            return h('div', { class: 'relative flex justify-center' }, h(TableDropdown, { customer }))
        }
    }
]