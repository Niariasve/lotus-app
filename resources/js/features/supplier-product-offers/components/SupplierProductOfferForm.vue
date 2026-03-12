<script setup lang="ts">
    import { Form, router } from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button';
    import {
        FieldGroup,
        FieldSeparator,
    } from '@/components/ui/field';
    import { Spinner } from '@/components/ui/spinner';
    import AvailabilityFormSection from '@/features/supplier-product-offers/components/AvailabilityFormSection.vue';
    import OfferInformationFormSection from '@/features/supplier-product-offers/components/OfferInformationFormSection.vue';
    import RetailPriceCalculatorFormSection from '@/features/supplier-product-offers/components/RetailPriceCalculatorFormSection.vue';
    import { useSupplierProductOfferForm } from '@/features/supplier-product-offers/composables/useSupplierProductOfferForm';
    import supplierProductOfferRoutes from '@/routes/supplier-product-offer';
    import { type Product, type Supplier, type SupplierProductOffer } from '@/types';

    const props = defineProps<{
        supplierProductOffer?: SupplierProductOffer,
        suppliers: Supplier[],
        products: Product[],
    }>();

    const form = useSupplierProductOfferForm(props);
</script>

<template>
    <Form v-bind="form.controller()" v-slot="{ processing, errors }">
        <div class="mx-auto flex flex-col gap-4 md:max-w-4xl">
            <div class="space-y-3 rounded-xl border p-4">
                <FieldGroup>
                    <OfferInformationFormSection :suppliers="suppliers" :products="products"
                        :supplier-id="form.supplierId.value" :product-id="form.productId.value"
                        :selected-supplier="form.selectedSupplier.value" :selected-product="form.selectedProduct.value"
                        :supplier-product-offer="supplierProductOffer" :errors="errors"
                        @update:supplier-id="form.supplierId.value = $event"
                        @update:product-id="form.productId.value = $event" />

                    <FieldSeparator />

                    <RetailPriceCalculatorFormSection :estimated-shipping="form.estimatedShipping.value"
                        :tax="form.tax.value" :product-weight="form.productWeight.value"
                        :courier-fee="form.courierFee.value" :base-cost="form.baseCost.value"
                        :profit-percentage="form.profitPercentage.value" :retail-price="form.retailPrice.value"
                        :raw-retail-price="form.rawRetailPrice.value" :selected-currency="form.selectedCurrency.value"
                        :errors="errors" @update:courier-fee="form.courierFee.value = $event"
                        @update:base-cost="form.baseCost.value = $event" @profit-input="form.handleProfitInput"
                        @retail-input="form.handleRetailInput" />

                    <FieldSeparator />

                    <AvailabilityFormSection :is-available="form.isAvailable.value"
                        :current-last-checked-at="form.currentLastCheckedAt.value" :errors="errors"
                        @update:is-available="form.isAvailable.value = $event" :url="form.url.value" />
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
