<script setup lang='ts' generic="TData">
    import type { Column } from '@tanstack/vue-table';
    import { ArrowDown, ChevronDown, ChevronsUpDown, ChevronUp, ArrowUp, EyeClosed, X } from 'lucide-vue-next'
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuSeparator,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu'
    import { cn } from '@/lib/utils';

    interface DataTableColumnHeaderProps<TData> {
        column: Column<TData, unknown>,
        title: string,
    }

    defineProps<DataTableColumnHeaderProps<TData>>();
</script>

<script lang='ts'>
    export default {
        inheritAttrs: false,
    }
</script>

<template>
    <div v-if="column.getCanSort()" :class="cn('flex items-center space-x-2', $attrs.class ?? '')">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="sm" class="-ml-3 h-8 data-[state=open]:bg-accent">
                    <span>{{ title }}</span>
                    <ChevronDown v-if="column.getIsSorted() === 'desc'" class="w-4 h-4 ml-2" />
                    <ChevronUp v-else-if="column.getIsSorted() === 'asc'" class="w-4 h-4 ml-2" />
                    <ChevronsUpDown v-else class="w-4 h-4 ml-2" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start">
                <DropdownMenuItem @click="column.toggleSorting(false)">
                    <ArrowUp class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                    Asc
                </DropdownMenuItem>
                <DropdownMenuItem @click="column.toggleSorting(true)">
                    <ArrowDown class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                    Desc
                </DropdownMenuItem>
                <DropdownMenuItem v-if="column.getIsSorted()" @click="column.clearSorting()">
                    <X class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                    Clear sort
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem @click="column.toggleVisibility(false)">
                    <EyeClosed class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                    Hide
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>

    <div v-else :class="$attrs.class">
        {{ title }}
    </div>
</template>

<style></style>
