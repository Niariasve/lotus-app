<script setup lang='ts'>
    import InputError from '@/components/InputError.vue';
    import { Field, FieldDescription, FieldGroup, FieldLabel, FieldLegend, FieldSet } from '@/components/ui/field';
    import { Input } from '@/components/ui/input';
    import {
        Select,
        SelectContent,
        SelectGroup,
        SelectItem,
        SelectLabel,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';
    import { trimDecimal } from '@/lib/utils';
    import { type SupplierProductOffer, type Product, type Supplier } from '@/types';

    defineProps<{
        suppliers: Supplier[],
        products: Product[],
        supplierId: number | null,
        productId: number | null,
        selectedSupplier?: Supplier,
        selectedProduct?: Product,
        supplierProductOffer?: SupplierProductOffer,
        errors: Record<string, string>
    }>();

    const emit = defineEmits([
        'update:supplier-id',
        'update:product-id',
    ]);
</script>

<template>
    <FieldSet>
        <FieldLegend>Offer Information</FieldLegend>
        <FieldDescription>Supplier and product for this offer</FieldDescription>
        <FieldGroup>
            <Field>
                <input type="hidden" name="supplier_id" :value="supplierId" />

                <FieldLabel>Supplier *</FieldLabel>
                <Select :model-value="supplierId" @update:model-value="emit('update:supplier-id', $event)">
                    <SelectTrigger class="w-auto">
                        <SelectValue placeholder="Select a Supplier" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Suppliers</SelectLabel>
                        </SelectGroup>
                        <SelectItem v-for="(supplier) in suppliers" :key="supplier.id" :value="supplier.id">
                            {{ supplier.name }}
                        </SelectItem>
                    </SelectContent>

                    <InputError :message="errors.supplier_id" />

                    <div v-if="selectedSupplier" class="rounded-md border p-3 text-sm">
                        <p><strong>Name:</strong> {{ selectedSupplier.name }}</p>
                        <p><strong>Currency:</strong> {{ selectedSupplier.currency }}</p>
                        <p><strong>Tax Policy:</strong> {{ trimDecimal(selectedSupplier.tax_policy) }}</p>
                        <p><strong>Est. Shipping:</strong> {{ selectedSupplier.estimated_shipping }}</p>
                    </div>
                </Select>
            </Field>

            <Field>
                <input type="hidden" name="product_id" :value="productId" />
                <FieldLabel for="product_id">Product *</FieldLabel>
                <Select :model-value="productId" @update:model-value="emit('update:product-id', $event)">
                    <SelectTrigger class="w-auto">
                        <SelectValue placeholder="Select a Product" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Products</SelectLabel>
                        </SelectGroup>
                        <SelectItem v-for="(product, index) in products" :key="index" :value="product.id">
                            {{ product.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <InputError :message="errors.product_id" />

                <div v-if="selectedProduct" class="rounded-md border p-3 text-sm">
                    <p><strong>SKU:</strong> {{ selectedProduct.sku }}</p>
                    <p><strong>Name:</strong> {{ selectedProduct.name }}</p>
                    <p><strong>Height:</strong> {{ trimDecimal(selectedProduct.height) || 'N/A' }}</p>
                    <p><strong>Est. Weight:</strong> {{ trimDecimal(selectedProduct.weight_est) || 'N/A'
                    }}</p>
                    <p><strong>Real Weight:</strong> {{ trimDecimal(selectedProduct.weight_real) ||
                        'N/A' }}</p>
                </div>
            </Field>

            <Field>
                <FieldLabel for="priority">Priority *</FieldLabel>
                <Input id="priority" name="priority" :default-value="supplierProductOffer?.priority" type="number"
                    min="0" placeholder="Ej: 2" />

                <InputError :message="errors.priority" />
            </Field>
        </FieldGroup>
    </FieldSet>
</template>

<style></style>