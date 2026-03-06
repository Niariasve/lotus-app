<script setup lang="ts">
    import { Form, router } from '@inertiajs/vue3';
    import SupplierController from '@/actions/App/Http/Controllers/SupplierController';
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
    import { Spinner } from '@/components/ui/spinner';
    import { Textarea } from '@/components/ui/textarea';
    import suppliers from '@/routes/suppliers';
    import { type Supplier } from '@/types';

    const props = defineProps<{
        supplier?: Supplier
    }>();

    const controller = () => {
        if (!props.supplier) return SupplierController.store.form()
        else return SupplierController.update.form(props.supplier!.id)
    }
</script>

<template>
    <Form v-bind="controller()" v-slot="{ processing, errors }">
        <div class="flex flex-col gap-4 md:max-w-4xl mx-auto">
            <div class="p-4 space-y-3 border rounded-xl">
                <FieldGroup>
                    <FieldSet>
                        <FieldLegend>Supplier Information</FieldLegend>
                        <FieldDescription>General supplier information</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <FieldLabel for="name">Name *</FieldLabel>
                                <Input id="name" name="name" placeholder="Bandai Namco" :default-value="supplier?.name"
                                    :disabled="processing" required maxlength="150" />
                                <InputError :message="errors.name" />
                            </Field>
                            <Field>
                                <FieldLabel for="description">Description</FieldLabel>
                                <Textarea id="description" name="description" placeholder="Supplier description..."
                                    :default-value="supplier?.description" :disabled="processing" />
                                <InputError :message="errors.description" />
                            </Field>
                            <Field>
                                <FieldLabel for="priority">Priority</FieldLabel>
                                <Input id="priority" name="priority" type="number" min="0"
                                    :default-value="supplier?.priority ?? 100" :disabled="processing" />
                                <FieldDescription>Lower value means higher priority</FieldDescription>
                                <InputError :message="errors.priority" />
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                    <FieldSeparator />
                    <FieldSet>
                        <FieldLegend>Supplier Policies</FieldLegend>
                        <FieldDescription>Tax, shipping and payment currency policies</FieldDescription>
                        <FieldGroup>
                            <Field>
                                <FieldLabel for="tax_policy">Tax Policy *</FieldLabel>
                                <Input id="tax_policy" name="tax_policy" type="number" min="0" max="1" step="0.0001"
                                    :default-value="supplier?.tax_policy ?? 0" :disabled="processing" required />
                                <FieldDescription>Value between 0 and 1 (up to 4 decimals)</FieldDescription>
                                <InputError :message="errors.tax_policy" />
                            </Field>
                            <Field>
                                <FieldLabel for="shipping_policy">Shipping Policy *</FieldLabel>
                                <Input id="shipping_policy" name="shipping_policy" type="number" min="0" max="1"
                                    step="0.0001" :default-value="supplier?.shipping_policy ?? 0"
                                    :disabled="processing" required />
                                <FieldDescription>Value between 0 and 1 (up to 4 decimals)</FieldDescription>
                                <InputError :message="errors.shipping_policy" />
                            </Field>
                            <Field>
                                <FieldLabel>Currency *</FieldLabel>
                                <InputError :message="errors.currency" />
                                <RadioGroup name="currency" :default-value="supplier?.currency ?? 'USD'">
                                    <Field>
                                        <div class="flex items-center space-x-2">
                                            <RadioGroupItem id="currency-usd" value="USD" :disabled="processing" />
                                            <FieldLabel for="currency-usd" class="text-sm">USD</FieldLabel>
                                        </div>
                                    </Field>
                                    <Field>
                                        <div class="flex items-center space-x-2">
                                            <RadioGroupItem id="currency-eur" value="EUR" :disabled="processing" />
                                            <FieldLabel for="currency-eur" class="text-sm">EUR</FieldLabel>
                                        </div>
                                    </Field>
                                </RadioGroup>
                            </Field>
                        </FieldGroup>
                    </FieldSet>
                </FieldGroup>
            </div>
            <div class="flex gap-2">
                <Button type="submit" :disabled="processing" class="cursor-pointer">
                    <Spinner v-if="processing" class="animate-spin" />
                    <div v-if="supplier">{{ $t('actions.update') }}</div>
                    <div v-else>{{ $t('actions.submit') }}</div>
                </Button>
                <Button type="button" :disabled="processing" variant="destructive" class="cursor-pointer"
                    @click="router.visit(suppliers.index().url)">
                    {{ $t('actions.cancel') }}
                </Button>
            </div>
        </div>
    </Form>
</template>
