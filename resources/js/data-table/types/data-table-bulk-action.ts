export type DataTableBulkAction<TData> = {
    id: string,
    label: string,
    onClick: (rows: TData[]) => void
    disabled?: (rows: TData[]) => boolean
    hidden?: (rows: TData[]) => boolean
}