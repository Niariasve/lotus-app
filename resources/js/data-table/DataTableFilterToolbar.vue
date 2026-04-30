<script setup lang='ts' generic='TData'>
    import { CaseSensitive, ListFilter, X } from 'lucide-vue-next';
    import { computed } from 'vue';
    import { Button } from '@/components/ui/button';
    import { ButtonGroup } from '@/components/ui/button-group';
    import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
    import DataTableFilterPopover from '@/data-table/DataTableFilterPopover.vue';
    import type { DraftFilter } from '@/data-table/types';
    import { useDataTableFilters } from '@/data-table/useDataTableFilters';

    const filters = useDataTableFilters<TData>();

    const draftFilters = computed(() => filters.filterState.draftFilters.value);

    const getDraftSummaryValue = (filter: DraftFilter): string => {
        if (filter.type === 'text') {
            return filter.draftValue.value;
        }

        if ('value' in filter.draftValue) {
            return filter.draftValue.value;
        }

        return filter.draftValue.values.join(', ');
    };
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <ButtonGroup>
            <ButtonGroup v-for="filter in draftFilters" :key="filter.id">
                <Popover>
                    <PopoverTrigger as-child>
                        <Button variant="secondary">
                            <CaseSensitive v-if="filter.type === 'text'" />
                            <ListFilter v-else />
                            {{ filter.label }}
                            <span>
                                {{ filter.draftValue.operator }}
                            </span>
                            <span v-if="getDraftSummaryValue(filter)">
                                {{ getDraftSummaryValue(filter) }}
                            </span>
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent align="start">
                        <DataTableFilterPopover :draft-filter="filter" />
                    </PopoverContent>
                </Popover>
                <Button variant="secondary" @click="filters.filterState.removeDraftFilter(filter.id)">
                    <X />
                </Button>
            </ButtonGroup>
        </ButtonGroup>
    </div>
</template>
