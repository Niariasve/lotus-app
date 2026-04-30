<script setup lang='ts' generic='TData'>
    import { CheckSquare, ListFilter } from 'lucide-vue-next';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectLabel,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';
    import { useSelectFilterPopover } from '@/data-table/filter-editors/select-filter/useSelectFilterPopover';

    interface DataTableFilterSelectPopoverProps {
        columnId: string;
    }

    const props = defineProps<DataTableFilterSelectPopoverProps>();

    const {
        allowedOperators,
        isMultiSelect,
        operatorRef,
        options,
        selectedValue,
        selectedValues,
    } = useSelectFilterPopover<TData>(props.columnId);
</script>

<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row items-center gap-4">
            <ListFilter class="size-5" />
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

        <div v-if="isMultiSelect" class="flex flex-row items-center gap-4">
            <CheckSquare class="size-5" />
            <Select v-model="selectedValues" multiple>
                <SelectTrigger class="flex-1">
                    <SelectValue placeholder="Select one or more" />
                </SelectTrigger>
                <SelectContent>
                    <SelectLabel>Options</SelectLabel>
                    <SelectItem
                        v-for="option in options"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>

        <div v-else class="flex flex-row items-center gap-4">
            <CheckSquare class="size-5" />
            <Select v-model="selectedValue">
                <SelectTrigger class="flex-1">
                    <SelectValue placeholder="Select an option" />
                </SelectTrigger>
                <SelectContent>
                    <SelectLabel>Options</SelectLabel>
                    <SelectItem
                        v-for="option in options"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
    </div>
</template>
