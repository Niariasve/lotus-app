<script setup lang='ts'>
    import InputError from '@/components/InputError.vue';
    import {
        Field,
        FieldDescription,
        FieldGroup,
        FieldLabel,
        FieldLegend,
        FieldSeparator,
        FieldSet,
    } from '@/components/ui/field';
    import { Input } from '@/components/ui/input';
    import { formatMoney, formatPercent } from '../lib/utils';


    defineProps<{
        estimatedShipping?: number,
        tax?: number,
        productWeight?: number,
        courierFee?: number,
        baseCost?: number,
        profitPercentage?: number,
        retailPrice?: number,
        rawRetailPrice?: number,
        selectedCurrency?: string,
        errors: Record<string, string>,
    }>();

    const emit = defineEmits([
        'update:courier-fee',
        'update:base-cost',
        'profit-input',
        'retail-input',
    ]);
</script>

<template>
    <FieldSet>
        <FieldLegend>Retail Price Calculator</FieldLegend>
        <FieldDescription>Calculate the retail price of this product for this supplier</FieldDescription>

        <FieldGroup>

            <FieldGroup class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                <Field>
                    <FieldLabel for="estimated_shipping">Estimated shipping cost</FieldLabel>
                    <Input id="estimated_shipping" :model-value="estimatedShipping" class="bg-muted" readonly />
                    <FieldDescription>Estimated shipping cost from supplier</FieldDescription>
                </Field>

                <Field>
                    <FieldLabel for="tax">Tax</FieldLabel>
                    <Input id="tax" :model-value="tax" class="bg-muted" readonly />
                    <FieldDescription>Tax policy from supplier</FieldDescription>
                </Field>

                <Field>
                    <FieldLabel for="product_weight">Product Weight (lb)</FieldLabel>
                    <Input id="product_weight" :model-value="productWeight" class="bg-muted" readonly />
                    <FieldDescription>Estimated or real weight of the product according to which value is available
                    </FieldDescription>
                </Field>
            </FieldGroup>

            <FieldSeparator />

            <Field>
                <FieldLabel for="courier_fee">Courier's Weight Fee *</FieldLabel>
                <Input id="courier_fee" type="number" min="0" step="0.01" :model-value="courierFee"
                    @update:model-value="emit('update:courier-fee', $event)" />
                <FieldDescription><span class="font-bold">Editable.</span> Courier's weight fee</FieldDescription>
            </Field>

            <Field>
                <FieldLabel for="base_cost">Base Cost of Product *</FieldLabel>
                <Input id="base_cost" name="base_cost" type="number" min="0" step="0.01" :model-value="baseCost"
                    @update:model-value="emit('update:base-cost', $event)" placeholder="Ej. 50" />
                <InputError :message="errors.base_cost" />
                <FieldDescription><span class="font-bold">Editable.</span> The supplier's cost for this product
                </FieldDescription>
            </Field>

            <Field>
                <FieldLabel for="profit_percentage">Profit Percentage *</FieldLabel>
                <Input id="profit_percentage" name="profit_percentage" type="number" min="0" max="1" step="0.0001"
                    :model-value="profitPercentage" @update:model-value="emit('profit-input', $event)"
                    placeholder="Ej. 0.35" />
                    <InputError :message="errors.profit_percentage" />
                <FieldDescription>
                    <span class="font-bold">Editable.</span> If you change this value, retail price is recalculated.
                </FieldDescription>
            </Field>

            <Field>
                <FieldLabel for="retail_price">Retail Price *</FieldLabel>
                <Input id="retail_price" name="retail_price" type="number" min="0" step="0.01" :model-value="retailPrice"
                    @update:model-value="emit('retail-input', $event)" />
                <InputError :message="errors.retail_price" />
                <FieldDescription>
                    <span class="font-bold">Editable.</span> Enter your target retail price. If you change this value,
                    profit is recalculated.
                </FieldDescription>
            </Field>

            <Field>
                <div class="rounded-md border p-3 text-sm space-y-1">
                    <p>
                        <strong>Original Cost (Raw):</strong>
                        {{ selectedCurrency }} {{ formatMoney(rawRetailPrice) }}
                    </p>
                    <p>
                        <strong>Final (with profit):</strong>
                        {{ selectedCurrency }} {{ formatMoney(retailPrice) }}
                    </p>
                    <p>
                        <strong>Derived Profit Margin:</strong>
                        {{ formatPercent(profitPercentage) }}
                    </p>
                    <p>
                        <strong>Total Profit:</strong>
                        {{ selectedCurrency }}
                        {{ formatMoney((retailPrice ?? 0) - (rawRetailPrice ?? 0)) }}
                    </p>
                    <p class="text-muted-foreground">
                        Raw formula: ((base_cost + estimated_shipping) * (1 + tax)) + (courier_fee * product_weight)
                    </p>
                </div>
            </Field>
        </FieldGroup>
    </FieldSet>
</template>

<style></style>