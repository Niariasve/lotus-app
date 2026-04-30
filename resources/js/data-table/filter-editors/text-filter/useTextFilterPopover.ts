import { computed, ref, watch } from 'vue';
import { resolveOperators } from '@/data-table/operators';
import type {
    DraftFilter,
    TextDraftValue,
    TextFilterOperator,
    TextOperator,
} from '@/data-table/types';
import {
    type FilterRegistryItem,
    getFilterRegistryItem,
} from '@/data-table/filter-registry';
import { useDataTableFilters } from '@/data-table/useDataTableFilters';

export const useTextFilterPopover = <TData>(columnId: string) => {
    const filters = useDataTableFilters<TData>();

    const draftFilter = computed(() => {
        const filter = filters.filterState.draftFilters.value.find(
            item => item.id === columnId,
        );

        return filter?.type === 'text' ? filter : undefined;
    });

    const column = computed(() => filters.table.getColumn(columnId));

    const meta = computed(() => {
        const dataTableMeta = column.value?.columnDef.meta?.dataTable;

        if (dataTableMeta?.type !== 'text') {
            return undefined;
        }

        return dataTableMeta;
    });

    const registryItem = getFilterRegistryItem('text') as FilterRegistryItem<
        TextOperator,
        TextDraftValue
    >;

    const allowedOperators = computed(() =>
        resolveOperators({
            baseOperators: registryItem.operators,
            operators: meta.value?.operators,
            excludedOperators: meta.value?.excludedOperators,
        }),
    );

    const search = ref(draftFilter.value?.draftValue.value ?? '');

    const operatorRef = ref<TextFilterOperator>(
        draftFilter.value?.draftValue.operator ?? allowedOperators.value[0].id,
    );

    const isValueRequired = computed(
        () =>
            operatorRef.value !== 'is_empty' &&
            operatorRef.value !== 'is_not_empty',
    );

    watch([search, operatorRef], ([searchValue, operatorValue]) => {
        const draftValue: TextDraftValue = {
            operator: operatorValue,
            value: searchValue,
        };

        const filterValue = registryItem.toAppliedFilterValue(draftValue);

        filters.filterState.updateDraftFilter(columnId, draftValue);
        filters.setColumnFilter(columnId, filterValue);
    });

    return {
        allowedOperators,
        isValueRequired,
        operatorRef,
        search,
    };
};
