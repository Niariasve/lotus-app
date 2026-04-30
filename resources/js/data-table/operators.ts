import type { SelectOperator, TextOperator } from '@/data-table/types';

export const textOperators: TextOperator[] = [
    { id: 'contains', label: 'contains', requiresValue: true },
    { id: 'does_not_contain', label: 'does not contain', requiresValue: true },
    { id: 'starts_with', label: 'starts with', requiresValue: true },
    { id: 'ends_with', label: 'ends with', requiresValue: true },
    { id: 'is', label: 'is', requiresValue: true },
    { id: 'is_not', label: 'is not', requiresValue: true },
    { id: 'is_empty', label: 'is empty', requiresValue: false },
    { id: 'is_not_empty', label: 'is not empty', requiresValue: false },
];

export const selectOperators: SelectOperator[] = [
    { id: 'is_in', label: 'is in', requiresValue: true },
    { id: 'is_not_in', label: 'is not in', requiresValue: true },
    { id: 'equals', label: 'equals', requiresValue: true },
    { id: 'not_equals', label: 'not equals', requiresValue: true}
]

export const resolveOperators = <TOperator extends string, TOption extends { id: TOperator }>(options: {
    baseOperators: TOption[];
    operators?: TOperator[];
    excludedOperators?: TOperator[];
}): TOption[] => {
    const configuredOperators = options.operators;
    const excludedOperators = options.excludedOperators ?? [];

    const baseOperators = configuredOperators?.length
        ? options.baseOperators.filter(operatos =>
            configuredOperators.includes(operatos.id),
        )
        : options.baseOperators;

    return baseOperators.filter(operator =>
        !excludedOperators.includes(operator.id),
    )
}
