<script setup lang="ts" generic="TData">
    import type { ColumnDef } from '@tanstack/vue-table';
    import { FlexRender } from '@tanstack/vue-table';
    import { FolderOpen } from 'lucide-vue-next';
    import { provide, toRef } from 'vue';
    import {
        Empty,
        EmptyDescription,
        EmptyHeader,
        EmptyMedia,
        EmptyTitle,
    } from '@/components/ui/empty'
    import {
        Table,
        TableBody,
        TableCell,
        TableFooter,
        TableHead,
        TableHeader,
        TableRow,
    } from '@/components/ui/table'
    import DataTableActions from '@/data-table/DataTableActions.vue';
    import DataTableFilterToolbar from '@/data-table/DataTableFilterToolbar.vue';
    import DataTablePagination from '@/data-table/DataTablePagination.vue';
    import type { DataTablePrimaryAction } from '@/data-table/primary-action';
    import type { DataTableBulkAction, DataTableRowSelectionConfig } from '@/data-table/types';
    import { useDataTable } from '@/data-table/useDataTable';
    import { dataTableFiltersKey } from '@/data-table/useDataTableFilters';

    const props = withDefaults(defineProps<{
        columns: ColumnDef<TData, any>[],
        data: TData[],
        primaryAction?: DataTablePrimaryAction,
        showFooter?: boolean,
        rowSelection?: boolean | DataTableRowSelectionConfig<TData>,
        bulkActions?: DataTableBulkAction<TData>[],
    }>(), {
        showFooter: true,
        primaryAction: () => ({
            label: 'New',
        }),
    });

    const dataTable = useDataTable({
        data: toRef(props, 'data'),
        columns: toRef(props, 'columns'),
    });

    provide(dataTableFiltersKey, dataTable);
</script>

<template>
    <div class="flex flex-col gap-2">
        <DataTableActions
            :table="dataTable.table"
            :primary-action="primaryAction"
            :bulk-actions="bulkActions"
        />
        <div>
            <DataTableFilterToolbar />
        </div>
        <div class="border rounded-md">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in dataTable.table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id" :colspan="header.colSpan">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="dataTable.table.getRowModel().rows?.length">
                        <TableRow v-for="row in dataTable.table.getRowModel().rows" :key="row.id"
                            :data-state="row.getIsSelected() ? 'selected' : undefined">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableCell :colspan="columns.length">
                            <Empty>
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <FolderOpen />
                                    </EmptyMedia>
                                    <EmptyTitle>No Records Yet</EmptyTitle>
                                    <EmptyDescription>
                                        You haven't created any records yet. Get started by creating a record.
                                    </EmptyDescription>
                                </EmptyHeader>
                            </Empty>
                        </TableCell>
                    </template>
                </TableBody>
                <TableFooter v-if="showFooter">
                    <TableRow v-for="footerGroup in dataTable.table.getFooterGroups()" :key="footerGroup.id">
                        <TableHead v-for="header in footerGroup.headers" :key="header.id" :colspan="header.colSpan">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.footer"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableFooter>
            </Table>
        </div>
        <div class="flex items-center justify-center md:justify-end py-4 space-x-2">
            <DataTablePagination :table="dataTable.table" />
        </div>
    </div>
</template>
