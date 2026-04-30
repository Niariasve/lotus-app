<script setup lang='ts' generic='TData'>
    import DataTableFilterSelectPopoverContent from '@/data-table/filter-editors/select-filter/DataTableFilterSelectPopoverContent.vue';
    import DataTableFilterTextPopoverContent from '@/data-table/filter-editors/text-filter/DataTableFilterTextPopoverContent.vue';
    import type { DraftFilter } from '@/data-table/types';

    interface DataTableFilterPopoverProps {
        draftFilter: DraftFilter,
    }

    defineProps<DataTableFilterPopoverProps>();
</script>

<template>
    <div v-if="draftFilter.type === 'text'">
        <DataTableFilterTextPopoverContent :columnId="draftFilter.id" />
    </div>
    <div v-else-if="draftFilter.type === 'select' || draftFilter.type === 'status'">
        <DataTableFilterSelectPopoverContent :columnId="draftFilter.id" />
    </div>
    <div v-else>
        Filter type not supported yet.
    </div>
</template>

<style></style>
