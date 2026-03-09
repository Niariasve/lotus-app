<script setup lang="ts">
    import { Form, router } from '@inertiajs/vue3';
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
        RadioGroup,
        RadioGroupItem,
    } from '@/components/ui/radio-group';
    import { Spinner } from '@/components/ui/spinner';
    import supplierProductOfferRoutes from '@/routes/supplier-product-offer';
    import { type Product, type Supplier, type SupplierProductOffer } from '@/types';

    const props = defineProps<{
        supplierProductOffer?: SupplierProductOffer,
        suppliers: Supplier[],
        products: Product[],
    }>();

    const controller = () => {
        if (!props.supplierProductOffer) return SupplierProductOfferController.store.form();
        return SupplierProductOfferController.update.form(props.supplierProductOffer.id);
    };
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
                                <FieldLabel for="supplier_id">Supplier *</FieldLabel>
                                <select id="supplier_id" name="supplier_id" :disabled="processing"
                                    :value="supplierProductOffer?.supplier_id"
                                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="" disabled>Select a supplier</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.name }}
                                    </option>
                                </select>
                                <InputError :message="errors.supplier_id" />
                            </Field>
                            <Field>
                                <FieldLabel for="product_id">Product *</FieldLabel>
                                <select id="product_id" name="product_id" :disabled="processing"
                                    :value="supplierProductOffer?.product_id"
                                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="" disabled>Select a product</option>
                                    <option v-for="product in products" :key="product.id" :value="product.id">
                                        {{ product.sku }} - {{ product.name }}
                                    </option>
                                </select>
                                <InputError :message="errors.product_id" />
                            </Field>
                            <Field>
                                <FieldLabel for="currency">Currency *</FieldLabel>
                                <Input id="currency" name="currency" maxlength="3" placeholder="USD"
                                    :default-value="supplierProductOffer?.currency ?? 'USD'" :disabled="processing" />
                                <InputError :message="errors.currency" />
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                    <FieldSeparator />
                    <FieldSet>
                        <FieldLegend>Pricing</FieldLegend>
                        <FieldDescription>Costs and estimated fees for the supplier offer</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <FieldLabel for="base_cost">Base Cost *</FieldLabel>
                                <Input id="base_cost" name="base_cost" type="number" min="0" max="99999999.99"
                                    step="0.01" :default-value="supplierProductOffer?.base_cost" :disabled="processing"
                                    required />
                                <InputError :message="errors.base_cost" />
                            </Field>
                            <Field>
                                <FieldLabel for="estimated_tax">Estimated Tax</FieldLabel>
                                <Input id="estimated_tax" name="estimated_tax" type="number" min="0" max="99999999.99"
                                    step="0.01" :default-value="supplierProductOffer?.estimated_tax ?? 0"
                                    :disabled="processing" />
                                <InputError :message="errors.estimated_tax" />
                            </Field>
                            <Field>
                                <FieldLabel for="estimated_shipping">Estimated Shipping</FieldLabel>
                                <Input id="estimated_shipping" name="estimated_shipping" type="number" min="0"
                                    max="99999999.99" step="0.01"
                                    :default-value="supplierProductOffer?.estimated_shipping ?? 0"
                                    :disabled="processing" />
                                <InputError :message="errors.estimated_shipping" />
                            </Field>
                            <Field>
                                <FieldLabel for="other_fees">Other Fees</FieldLabel>
                                <Input id="other_fees" name="other_fees" type="number" min="0" max="99999999.99"
                                    step="0.01" :default-value="supplierProductOffer?.other_fees ?? 0"
                                    :disabled="processing" />
                                <InputError :message="errors.other_fees" />
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                    <FieldSeparator />
                    <FieldSet>
                        <FieldLegend>Availability</FieldLegend>
                        <FieldDescription>Current availability and verification date</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <FieldLabel>Is Available *</FieldLabel>
                                <InputError :message="errors.is_available" />
                                <RadioGroup name="is_available"
                                    :default-value="supplierProductOffer ? (supplierProductOffer.is_available ? '1' : '0') : '1'">
                                    <Field>
                                        <div class="flex items-center space-x-2">
                                            <RadioGroupItem id="is-available-yes" value="1" :disabled="processing" />
                                            <FieldLabel for="is-available-yes" class="text-sm">Available</FieldLabel>
                                        </div>
                                    </Field>
                                    <Field>
                                        <div class="flex items-center space-x-2">
                                            <RadioGroupItem id="is-available-no" value="0" :disabled="processing" />
                                            <FieldLabel for="is-available-no" class="text-sm">Unavailable</FieldLabel>
                                        </div>
                                    </Field>
                                </RadioGroup>
                            </Field>
                            <Field>
                                <FieldLabel for="last_checked_at">Last Checked At</FieldLabel>
                                <Input id="last_checked_at" name="last_checked_at" type="datetime-local"
                                    :default-value="supplierProductOffer?.last_checked_at?.replace(' ', 'T').slice(0, 16)"
                                    :disabled="processing" />
                                <InputError :message="errors.last_checked_at" />
                            </Field>
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
