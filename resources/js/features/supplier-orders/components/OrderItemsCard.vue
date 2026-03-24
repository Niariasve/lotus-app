<script setup lang='ts'>
import { PackagePlus } from 'lucide-vue-next';
import type { AcceptableValue } from 'reka-ui';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { type Product } from '@/types';
import { type SupplierOrderItemDraft } from '../composables/useCreateSupplierOrderForm';
import OrderItemRow from './OrderItemRow.vue';

defineProps<{
    addItem: () => void,
    disabled: boolean,
    errors: Record<string, string>,
    formatMoney: (amount: number) => string,
    itemError: (errors: Record<string, string>, index: number, field: string) => string | undefined,
    itemName: (index: number, field: string) => string,
    items: SupplierOrderItemDraft[],
    lineTotal: (item: SupplierOrderItemDraft) => number,
    products: Product[],
    removeItem: (key: number) => void,
}>();

const emit = defineEmits<{
    'update:item-product': [payload: { key: number, value: AcceptableValue }],
    'update:item-quantity': [payload: { key: number, value: string | number }],
    'update:item-unit-cost': [payload: { key: number, value: string | number }],
}>();
</script>

<template>
    <Card class="gap-0 overflow-hidden pt-0">
        <CardHeader class="border-b bg-muted/30 pt-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <CardTitle>{{ $t('supplier_orders.fields.items') }}</CardTitle>
                    <CardDescription>
                        {{ $t('supplier_orders.create.items_description') }}
                    </CardDescription>
                </div>

                <Button type="button" variant="outline" class="cursor-pointer" :disabled="disabled" @click="addItem">
                    <PackagePlus />
                    {{ $t('supplier_orders.create.add_item') }}
                </Button>
            </div>
        </CardHeader>

        <CardContent class="space-y-4 pt-6">
            <OrderItemRow
                v-for="(item, index) in items"
                :key="item.key"
                :disabled="disabled"
                :errors="errors"
                :format-money="formatMoney"
                :index="index"
                :item="item"
                :item-error="itemError"
                :item-name="itemName"
                :items-length="items.length"
                :line-total="lineTotal"
                :products="products"
                @remove="removeItem"
                @update:product="emit('update:item-product', $event)"
                @update:quantity="emit('update:item-quantity', $event)"
                @update:unit-cost="emit('update:item-unit-cost', $event)"
            />

            <InputError :message="errors.items" />
        </CardContent>
    </Card>
</template>
