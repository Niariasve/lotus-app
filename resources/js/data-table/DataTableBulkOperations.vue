<script setup lang="ts" generic="TData">
    import type { Table } from '@tanstack/vue-table';
    import { List, Wrench } from 'lucide-vue-next';
    import { computed } from 'vue';
    import { Button } from '@/components/ui/button'
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuLabel,
        DropdownMenuSeparator,
        DropdownMenuTrigger,
        DropdownMenuItem,
    } from '@/components/ui/dropdown-menu'
    import {
        Tooltip,
        TooltipContent,
        TooltipProvider,
        TooltipTrigger,
    } from '@/components/ui/tooltip'
    import type { DataTableBulkAction } from '@/data-table/types';

    interface BulkOperationsProps {
        table: Table<TData>
        bulkActions?: DataTableBulkAction<TData>[]
    }

    const props = defineProps<BulkOperationsProps>();

    const selectedRows = computed<TData[]>(() =>
        props.table.getFilteredSelectedRowModel().rows.map((row) => row.original),
    );

    const visibleBulkActions = computed(() =>
        (props.bulkActions ?? []).filter(
            (action) => !action.hidden?.(selectedRows.value),
        ),
    );

    const handleBulkAction = (action: DataTableBulkAction<TData>): void => {
        action.onClick(selectedRows.value);
    };
</script>

<template>
    <TooltipProvider>
        <Tooltip>
            <DropdownMenu>
                <TooltipTrigger as-child>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost">
                            <Wrench class="w-4 h-4" />
                        </Button>
                    </DropdownMenuTrigger>
                </TooltipTrigger>
                <TooltipContent>
                    <p>Actions</p>
                </TooltipContent>
                <DropdownMenuContent align="end" v-on:close-auto-focus="((e: Event) => e.preventDefault())">
                    <DropdownMenuLabel>Exports</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    
                    <DropdownMenuItem>
                        <List class="size-4" />
                        CSV export
                    </DropdownMenuItem>
                    
                    <template v-if="visibleBulkActions.length">
                        <DropdownMenuSeparator />
                        <DropdownMenuLabel>Bulk Actions</DropdownMenuLabel>
                        <DropdownMenuSeparator />

                        <DropdownMenuItem
                            v-for="action in visibleBulkActions"
                            :key="action.id"
                            :disabled="action.disabled?.(selectedRows)"
                            @click="handleBulkAction(action)"
                        >
                            <List class="size-4" />
                            {{ action.label }}
                        </DropdownMenuItem>
                    </template>

                </DropdownMenuContent>
            </DropdownMenu>
        </Tooltip>
    </TooltipProvider>
</template>

<style></style>
