<script setup lang="ts">
    import { Form, router } from '@inertiajs/vue3';
    import { computed, ref, watch } from 'vue';
    import SupplierProductOfferController from '@/actions/App/Http/Controllers/SupplierProductOfferController';
    import InputError from '@/components/InputError.vue';
    import { Button } from '@/components/ui/button';
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
    import {
        Select,
        SelectContent,
        SelectGroup,
        SelectItem,
        SelectLabel,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';
    import { Spinner } from '@/components/ui/spinner';
    import { trimDecimal } from '@/lib/utils';
    import supplierProductOfferRoutes from '@/routes/supplier-product-offer';
    import { type Product, type Supplier, type SupplierProductOffer } from '@/types';

    const props = defineProps<{
        supplierProductOffer?: SupplierProductOffer,
        suppliers: Supplier[],
        products: Product[],
    }>();

    const DEFAULT_COURIER_FEE = 8.75;

    const controller = () => {
        if (!props.supplierProductOffer) return SupplierProductOfferController.store.form();
        return SupplierProductOfferController.update.form(props.supplierProductOffer.id);
    };

    const supplierId = ref<number | null>(
        props.supplierProductOffer ? props.supplierProductOffer.supplier_id : null
    );

    const selectedSupplier = computed(() =>
        props.suppliers.find((s) => s.id === supplierId.value)
    );

    const estimatedShipping = ref<number | undefined>(undefined);
    const tax = ref<number | undefined>(undefined);
    const productWeight = ref<number | undefined>(undefined);

    const productId = ref<number | null>(
        props.supplierProductOffer ? props.supplierProductOffer.product_id : null
    );

    const selectedProduct = computed(() =>
        props.products.find((p) => p.id === productId.value)
    );

    const baseCost = ref<number | undefined>(
        props.supplierProductOffer ? Number(props.supplierProductOffer.base_cost) : undefined
    );
    const courierFee = ref<number>(DEFAULT_COURIER_FEE);
    const profitPercentage = ref<number | undefined>(
        props.supplierProductOffer ? Number(props.supplierProductOffer.profit_percentage) : undefined
    );
    const retailPrice = ref<number | undefined>(
        props.supplierProductOffer ? Number(props.supplierProductOffer.retail_price) : undefined
    );

    const selectedCurrency = computed(() => selectedSupplier.value?.currency ?? 'USD');

    const rawRetailPrice = computed(() => {
        if (
            baseCost.value === undefined ||
            estimatedShipping.value === undefined ||
            tax.value === undefined ||
            productWeight.value === undefined
        ) return undefined;


        return ((baseCost.value + estimatedShipping.value) * (1 + tax.value)) + (courierFee.value * productWeight.value);
    });

    const finalRetailPrice = computed(() => {
        if (rawRetailPrice.value === undefined || profitPercentage.value === undefined) return undefined;
        return rawRetailPrice.value * (1 + profitPercentage.value);
    });

    const formatMoney = (value: number | undefined): string => {
        if (value === undefined || Number.isNaN(value)) return 'N/A';
        return Number(value).toFixed(2);
    };

    watch(selectedSupplier, (supplier) => {
        if (supplier) {
            estimatedShipping.value = Number(supplier.estimated_shipping);
            tax.value = trimDecimal(supplier.tax_policy) as number;
        }
    }, { immediate: true });

    watch(selectedProduct, (product) => {
        if (product) {
            const weightValue = (product.weight_real ? product.weight_real : product.weight_est) ?? 0;
            productWeight.value = trimDecimal(weightValue) as number;
        }
    }, { immediate: true });

    watch(finalRetailPrice, (value) => {
        retailPrice.value = value !== undefined ? Number(value.toFixed(2)) : undefined;
    }, { immediate: true });

</script>

<template>
    <Form v-bind="controller()" v-slot="{ processing, errors }">
        <div class="mx-auto flex flex-col gap-4 md:max-w-4xl">
            <div class="space-y-3 rounded-xl border p-4">
                <FieldGroup>
                    <FieldSet>
                        <FieldLegend>Offer Information</FieldLegend>
                        <FieldDescription>Supplier and product for this offer</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <input type="hidden" name="supplier_id" :value="supplierId" />
                                <FieldLabel for="supplier_id">Supplier *</FieldLabel>
                                <Select v-model="supplierId">
                                    <SelectTrigger class="w-auto">
                                        <SelectValue placeholder="Select a Supplier" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Suppliers</SelectLabel>
                                        </SelectGroup>
                                        <SelectItem v-for="(supplier, index) in suppliers" :key="index"
                                            :value="supplier.id">
                                            {{ supplier.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.supplier_id" />

                                <div v-if="selectedSupplier" class="rounded-md border p-3 text-sm">
                                    <p><strong>Name:</strong> {{ selectedSupplier.name }}</p>
                                    <p><strong>Currency:</strong> {{ selectedSupplier.currency }}</p>
                                    <p><strong>Tax Policy:</strong> {{ trimDecimal(selectedSupplier.tax_policy) }}</p>
                                    <p><strong>Est. Shipping:</strong> {{ selectedSupplier.estimated_shipping }}</p>
                                </div>
                            </Field>

                            <Field>
                                <input type="hidden" name="product_id" :value="productId" />
                                <FieldLabel for="product_id">Product *</FieldLabel>
                                <Select v-model="productId">
                                    <SelectTrigger class="w-auto">
                                        <SelectValue placeholder="Select a Product" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Products</SelectLabel>
                                        </SelectGroup>
                                        <SelectItem v-for="(product, index) in products" :key="index"
                                            :value="product.id">
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
                                <Input id="priority" name="priority" :default-value="supplierProductOffer?.priority"
                                    type="number" min="0" placeholder="Ej: 2" />
                            </Field>
                        </FieldGroup>
                        <FieldSeparator />
                        <FieldGroup>
                            <FieldSet>
                                <FieldLegend>Retail Price Calculator</FieldLegend>
                                <FieldDescription>Calculate the retail price of this product for this supplier
                                </FieldDescription>

                                <FieldGroup>
                                    <Field>
                                        <FieldLabel for="estimated_shipping">Estimated Shipping</FieldLabel>
                                        <Input id="estimated_shipping" v-model="estimatedShipping" readonly />
                                        <FieldDescription>Estimated shipping cost from supplier</FieldDescription>
                                    </Field>
                                    <Field>
                                        <FieldLabel for="tax">Tax Policy</FieldLabel>
                                        <Input id="tax" v-model="tax" readonly />
                                        <FieldDescription>Tax policy from supplier</FieldDescription>
                                    </Field>
                                    <Field>
                                        <FieldLabel for="product_weight">Product Weight (lbs.)</FieldLabel>
                                        <Input id="product_weight" v-model="productWeight" readonly />
                                        <FieldDescription>Estimated or real weight of the product according to which
                                            value is available</FieldDescription>
                                    </Field>
                                    <Field>
                                        <FieldLabel for="courier_fee">Courier's Weight Fee</FieldLabel>
                                        <Input id="courier_fee" type="number" min="0" step="0.01"
                                            v-model="courierFee" />
                                        <FieldDescription>Courier's weight fee</FieldDescription>
                                    </Field>
                                    <Field>
                                        <FieldLabel for="base_cost">Base Cost *</FieldLabel>
                                        <Input id="base_cost" name="base_cost" type="number" min="0" step="0.01"
                                            v-model="baseCost" placeholder="75" />
                                    </Field>
                                    <Field>
                                        <FieldLabel for="profit_percentage">Profit Percentage</FieldLabel>
                                        <Input id="profit_percentage" name="profit_percentage" type="number" min="0"
                                            max="1" step="0.0001" v-model="profitPercentage" />
                                        <FieldDescription>Profit percentage desirable for this product written as
                                            decimal</FieldDescription>
                                    </Field>
                                    <Field>
                                        <FieldLabel for="retail_price">Retail Price</FieldLabel>
                                        <Input id="retail_price" name="retail_price" type="number" step="0.01"
                                            v-model="retailPrice" readonly />
                                        <FieldDescription>Calculated retail price of the product calculated
                                        </FieldDescription>
                                    </Field>
                                    <Field>
                                        <div class="rounded-md border p-3 text-sm space-y-1">
                                            <p><strong>Raw (no profit):</strong> {{ selectedCurrency }} {{
                                                formatMoney(rawRetailPrice) }}</p>
                                            <p><strong>Final (with profit):</strong> {{ selectedCurrency }} {{
                                                formatMoney(finalRetailPrice) }}</p>
                                            <p class="text-muted-foreground">
                                                Formula: ((base_cost + estimated_shipping) * tax) + (courier_fee *
                                                product_weight), then + profit%.
                                            </p>
                                        </div>
                                    </Field>
                                </FieldGroup>
                            </FieldSet>
                        </FieldGroup>
                    </FieldSet>
                </FieldGroup>
            </div>
            <div class="flex gap-2">
                <Button type="submit" :disabled="processing" class="cursor-pointer">
                    <Spinner v-if="processing" class="animate-spin" />
                    <div v-if="supplierProductOffer">{{ $t('actions.update') }}</div>
                    <div v-else>{{ $t('actions.submit') }}</div>
                </Button>
                <Button type="button" :disabled="processing" variant="destructive" class="cursor-pointer"
                    @click="router.visit(supplierProductOfferRoutes.index().url)">
                    {{ $t('actions.cancel') }}
                </Button>
            </div>
        </div>
    </Form>
</template>
