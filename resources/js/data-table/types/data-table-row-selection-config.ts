export type DataTableRowSelectionConfig<TData> = {
    getRowId?: (row: TData) => string
}