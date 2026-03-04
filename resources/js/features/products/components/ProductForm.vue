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
    import {
        RadioGroup,
        RadioGroupItem,
    } from '@/components/ui/radio-group'
    import { ScrollArea } from '@/components/ui/scroll-area'
    import { Spinner } from '@/components/ui/spinner';
    import products from '@/routes/products';
    import { type Product } from '@/types';

    const props = defineProps<{
        product?: Product
    }>();

    const controller = () => {
        if (!props.product) return ProductController.store.form()
        else return ProductController.update.form(props.product!.id)
    }
</script>

<template>
    <Form v-bind="controller()">
        <div class="flex flex-col gap-4 md:max-w-4xl mx-auto">
            <div class="p-4 space-y-3 border rounded-xl">
                <FieldGroup>
                    <FieldSet>
                        <FieldLegend>Product Information</FieldLegend>
                        <FieldDescription>General product information</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <FieldLabel for="sku">SKU *</FieldLabel>
                                <Input id="sku" name="sku" placeholder="ABC123456" />
                            </Field>
                            <Field>
                                <FieldLabel for="name">Name *</FieldLabel>
                                <Input id="name" name="name" placeholder="Naruto Shipuden" />
                            </Field>
                            <Field>
                                <FieldLabel for="description">Description</FieldLabel>
                                <Input id="description" name="description" placeholder="Product Description" />
                                
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                </FieldGroup>
            </div>
        </div>
    </Form>
</template>