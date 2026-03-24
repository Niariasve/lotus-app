<script setup lang='ts'>
import { Trash2 } from 'lucide-vue-next';
import type { AcceptableValue } from 'reka-ui';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { type Product } from '@/types';
import { type SupplierOrderItemDraft } from '../composables/useCreateSupplierOrderForm';

defineProps<{
    disabled: boolean,
    errors: Record<string, string>,
    formatMoney: (amount: number) => string,
    index: number,
    item: SupplierOrderItemDraft,
    itemError: (errors: Record<string, string>, index: number, field: string) => string | undefined,
    itemName: (index: number, field: string) => string,
    itemsLength: number,
    lineTotal: (item: SupplierOrderItemDraft) => number,
    products: Product[],
}>();

const emit = defineEmits<{
    remove: [key: number],
    'update:product': [payload: { key: number, value: AcceptableValue }],
    'update:quantity': [payload: { key: number, value: string | number }],
    'update:unit-cost': [payload: { key: number, value: string | number }],
}>();
</script>

<template>
    <div class="rounded-2xl border border-border bg-muted/20 p-4">
        <input type="hidden" :name="itemName(index, 'product_id')" :value="item.productId" />

        <div class="mb-4 flex items-center justify-between gap-3">
            <Badge variant="secondary">
                {{ $t('supplier_orders.fields.items') }} {{ index + 1 }}
            </Badge>

            <Button
                type="button"
                variant="ghost"
                size="sm"
                class="cursor-pointer text-destructive"
                :disabled="disabled || itemsLength === 1"
                @click="emit('remove', item.key)"
            >
                <Trash2 />
                {{ $t('supplier_orders.create.remove_item') }}
            </Button>
        </div>

        <div class="grid gap-4 md:grid-cols-[minmax(0,1.5fr)_minmax(0,0.7fr)_minmax(0,0.8fr)]">
            <Field>
                <FieldLabel :for="`product_${item.key}`">
                    Product
                </FieldLabel>
                <Select
                    :model-value="item.productId"
                    :disabled="disabled"
                    @update:model-value="emit('update:product', { key: item.key, value: $event })"
                >
                    <SelectTrigger :id="`product_${item.key}`" class="w-full">
                        <SelectValue placeholder="Product" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="product in products"
                            :key="product.id"
                            :value="String(product.id)"
                        >
                            {{ product.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="itemError(errors, index, 'product_id')" />
            </Field>

            <Field>
                <FieldLabel :for="`quantity_${item.key}`">
                    {{ $t('supplier_orders.fields.quantity') }}
                </FieldLabel>
                <Input
                    :id="`quantity_${item.key}`"
                    :name="itemName(index, 'quantity')"
                    :model-value="item.quantity"
                    type="number"
                    min="1"
                    step="1"
                    :disabled="disabled"
                    required
                    @update:model-value="emit('update:quantity', { key: item.key, value: $event })"
                />
                <InputError :message="itemError(errors, index, 'quantity')" />
            </Field>

            <Field>
                <FieldLabel :for="`unit_cost_${item.key}`">
                    {{ $t('supplier_orders.fields.unit_cost') }}
                </FieldLabel>
                <Input
                    :id="`unit_cost_${item.key}`"
                    :name="itemName(index, 'unit_cost')"
                    :model-value="item.unitCost"
                    type="number"
                    min="0"
                    step="0.01"
                    :disabled="disabled"
                    required
                    @update:model-value="emit('update:unit-cost', { key: item.key, value: $event })"
                />
                <InputError :message="itemError(errors, index, 'unit_cost')" />
            </Field>
        </div>

        <Separator class="my-4" />

        <div class="flex items-center justify-between gap-4 text-sm">
            <span class="text-muted-foreground">
                {{ $t('supplier_orders.fields.line_total') }}
            </span>
            <span class="font-semibold text-foreground">
                {{ formatMoney(lineTotal(item)) }}
            </span>
        </div>
    </div>
</template>
