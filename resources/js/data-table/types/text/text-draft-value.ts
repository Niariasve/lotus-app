import type { TextFilterOperator } from './text-filter-operator';

export type TextDraftValue = {
    operator: TextFilterOperator;
    value: string;
};
