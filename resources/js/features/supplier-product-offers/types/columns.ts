import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import DataTableColumnHeader from '@/components/ui/data-table/DataTableColumnHeader.vue';
import { formatDateTime } from '@/lib/utils';
import TableDropdown from '../components/SupplierProductOfferDataTableDropdown.vue';
import { type SupplierProductOffer } from './supplierProductOffers';

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
        accessorKey: 'retail_price',
        header: 'Retail Price',
        cell: ({ row }) => {
            const supplier = row.original.supplier;
            return supplier ? `${supplier.currency} ${row.original.retail_price}` : row.original.retail_price;
        }
    },
    {
        accessorKey: 'priority',
        header: 'Priority',
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
