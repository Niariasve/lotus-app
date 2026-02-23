<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { Form } from '@inertiajs/vue3';
    import { ArrowLeft } from 'lucide-vue-next';
    import CustomerController from '@/actions/App/Http/Controllers/CustomerController';
    import Heading from '@/components/Heading.vue';
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
    import AppLayout from '@/layouts/AppLayout.vue';
    import customers from '@/routes/customers';
    import { type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Customers',
            href: customers.index().url,
        },
        {
            title: 'Create',
            href: customers.create().url,
        }
    ];

</script>

<template>

    <Head title="Create Customer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Create Customer" description="Create customer" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(customers.index().url)" class="cursor-pointer" variant="destructive">
                    <ArrowLeft />
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <Form v-bind="CustomerController.store.form()" v-slot="{ processing, errors }">
                <div class="flex md:max-w-3xl p-4 space-y-3 border rounded-xl mx-auto">
                    <FieldGroup>
                        <FieldSet>
                            <FieldLegend>Customer Information</FieldLegend>
                            <FieldDescription>
                                Customer's personal information
                            </FieldDescription>
                            <FieldGroup>
                                <Field>
                                    <FieldLabel for="full_name">Full name *</FieldLabel>
                                    <Input
                                        id="full_name"
                                        name="full_name"
                                        placeholder="Hayden Christensen"
                                    />
                                    <InputError :message="errors.full_name" />
                                </Field>
                                <Field>
                                    <FieldLabel for="city">City</FieldLabel>
                                    <Input 
                                        id="city"
                                        name="city"
                                        placeholder="Guayaquil"
                                    />
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                        <FieldSeparator />
                        <FieldSet>
                            <FieldLegend>Contact Information</FieldLegend>
                            <FieldDescription>
                                Customer's contact information
                            </FieldDescription>
                            <FieldGroup>
                                <Field>
                                    <FieldLabel for="email">Email</FieldLabel>
                                    <Input 
                                        id="email"
                                        name="email"
                                        placeholder="user@example.com"
                                    />
                                </Field>
                                <Field>
                                    <FieldLabel for="phone">Phone</FieldLabel>
                                    <Input
                                        id="phone"
                                        name="phone"
                                        placeholder="099999999"
                                    />
                                </Field>
                                <Field orientation="horizontal">
                                    <Button type="submit" :disabled="processing">
                                        <Spinner v-if="processing" class="animate-spin" />
                                        {{ $t('actions.submit') }}
                                    </Button>
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                    </FieldGroup>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
