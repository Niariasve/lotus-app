import type { SelectFilterOperator, SelectOption } from './select';
import type { TextFilterOperator } from './text';

export interface DataTableColumnMeta {
    dataTable:
        | {
              label: string;
              type: 'text';
              operators?: TextFilterOperator[];
              excludedOperators?: TextFilterOperator[];
          }
        | {
              label: string;
              type: 'select' | 'status';
              operators?: SelectFilterOperator[];
              excludedOperators?: SelectFilterOperator[];
              options: SelectOption[];
          };
}
