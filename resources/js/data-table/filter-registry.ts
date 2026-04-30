import { resolveOperators, selectOperators, textOperators } from '@/data-table/operators';
import type {
    DataTableColumnType,
    DataTableColumnMeta,
    DraftFilter,
    SelectDraftValue,
    SelectOperator,
    TextDraftValue,
    TextOperator,
} from '@/data-table/types';

export type FilterRegistryItem<TOperatorOption, TDraftValue> = {
    operators: TOperatorOption[];
    getDefaultDraftValue: () => TDraftValue;
    getInitialDraftValue: (config?: {
        operators?: TOperatorOption extends { id: infer TOperator }
            ? TOperator[]
            : never;
        excludedOperators?: TOperatorOption extends { id: infer TOperator }
            ? TOperator[]
            : never;
    }) => TDraftValue;
    toAppliedFilterValue: (draftValue: TDraftValue) => unknown;
};

export type TextFilterRegistryItem = FilterRegistryItem<
    TextOperator,
    TextDraftValue
>;

export type SelectFilterRegistryItem = FilterRegistryItem<
    SelectOperator,
    SelectDraftValue
>;

type TextMeta = Extract<DataTableColumnMeta['dataTable'], { type: 'text' }>;
type SelectMeta = Extract<
    DataTableColumnMeta['dataTable'],
    { type: 'select' | 'status' }
>;

const selectFilterRegistryItem: SelectFilterRegistryItem = {
    operators: selectOperators,
    getDefaultDraftValue: () => ({ operator: 'equals', value: '' }),
    getInitialDraftValue: (config) => {
        const defaultDraftValue =
            selectFilterRegistryItem.getDefaultDraftValue();
        const allowedOperators = resolveOperators({
            baseOperators: selectFilterRegistryItem.operators,
            operators: config?.operators,
            excludedOperators: config?.excludedOperators,
        });

        const operator = allowedOperators.some(
            (item) => item.id === defaultDraftValue.operator,
        )
            ? defaultDraftValue.operator
            : (allowedOperators[0]?.id ?? defaultDraftValue.operator);

        if (operator === 'is_in' || operator === 'is_not_in') {
            return {
                operator,
                values: [],
            };
        }

        return {
            operator,
            value: '',
        };
    },
    toAppliedFilterValue: (draftValue: SelectDraftValue) => {
        if ('values' in draftValue) {
            return {
                operator: draftValue.operator,
                values: draftValue.values,
            };
        }

        return {
            operator: draftValue.operator,
            value: draftValue.value,
        };
    },
};

const filterRegistry = {
    text: {
        operators: textOperators,
        getDefaultDraftValue: () => ({ operator: 'contains', value: '' }),
        getInitialDraftValue: (config) => {
            const defaultDraftValue =
                filterRegistry.text.getDefaultDraftValue();
            const allowedOperators = resolveOperators({
                baseOperators: filterRegistry.text.operators,
                operators: config?.operators,
                excludedOperators: config?.excludedOperators,
            });

            return {
                operator: allowedOperators.some(
                    (item) => item.id === defaultDraftValue.operator,
                )
                    ? defaultDraftValue.operator
                    : (allowedOperators[0]?.id ?? defaultDraftValue.operator),
                value: '',
            };
        },
        toAppliedFilterValue: (draftValue: TextDraftValue) =>
            draftValue.operator === 'is_empty' ||
            draftValue.operator === 'is_not_empty'
                ? {
                      operator: draftValue.operator,
                  }
                : {
                      operator: draftValue.operator,
                      value: draftValue.value,
                  },
    } as TextFilterRegistryItem,
    select: selectFilterRegistryItem,
    status: selectFilterRegistryItem,
};

export function getFilterRegistryItem(type: 'text'): TextFilterRegistryItem;
export function getFilterRegistryItem(
    type: 'select' | 'status',
): SelectFilterRegistryItem;
export function getFilterRegistryItem(type: DataTableColumnType) {
    if (type === 'text' || type === 'select' || type === 'status') {
        return filterRegistry[type];
    }

    throw new Error(`Filter registry item not implemented for type: ${type}`);
}

export function createDraftFilter(args: {
    columnId: string;
    label: string;
    meta?: TextMeta;
}): Extract<DraftFilter, { type: 'text' }>;
export function createDraftFilter(args: {
    columnId: string;
    label: string;
    meta: SelectMeta;
}): Extract<DraftFilter, { type: 'select' | 'status' }>;
export function createDraftFilter(args: {
    columnId: string;
    label: string;
    meta?: DataTableColumnMeta['dataTable'];
}): DraftFilter;
export function createDraftFilter({
    columnId,
    label,
    meta,
}: {
    columnId: string;
    label: string;
    meta?: DataTableColumnMeta['dataTable'];
}): DraftFilter {
    if (!meta || meta.type === 'text') {
        return {
            id: columnId,
            label,
            type: 'text',
            draftValue: getFilterRegistryItem('text').getInitialDraftValue({
                operators: meta?.operators,
                excludedOperators: meta?.excludedOperators,
            }),
        };
    }

    return {
        id: columnId,
        label,
        type: meta.type,
        draftValue: getFilterRegistryItem(meta.type).getInitialDraftValue({
            operators: meta.operators,
            excludedOperators: meta.excludedOperators,
        }),
    };
}
