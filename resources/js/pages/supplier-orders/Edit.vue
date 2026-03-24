<script setup lang='ts'>
import { Form, Head, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import OrderDetailsCard from '@/features/supplier-orders/components/OrderDetailsCard.vue';
import OrderItemsCard from '@/features/supplier-orders/components/OrderItemsCard.vue';
import OrderSummaryCard from '@/features/supplier-orders/components/OrderSummaryCard.vue';
import { useCreateSupplierOrderForm } from '@/features/supplier-orders/composables/useCreateSupplierOrderForm';
import AppLayout from '@/layouts/AppLayout.vue';
import supplierOrdersRoutes from '@/routes/supplier-orders';
import {
    type BreadcrumbItem,
    type Product,
    type Supplier,
    type SupplierOrder,
    type SupplierOrderStatus,
} from '@/types';

const props = defineProps<{
    order: SupplierOrder,
    products: Product[],
    suppliers: Supplier[],
    statuses: SupplierOrderStatus[],
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Supplier Orders',
        href: supplierOrdersRoutes.index().url,
    },
    {
        title: props.order.order_number,
        href: supplierOrdersRoutes.edit(props.order.id).url,
    },
];

const {
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
} = useCreateSupplierOrderForm(props);
</script>

<template>
    <Head :title="$t('supplier_orders.edit.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-3">
                    <Badge variant="outline">{{ $t('actions.update') }}</Badge>
                    <Heading
                        :title="$t('supplier_orders.edit.title')"
                        :description="$t('supplier_orders.edit.description')"
                        class="mb-0"
                    />
                </div>

                <Button
                    type="button"
                    variant="ghost"
                    class="cursor-pointer self-start"
                    @click="router.visit(supplierOrdersRoutes.index().url)"
                >
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <Form
                v-bind="supplierOrdersRoutes.update.form(order.id)"
                :options="{ preserveScroll: true }"
                v-slot="{ processing, errors }"
            >
                <input type="hidden" name="supplier_id" :value="supplierId" />
                <input type="hidden" name="status_id" :value="statusId" />

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="space-y-6">
                        <OrderDetailsCard
                            :arrived-at-default-value="order.arrived_at"
                            :disabled="processing"
                            :empty-status-value="EMPTY_STATUS_VALUE"
                            :errors="errors"
                            :order-number-default-value="order.order_number"
                            :ordered-at-default-value="order.ordered_at"
                            :shipped-at-default-value="order.shipped_at"
                            :status-id="statusId"
                            :status-select-value="statusSelectValue"
                            :statuses="statuses"
                            :supplier-id="supplierId"
                            :suppliers="suppliers"
                            :tracking-default-value="order.tracking"
                            @update:status-id="setStatusId"
                            @update:supplier-id="setSupplierId"
                        />

                        <OrderItemsCard
                            :add-item="addItem"
                            :disabled="processing"
                            :errors="errors"
                            :format-money="formatMoney"
                            :item-error="itemError"
                            :item-name="itemName"
                            :items="items"
                            :line-total="lineTotal"
                            :products="products"
                            :remove-item="removeItem"
                            @update:item-product="setItemProduct($event.key, $event.value)"
                            @update:item-quantity="setItemQuantity($event.key, $event.value)"
                            @update:item-unit-cost="setItemUnitCost($event.key, $event.value)"
                        />
                    </div>

                    <div class="xl:sticky xl:top-6 xl:self-start">
                        <OrderSummaryCard
                            :disabled="processing"
                            :format-money="formatMoney"
                            :item-count="itemCount"
                            :order-total="orderTotal"
                            :selected-status="selectedStatus"
                            :selected-supplier="selectedSupplier"
                            @cancel="router.visit(supplierOrdersRoutes.index().url)"
                        />
                    </div>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
