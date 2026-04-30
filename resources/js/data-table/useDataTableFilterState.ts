import type { Table } from '@tanstack/vue-table';
import { ref } from 'vue';
import type { Ref } from 'vue';
import { createDraftFilter } from '@/data-table/filter-registry';
import type {
    DraftFilter,
} from '@/data-table/types';

export interface StateOptions<TData> {
    table: Table<TData>
}

export interface StateController {
    draftFilters: Ref<DraftFilter[]>;
    addDraftFilter: (columnId: string) => void;
    removeDraftFilter: (columnId: string) => void;
    updateDraftFilter: (columnId: string, value: any) => void;
    hasDraftFilter: (columnId: string) => boolean;
}

export function useDataTableFilterState<TData>({
    table
}: StateOptions<TData>): StateController {
    const draftFilters = ref<DraftFilter[]>([]);

    const hasDraftFilter = (columnId: string): boolean =>
        draftFilters.value.some(filter => filter.id === columnId)

    const addDraftFilter = (columnId: string) => {
        const column = table.getColumn(columnId);

        if (!column || hasDraftFilter(columnId)) {
            return;
        }

        const columnDefMeta = column.columnDef.meta;
        const dataTableMeta = columnDefMeta?.dataTable;
        const label = dataTableMeta?.label ?? column.id;

        draftFilters.value.push(
            createDraftFilter({
                columnId,
                label,
                meta: dataTableMeta,
            }),
        );
    }

    const removeDraftFilter = (columnId: string): void => {
        draftFilters.value = draftFilters.value.filter(
            filter => filter.id !== columnId
        );

        table.getColumn(columnId)?.setFilterValue(undefined);
    }

    const updateDraftFilter = (
        columnId: string,
        value: DraftFilter['draftValue']
    ): void => {
        const filter = draftFilters.value.find(filter => filter.id === columnId);

        if (!filter) {
            return
        }

        filter.draftValue = value;
    }

    return {
        draftFilters,
        addDraftFilter,
        removeDraftFilter,
        updateDraftFilter,
        hasDraftFilter,
    }
}
