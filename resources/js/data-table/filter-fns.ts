import type { FilterFn, Row } from '@tanstack/vue-table';
import type { SelectFilterValue, TextFilterValue } from '@/data-table/types';

const normalizeText = (value: unknown): string => {
    if (value == null) {
        return '';
    }

    const textValue = String(value).trim().toLowerCase();

    return textValue;
};

export const dataTableTextFilterFn: FilterFn<any> = (
    row: Row<any>,
    columnId: string,
    filterValue: TextFilterValue = { value: '', operator: 'contains' },
) => {
    const rawValue = row.getValue(columnId);
    const normalizedValue = normalizeText(rawValue);
    const typedFilterValue = filterValue as TextFilterValue;

    if (!('value' in typedFilterValue)) {
        return typedFilterValue.operator === 'is_empty'
            ? normalizedValue === ''
            : normalizedValue !== '';
    }

    const normalizedFilterValue = normalizeText(typedFilterValue.value);

    switch (typedFilterValue.operator) {
        case 'is':
            return normalizedFilterValue === normalizedValue;

        case 'is_not':
            return normalizedFilterValue !== normalizedValue;

        case 'contains':
            return normalizedValue.includes(normalizedFilterValue);

        case 'does_not_contain':
            return !normalizedValue.includes(normalizedFilterValue);

        case 'starts_with':
            return normalizedValue.startsWith(normalizedFilterValue);

        case 'ends_with':
            return normalizedValue.endsWith(normalizedFilterValue);

        default:
            return true;
    }
};

export const dataTableSelectFilterFn: FilterFn<any> = (
    row: Row<any>,
    columnId: string,
    filterValue: SelectFilterValue = { operator: 'equals', value: '' },
) => {
    const rawValue = row.getValue(columnId);
    const normalizedValue = normalizeText(rawValue);

    if ('values' in filterValue) {
        const normalizedValues = filterValue.values.map((value) =>
            normalizeText(value),
        );

        return filterValue.operator === 'is_in'
            ? normalizedValues.includes(normalizedValue)
            : !normalizedValues.includes(normalizedValue);
    }

    const normalizedFilterValue = normalizeText(filterValue.value);

    return filterValue.operator === 'equals'
        ? normalizedValue === normalizedFilterValue
        : normalizedValue !== normalizedFilterValue;
};

export const dataTableFilterFns = {
    dataTableText: dataTableTextFilterFn,
    dataTableSelect: dataTableSelectFilterFn,
};
