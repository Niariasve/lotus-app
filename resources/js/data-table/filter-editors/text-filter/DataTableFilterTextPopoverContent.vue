<script setup lang='ts' generic='TData'>
    import { Funnel, Search } from 'lucide-vue-next';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectLabel,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';
    import { Input } from '@/components/ui/input';
    import { useTextFilterPopover } from '@/data-table/filter-editors/text-filter/useTextFilterPopover';

    interface DataTableFilterTextPopoverProps {
        columnId: string;
    }

    const props = defineProps<DataTableFilterTextPopoverProps>();

    const { allowedOperators, isValueRequired, operatorRef, search } =
        useTextFilterPopover<TData>(props.columnId);
</script>

<template>
    <div class="flex flex-col gap-2">
        <div class="flex flex-row gap-4 items-center">
            <Funnel class="size-5" />
            <Select v-model="operatorRef">
                <SelectTrigger class="flex-1">
                    <SelectValue class="capitalize" />
                </SelectTrigger>
                <SelectContent>
                    <SelectLabel>Operators</SelectLabel>
                    <SelectItem
                        v-for="(operator, index) in allowedOperators"
                        :key="index"
                        :value="operator.id"
                        class="capitalize"
                    >
                        {{ operator.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
        <div v-if="isValueRequired" class="flex flex-row gap-4 items-center">
            <Search class="size-5" />
            <Input v-model="search" class="flex-1" />
        </div>
    </div>
</template>
