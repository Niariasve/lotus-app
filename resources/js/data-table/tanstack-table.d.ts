import '@tanstack/vue-table';
import type { DataTableColumnMeta } from '@/data-table/types';

declare module '@tanstack/vue-table' {
    interface ColumnMeta<TData extends RowData, TValue> {
        dataTable: DataTableColumnMeta['dataTable'];
    }
}
