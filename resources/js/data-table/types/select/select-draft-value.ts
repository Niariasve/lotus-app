export type SelectDraftValue =
    | {
        operator: 'is_in' | 'is_not_in';
        values: string[];
    }
    | {
        operator: 'equals' | 'not_equals';
        value: string;
    };
