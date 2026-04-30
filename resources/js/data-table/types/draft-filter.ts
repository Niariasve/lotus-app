import type { SelectDraftValue } from './select';
import type { TextDraftValue } from './text';

export type DraftFilter =
    | {
          id: string;
          label: string;
          type: 'text';
          draftValue: TextDraftValue;
      }
    | {
          id: string;
          label: string;
          type: 'select' | 'status';
          draftValue: SelectDraftValue;
      };
