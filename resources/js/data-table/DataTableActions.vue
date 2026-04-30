<script setup lang='ts' generic="TData">
    import { Table } from '@tanstack/vue-table';
    import { Button } from '@/components/ui/button';
    import { ButtonGroup } from '@/components/ui/button-group'
    import DataTableColumnToggle from '@/data-table/DataTableColumnToggle.vue';
    import DataTableFilterPicker from '@/data-table/DataTableFilterPicker.vue';
    import DataTableSearch from '@/data-table/DataTableSearch.vue';
    import type { DataTablePrimaryAction } from '@/data-table/primary-action';
    import type { DataTableBulkAction } from '@/data-table/types';
    import DataTableBulkOperations from './DataTableBulkOperations.vue';

    interface DataTableActionsProps {
        table: Table<TData>,
        primaryAction?: DataTablePrimaryAction,
        bulkActions?: DataTableBulkAction<TData>[],
    }

    withDefaults(defineProps<DataTableActionsProps>(), {
        primaryAction: () => ({
            label: 'New',
        }),
    });
</script>

<template>
    <div class="flex flex-row items-center justify-between">
        <DataTableSearch :table="table" />

        <ButtonGroup>
            <ButtonGroup>
                <DataTableFilterPicker :table="table" />
                <DataTableBulkOperations :table="table" :bulk-actions="bulkActions" />
                <DataTableColumnToggle :table="table" />
            </ButtonGroup>

            <ButtonGroup>
                <Button class="px-6" type="button" variant="outline" @click="primaryAction?.onClick?.()">
                    {{ primaryAction?.label ?? 'New' }}
                </Button>
            </ButtonGroup>
        </ButtonGroup>
    </div>
</template>
