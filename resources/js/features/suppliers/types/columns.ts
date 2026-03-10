import { type ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableColumnHeader from "@/components/ui/data-table/DataTableColumnHeader.vue";
import { trimDecimal } from "@/lib/utils";
import TableDropdown from "../components/SupplierDataTableDropdown.vue";
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
        accessorKey: 'tax_policy',
        header: 'Tax Policy',
        cell: ({ row }) => trimDecimal(row.original.tax_policy)

    },
    {
        accessorKey: 'estimated_shipping',
        header: 'Estimated Shipping Cost',
        cell: ({ row }) => trimDecimal(row.original.estimated_shipping)
    },
    {
        accessorKey: 'currency',
        header: 'Currency',
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'text-center' }, 'Actions'),
        cell: ({ row }) => {
            const supplier = row.original;
            return h('div', { class: 'relative flex justify-center' }, h(TableDropdown, { supplier }))
        }
    }
];