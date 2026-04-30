import type { InjectionKey } from 'vue';
import { inject } from 'vue';
import type { DataTableFiltersController } from '@/data-table/useDataTable';

export const dataTableFiltersKey = 
    Symbol('data-table-filters') as InjectionKey<DataTableFiltersController<any>>;

export function useDataTableFilters<TData>(): DataTableFiltersController<TData> {
    const filters = inject(dataTableFiltersKey);

    if (!filters) {
        throw new Error('useDataTableFilters must be used inside a DataTable component.');
    }

    return filters as DataTableFiltersController<TData>;
}
