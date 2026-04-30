import { Table } from '@tanstack/vue-table';
import { Ref, ref, watch } from 'vue';

interface UseDataTableSearchOptions<TData> {
    table: Table<TData>;
}

interface UseDataTableSearchReturn<TData> {
    search: Ref<string, string>,
    filterBy: Ref<string, string>,
    showClearSearch: Ref<boolean, boolean>,
    clearSearch: () => void,
    resetColumnFilters: () => void,
}

export function useDataTableSearch<TData>({ 
    table 
}: UseDataTableSearchOptions<TData>): UseDataTableSearchReturn<TData> {
    const search = ref<string>('');
    const filterBy = ref<string>('');
    const showClearSearch = ref<boolean>(false);

    const clearSearch = () => {
        search.value = '';
    }

    const resetColumnFilters = () => {
        table.resetColumnFilters();
        filterBy.value = '';
    }

    watch([search, filterBy], ([searchValue, filterByValue]) => {
        if (filterByValue.length > 0) {
            table.getColumn(filterByValue)?.setFilterValue(searchValue);
        } else {
            table.setGlobalFilter(searchValue);
        }
        
        showClearSearch.value = searchValue.length > 0
    });

    return {
        search,
        filterBy,
        showClearSearch,
        clearSearch,
        resetColumnFilters,
    }
}
