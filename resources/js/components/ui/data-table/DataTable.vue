<script setup lang='ts' generic="TData, TValue">
  import { computed, ref } from 'vue';
  import { ChevronDown, CirclePlus, X } from 'lucide-vue-next';
  import type { ColumnDef, SortingState, ColumnFiltersState } from '@tanstack/vue-table';
  import {
    FlexRender,
    getCoreRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    getFilteredRowModel,
    useVueTable
  } from '@tanstack/vue-table';
  import { valueUpdater } from '../table/utils';

  import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
  } from '@/components/ui/table';

  import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
  } from "@/components/ui/dropdown-menu";

  import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
  } from '@/components/ui/select'

  import { Input } from '../input';
  import { Button } from '../button';
  import DataTablePagination from './DataTablePagination.vue';

  const props = defineProps<{
    columns: ColumnDef<TData, TValue>[],
    data: TData[],
    filterableColumns?: { value: string, label: string }[],
  }>();

  const sorting = ref<SortingState>([]);
  const columnFilters = ref<ColumnFiltersState>([]);
  const filterBy = ref(
    props.filterableColumns && props.filterableColumns.length > 0
      ? props.filterableColumns[0].value
      : ''
  );

  const table = useVueTable({
    get data() { return props.data },
    get columns() { return props.columns },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
    getFilteredRowModel: getFilteredRowModel(),
    state: {
      get sorting() { return sorting.value },
      get columnFilters() { return columnFilters.value },
    },
  });

  function clearFilter() {
    if (filterBy.value) {
      table.getColumn(filterBy.value)?.setFilterValue("");
    }
  }
  
</script>

<template>
  <div class="flex flex-col md:flex-row justify-between items-center py-4 gap-4">
    <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-140">

      <Input placeholder="Search" :model-value="table.getColumn(filterBy)?.getFilterValue() as string"
        @update:model-value="table.getColumn(filterBy)?.setFilterValue($event)" />

      <div class="flex flex-row w-full gap-2">
        <Select v-if="filterableColumns && filterableColumns.length > 0" v-model="filterBy">
          <SelectTrigger class="w-full md:w-auto">
            <SelectValue placeholder="Select a filter" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectLabel>Filter By</SelectLabel>
              <SelectItem v-for="(col, index) in filterableColumns" :value="col.value" :key="index">
                {{ col.label }}
              </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
  
        <Button
          v-if="table.getColumn(filterBy)?.getFilterValue()"
          variant="ghost"
          class="flex items-center rounded-full text-foreground"
          @click="clearFilter"
        >
          <p>Reset</p>
          <X class="h-4 w-4" />
        </Button>
      </div>
    </div>

    <div class="flex flex-row items-center gap-4 w-full md:w-auto">
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline" class="flex justify-between cursor-pointer w-full">
            Columns
            <ChevronDown class="ml-1 h-4 w-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuCheckboxItem v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
            :key="column.id" class="capitalize" :model-value="column.getIsVisible()" @update:model-value="(value) => {
              column.toggleVisibility(!!value)
            }">
            {{ column.id }}
          </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </div>

  <div class="border rounded-md">
    <Table>
      <TableHeader>
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
          <TableHead v-for="header in headerGroup.headers" :key="header.id">
            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
              :props="header.getContext()" />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template v-if="table.getRowModel().rows?.length">
          <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
            :data-state="row.getIsSelected() ? 'selected' : undefined">
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </TableCell>
          </TableRow>
        </template>
        <template v-else>
          <TableRow>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              No results.
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>
  <div class="flex items-center justify-center md:justify-end py-4 space-x-2">
    <DataTablePagination :table="table" />
  </div>
</template>