import { computed, ref, watch } from 'vue';
import { resolveOperators } from '@/data-table/operators';
import type {
    SelectDraftValue,
    SelectOperator,
} from '@/data-table/types';
import {
    type FilterRegistryItem,
    getFilterRegistryItem,
} from '@/data-table/filter-registry';
import { useDataTableFilters } from '@/data-table/useDataTableFilters';

export const useSelectFilterPopover = <TData>(columnId: string) => {
    const filters = useDataTableFilters<TData>();

    const draftFilter = computed(() => {
        const filter = filters.filterState.draftFilters.value.find(
            item => item.id === columnId,
        );

        return filter?.type === 'select' || filter?.type === 'status'
            ? filter
            : undefined;
    });

    const column = computed(() => filters.table.getColumn(columnId));

    const meta = computed(() => {
        const dataTableMeta = column.value?.columnDef.meta?.dataTable;

        if (
            dataTableMeta?.type !== 'select' &&
            dataTableMeta?.type !== 'status'
        ) {
            return undefined;
        }

        return dataTableMeta;
    });

    const registryItem = getFilterRegistryItem('select') as FilterRegistryItem<
        SelectOperator,
        SelectDraftValue
    >;

    const allowedOperators = computed(() =>
        resolveOperators({
            baseOperators: registryItem.operators,
            operators: meta.value?.operators,
            excludedOperators: meta.value?.excludedOperators,
        }),
    );

    const options = computed(() => meta.value?.options ?? []);

    const operatorRef = ref<SelectOperator['id']>(
        draftFilter.value?.draftValue.operator ?? allowedOperators.value[0].id,
    );

    const selectedValue = ref(
        draftFilter.value?.draftValue && 'value' in draftFilter.value.draftValue
            ? draftFilter.value.draftValue.value
            : '',
    );

    const selectedValues = ref(
        draftFilter.value?.draftValue && 'values' in draftFilter.value.draftValue
            ? draftFilter.value.draftValue.values
            : [],
    );

    const isMultiSelect = computed(
        () =>
            operatorRef.value === 'is_in' || operatorRef.value === 'is_not_in',
    );

    watch(operatorRef, nextOperator => {
        if (nextOperator === 'is_in' || nextOperator === 'is_not_in') {
            selectedValues.value = selectedValue.value ? [selectedValue.value] : [];
            return;
        }

        selectedValue.value = selectedValues.value[0] ?? '';
    });

    watch([operatorRef, selectedValue, selectedValues], () => {
        const draftValue: SelectDraftValue = isMultiSelect.value
            ? {
                  operator:
                      operatorRef.value === 'is_in' ? 'is_in' : 'is_not_in',
                  values: selectedValues.value,
              }
            : {
                  operator:
                      operatorRef.value === 'equals'
                          ? 'equals'
                          : 'not_equals',
                  value: selectedValue.value,
              };

        const filterValue = registryItem.toAppliedFilterValue(draftValue);

        filters.filterState.updateDraftFilter(columnId, draftValue);
        filters.setColumnFilter(columnId, filterValue);
    });

    return {
        allowedOperators,
        isMultiSelect,
        operatorRef,
        options,
        selectedValue,
        selectedValues,
    };
};
