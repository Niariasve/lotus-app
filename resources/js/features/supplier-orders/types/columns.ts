import { type ColumnDef } from '@tanstack/vue-table';
import { formatDate, formatDateTime } from '@/lib/utils';
import { type SupplierOrder } from './supplierOrders';

export const columns: ColumnDef<SupplierOrder>[] = [
    {
        accessorKey: 'order_number',
        header: 'Order Number',
    },
    {
        id: 'supplier',
        accessorFn: (supplierOrder) => {
            return supplierOrder.supplier?.name ?? `Supplier #${supplierOrder.supplier_id}`;
        },
        header: 'Supplier',
        cell: ({ row }) => row.getValue('supplier'),
    },
    {
        id: 'status',
        accessorFn: (supplierOrder) => {
            return supplierOrder.status?.name ?? '—';
        },
        header: 'Status',
        cell: ({ row }) => row.getValue('status'),
    },
    {
        accessorKey: 'tracking',
        header: 'Tracking',
        cell: ({ row }) => row.original.tracking ?? '—',
    },
    {
        accessorKey: 'ordered_at',
        header: 'Ordered At',
        cell: ({ row }) => formatDate(row.original.ordered_at),
    },
    {
        accessorKey: 'shipped_at',
        header: 'Shipped At',
        cell: ({ row }) => formatDate(row.original.shipped_at),
    },
    {
        accessorKey: 'arrived_at',
        header: 'Arrived At',
        cell: ({ row }) => formatDate(row.original.arrived_at),
    },
    {
        accessorKey: 'items_count',
        header: 'Items',
        cell: ({ row }) => row.original.items_count ?? 0,
    },
    {
        accessorKey: 'created_at',
        header: 'Created At',
        cell: ({ row }) => formatDateTime(row.original.created_at),
    },
];
