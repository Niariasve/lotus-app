import  type { ColumnDef, Table, ColumnFiltersState } from '@tanstack/vue-table';
import {
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import type { MaybeRefOrGetter, Ref } from 'vue';
import { ref, toValue } from 'vue';
import { valueUpdater } from '@/components/ui/table/utils';
import { dataTableFilterFns } from '@/data-table/filter-fns';
import type { StateController } from '@/data-table/useDataTableFilterState';
import {
    useDataTableFilterState,
} from '@/data-table/useDataTableFilterState';

export interface UseDataTableOptions<TData> {
    data: MaybeRefOrGetter<TData[]>;
    columns: MaybeRefOrGetter<ColumnDef<TData, any>[]>;
}

export interface DataTableFiltersController<TData> {
    table: Table<TData>;
    columnFilters: Ref<ColumnFiltersState>;
    setColumnFilter: (columnId: string, value: any) => void;
    clearColumnFilter: (columnId: string) => void;
    isColumnFiltered: (columnId: string) => boolean;
    filterState: StateController;
}

export function useDataTable<TData>({
    data,
    columns,
}: UseDataTableOptions<TData>): DataTableFiltersController<TData> {
    
    const columnFilters = ref<ColumnFiltersState>([]);

    const rowSelection = ref({});

    const table = useVueTable({
        get data() {
            return toValue(data);
        },
        get columns() {
            return toValue(columns);
        },
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        filterFns: dataTableFilterFns,
        globalFilterFn: 'includesString',
        state: {
            get columnFilters() {
                return columnFilters.value;
            },
            get rowSelection() {
                return rowSelection.value;
            }
        },
        onColumnFiltersChange: updaterOrValue =>
            valueUpdater(updaterOrValue, columnFilters),
        onRowSelectionChange: updaterOrValue =>
            valueUpdater(updaterOrValue, rowSelection),
    });

    const filterState = useDataTableFilterState<TData>({ table });

    const isColumnFiltered = (columnId: string): boolean =>
        columnFilters.value.some(filter => filter.id === columnId)

    const setColumnFilter = (
        columnId: string,
        value: any
    ): void => {
        table.getColumn(columnId)?.setFilterValue(value);
    }

    const clearColumnFilter = (columnId: string): void => {
        table.getColumn(columnId)?.setFilterValue(undefined);
    }

    return {
        table,
        filterState,
        columnFilters,
        setColumnFilter,
        clearColumnFilter,
        isColumnFiltered,
    };
}
