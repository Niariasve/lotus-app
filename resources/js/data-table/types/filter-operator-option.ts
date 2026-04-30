export type FilterOperatorOption<TOperator extends string> = {
    id: TOperator;
    label: string;
    requiresValue: boolean;
};
