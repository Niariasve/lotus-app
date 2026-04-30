<script setup lang='ts' generic="TData">
    import { Table } from '@tanstack/vue-table';
    import { X } from 'lucide-vue-next';
    import {
        InputGroup,
        InputGroupAddon,
        InputGroupButton,
        InputGroupInput,
    } from '@/components/ui/input-group'
    import { useDataTableSearch } from '@/data-table/useDataTableSearch';

    interface DataTableSearchProps {
        table: Table<TData>,
    }

    const props = defineProps<DataTableSearchProps>();

    const {
        search,
        showClearSearch,
        clearSearch
    } = useDataTableSearch({ table: props.table });
</script>

<template>
    <div class="flex items-center gap-3">
        <InputGroup class="w-56">
            <InputGroupInput placeholder="Type to search..." v-model="search" />

            <InputGroupAddon align="inline-end">
                <InputGroupButton v-show="showClearSearch" @click="clearSearch" size="icon-xs">
                    <X />
                </InputGroupButton>
            </InputGroupAddon>
        </InputGroup>
    </div>
</template>
