import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import DataTableColumnHeader from '@/components/ui/data-table/DataTableColumnHeader.vue';
import TableDropdown from '../components/SupplierProductOfferDataTableDropdown.vue';
import { type SupplierProductOffer } from './supplierProductOffers';

const toNumber = (value: number | string): number => Number(value ?? 0);
const formatCurrency = (currency: string, value: number | string): string =>
    `${currency} ${toNumber(value).toFixed(2)}`;

const formatDateTime = (value: string | null): string => {
    if (!value) return 'Not checked';

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return 'Not checked';

    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

export const columns: ColumnDef<SupplierProductOffer>[] = [
    {
        accessorKey: 'id',
        header: 'Id',
    },
    {
        id: 'supplier',
        accessorFn: (supplierProductOffer) =>
            supplierProductOffer.supplier?.name ?? `Supplier #${supplierProductOffer.supplier_id}`,
        header: ({ column }) => {
            return h(DataTableColumnHeader<SupplierProductOffer>, {
                column,
                title: 'Supplier',
            });
        },
        cell: ({ row }) => row.getValue('supplier'),
    },
    {
        id: 'product',
        accessorFn: (supplierProductOffer) => {
            if (!supplierProductOffer.product) return `Product #${supplierProductOffer.product_id}`;

            if (supplierProductOffer.product.sku) {
                return `${supplierProductOffer.product.name} (${supplierProductOffer.product.sku})`;
            }

            return supplierProductOffer.product.name;
        },
        header: ({ column }) => {
            return h(DataTableColumnHeader<SupplierProductOffer>, {
                column,
                title: 'Product',
            });
        },
        cell: ({ row }) => row.getValue('product'),
    },
    {
        accessorKey: 'base_cost',
        header: ({ column }) => {
            return h(DataTableColumnHeader<SupplierProductOffer>, {
                column,
                title: 'Base Cost',
            });
        },
        cell: ({ row }) => formatCurrency(row.original.currency, row.original.base_cost),
    },
    {
        id: 'estimated_total',
        accessorFn: (supplierProductOffer) =>
            toNumber(supplierProductOffer.base_cost) +
            toNumber(supplierProductOffer.estimated_tax) +
            toNumber(supplierProductOffer.estimated_shipping) +
            toNumber(supplierProductOffer.other_fees),
        header: ({ column }) => {
            return h(DataTableColumnHeader<SupplierProductOffer>, {
                column,
                title: 'Estimated Total',
            });
        },
        cell: ({ row }) => formatCurrency(row.original.currency, row.getValue('estimated_total') as number),
    },
    {
        accessorKey: 'currency',
        header: 'Currency',
    },
    {
        id: 'availability',
        accessorFn: (supplierProductOffer) => (supplierProductOffer.is_available ? 'Available' : 'Unavailable'),
        header: ({ column }) => {
            return h(DataTableColumnHeader<SupplierProductOffer>, {
                column,
                title: 'Availability',
            });
        },
        cell: ({ row }) => {
            if (row.original.is_available) {
                return h('span', { class: 'font-semibold text-emerald-600' }, 'Available');
            }

            return h('span', { class: 'font-semibold text-destructive' }, 'Unavailable');
        },
    },
    {
        accessorKey: 'last_checked_at',
        header: 'Last Checked',
        cell: ({ row }) => formatDateTime(row.original.last_checked_at),
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'text-center' }, 'Actions'),
        enableHiding: false,
        cell: ({ row }) => {
            const supplierProductOffer = row.original;
            return h(
                'div',
                { class: 'relative flex justify-center' },
                h(TableDropdown, { supplierProductOffer }),
            );
        },
    },
];
