import type { AcceptableValue } from 'reka-ui';
import { computed, ref } from 'vue';
import {
    type Product,
    type Supplier,
    type SupplierOrder,
    type SupplierOrderStatus,
} from '@/types';

const EMPTY_STATUS_VALUE = '__none__';

type SupplierOrderItemDraft = {
    key: number,
    productId: string,
    quantity: string,
    unitCost: string,
};

export type { SupplierOrderItemDraft };

type UseCreateSupplierOrderFormProps = {
    order?: SupplierOrder,
    suppliers: Supplier[],
    statuses: SupplierOrderStatus[],
    products: Product[],
};

const createEmptyItem = (key: number): SupplierOrderItemDraft => ({
    key,
    productId: '',
    quantity: '1',
    unitCost: '0.00',
});

export const useCreateSupplierOrderForm = (
    props: UseCreateSupplierOrderFormProps,
) => {
    const initialItems = props.order?.items?.length
        ? props.order.items.map((item, index) => ({
            key: index,
            productId: String(item.product_id),
            quantity: String(item.quantity),
            unitCost: String(item.unit_cost),
        }))
        : [createEmptyItem(0)];

    const supplierId = ref(props.order ? String(props.order.supplier_id) : '');
    const statusId = ref(props.order?.status_id ? String(props.order.status_id) : '');
    const nextItemKey = ref(initialItems.length);
    const items = ref<SupplierOrderItemDraft[]>(initialItems);

    const selectedSupplier = computed(() => {
        return props.suppliers.find(
            (supplier) => supplier.id === Number(supplierId.value),
        ) ?? null;
    });

    const selectedStatus = computed(() => {
        return props.statuses.find(
            (status) => status.id === Number(statusId.value),
        ) ?? null;
    });

    const statusSelectValue = computed(() => {
        return statusId.value || EMPTY_STATUS_VALUE;
    });

    const itemCount = computed(() => items.value.length);

    const lineTotal = (item: SupplierOrderItemDraft): number => {
        return Number(item.quantity || 0) * Number(item.unitCost || 0);
    };

    const orderTotal = computed(() => {
        return items.value.reduce((total, item) => total + lineTotal(item), 0);
    });

    const currencyFormatter = computed(() => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: selectedSupplier.value?.currency ?? 'USD',
        });
    });

    const formatMoney = (amount: number): string => {
        return currencyFormatter.value.format(amount);
    };

    const addItem = (): void => {
        items.value.push(createEmptyItem(nextItemKey.value));
        nextItemKey.value += 1;
    };

    const removeItem = (key: number): void => {
        if (items.value.length === 1) {
            return;
        }

        items.value = items.value.filter((item) => item.key !== key);
    };

    const setSupplierId = (value: AcceptableValue): void => {
        supplierId.value = value === null ? '' : String(value);
    };

    const setStatusId = (value: AcceptableValue): void => {
        if (value === null) {
            statusId.value = '';
            return;
        }

        const normalizedValue = String(value);

        statusId.value = normalizedValue === EMPTY_STATUS_VALUE ? '' : normalizedValue;
    };

    const setItemProduct = (key: number, value: AcceptableValue): void => {
        const item = items.value.find((currentItem) => currentItem.key === key);

        if (!item) {
            return;
        }

        item.productId = value === null ? '' : String(value);
    };

    const setItemQuantity = (key: number, value: string | number): void => {
        const item = items.value.find((currentItem) => currentItem.key === key);

        if (!item) {
            return;
        }

        item.quantity = String(value);
    };

    const setItemUnitCost = (key: number, value: string | number): void => {
        const item = items.value.find((currentItem) => currentItem.key === key);

        if (!item) {
            return;
        }

        item.unitCost = String(value);
    };

    const itemName = (index: number, field: string): string => {
        return `items[${index}][${field}]`;
    };

    const itemError = (
        errors: Record<string, string>,
        index: number,
        field: string,
    ): string | undefined => {
        return errors[`items.${index}.${field}`];
    };

    return {
        addItem,
        EMPTY_STATUS_VALUE,
        formatMoney,
        itemCount,
        itemError,
        itemName,
        items,
        lineTotal,
        orderTotal,
        removeItem,
        selectedStatus,
        selectedSupplier,
        setItemProduct,
        setItemQuantity,
        setItemUnitCost,
        setStatusId,
        setSupplierId,
        statusId,
        statusSelectValue,
        supplierId,
    };
};
