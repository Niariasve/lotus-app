export type TextFilterValue =
    | {
          operator: 'is_empty' | 'is_not_empty';
      }
    | {
          operator:
              | 'is'
              | 'is_not'
              | 'contains'
              | 'does_not_contain'
              | 'starts_with'
              | 'ends_with';
          value: string;
      };
