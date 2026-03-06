<script setup lang="ts">
    import { Form, router } from '@inertiajs/vue3';
    import ProductController from '@/actions/App/Http/Controllers/ProductController';
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
    } from '@/components/ui/field'
    import { Input } from '@/components/ui/input';
    import { Spinner } from '@/components/ui/spinner';
    import { Textarea } from '@/components/ui/textarea';
    import products from '@/routes/products';
    import { type Product } from '@/types';

    const props = defineProps<{
        product?: Product
    }>();

    const controller = () => {
        if (!props.product) return ProductController.store.form()
        else return ProductController.update.form(props.product!.id)
    }

    console.log(props.product);
</script>

<template>
    <Form v-bind="controller()" v-slot="{ processing, errors }">
        <div class="flex flex-col gap-4 md:max-w-4xl mx-auto">
            <div class="p-4 space-y-3 border rounded-xl">
                <FieldGroup>
                    <FieldSet>
                        <FieldLegend>Product Information</FieldLegend>
                        <FieldDescription>General product information</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <FieldLabel for="sku">SKU *</FieldLabel>
                                <Input id="sku" name="sku" placeholder="ABC123456" :default-value="product?.sku" />
                                <InputError :message="errors.sku" />
                            </Field>
                            <Field>
                                <FieldLabel for="name">Name *</FieldLabel>
                                <Input id="name" name="name" placeholder="Naruto Shipuden" :default-value="product?.name" />
                                <InputError :message="errors.name" />
                            </Field>
                            <Field>
                                <FieldLabel for="description">Description</FieldLabel>
                                <Textarea id="description" name="description" placeholder="Product Description..." :default-value="product?.description" />
                                <InputError :message="errors.description" />
                            </Field>
                            <Field>
                                <FieldLabel for="brand">Brand</FieldLabel>
                                <Input id="brand" name="brand" placeholder="Sony" :default-value="product?.brand" />
                                <InputError :message="errors.brand" />
                            </Field>
                            <Field>
                                <FieldLabel for="line">Line</FieldLabel>
                                <Input id="line" name="line" placeholder="Idontknow..." :default-value="product?.line" />
                                <InputError :message="errors.line" />
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                    <FieldSeparator />
                    <FieldSet>
                        <FieldLegend>Product Attributes</FieldLegend>
                        <FieldDescription>Product specifications and attributes</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <FieldLabel for="height">Height (cm)</FieldLabel>
                                <Input id="height" name="height" type="number" min="0" max="999.999" step="0.001" placeholder="35" :default-value="product?.height" />
                                <FieldDescription>Enter height in centimeters (cm)</FieldDescription>
                                <InputError :message="errors.height" />
                            </Field>
                            <Field>
                                <FieldLabel for="weight_est">Estimated Weight (lb)</FieldLabel>
                                <Input id="weight_est" name="weight_est" type="number" min="0" max="999.999" step="0.001" placeholder="35" :default-value="product?.weight_est" />
                                <FieldDescription>Enter estimated weight in pounds (lb)</FieldDescription>
                                <InputError :message="errors.weight_est" />
                            </Field>
                            <Field>
                                <FieldLabel for="weight_real">Real Weight (lb)</FieldLabel>
                                <Input id="weight_real" name="weight_real" type="number" min="0" max="999.999" step="0.001" placeholder="35" :default-value="product?.weight_real" />
                                <FieldDescription>Enter real weight in pounds (lb)</FieldDescription>
                                <InputError :message="errors.weight_real" />
                            </Field>
                            <Field>
                                <FieldLabel for="release_date">Release Date</FieldLabel>
                                <Input id="release_date" name="release_date" type="date" :default-value="product?.release_date" />
                                <FieldDescription>Enter release date with MM/DD/YYYY format</FieldDescription>
                                <InputError :message="errors.release_date" />
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                </FieldGroup>
            </div>
            <div class="flex gap-2">
                <Button type="submit" :disabled="processing" class="cursor-pointer">
                    <Spinner v-if="processing" class="animate-spin" />
                    <div v-if="product">{{ $t('actions.update') }}</div>
                    <div v-else>{{ $t('actions.submit') }}</div>
                </Button>
                <Button type="button" :disabled="processing" variant="destructive" class="cursor-pointer"
                    @click="router.visit(products.index().url)">
                    {{ $t('actions.cancel') }}
                </Button>
            </div>
        </div>
    </Form>
</template>