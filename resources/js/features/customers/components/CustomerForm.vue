<script setup lang="ts">
    import { Form, router } from '@inertiajs/vue3';
    import CustomerController from '@/actions/App/Http/Controllers/CustomerController';
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
    import customers from '@/routes/customers';
    import { type Customer, type ContactPlatform } from '@/types';


    defineProps<{
        customer?: Customer,
        contactPlatforms: Array<ContactPlatform>
    }>();
</script>

<template>
    <Form v-bind="CustomerController.store.form()" v-slot="{ processing, errors }">
        <div class="flex flex-col gap-4 md:max-w-4xl mx-auto">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-3 p-4 space-y-3 border rounded-xl">
                    <FieldGroup>
                        <FieldSet>
                            <FieldLegend>Customer Information</FieldLegend>
                            <FieldDescription>
                                Customer's personal information
                            </FieldDescription>
                            <FieldGroup>
                                <Field>
                                    <FieldLabel for="full_name">Full name *</FieldLabel>
                                    <Input id="full_name" name="full_name" placeholder="Hayden Christensen" />
                                    <InputError :message="errors.full_name" />
                                </Field>
                                <Field>
                                    <FieldLabel for="city">City</FieldLabel>
                                    <Input id="city" name="city" placeholder="Guayaquil" />
                                    <InputError :message="errors.city" />
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
                                    <Input id="email" name="email" placeholder="user@example.com" />
                                    <InputError :message="errors.email" />
                                </Field>
                                <Field>
                                    <FieldLabel for="phone">Phone</FieldLabel>
                                    <Input id="phone" name="phone" placeholder="099999999" />
                                    <InputError :message="errors.phone" />
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                    </FieldGroup>
                </div>
                <ScrollArea class="flex-1 p-4 h-150 space-y-3 border rounded-xl">
                    <FieldGroup>
                        <FieldSet>
                            <FieldLegend>Contact Platforms</FieldLegend>
                            <FieldDescription class="text-wrap">
                                Customer's contact platforms
                            </FieldDescription>
                            <FieldGroup>
                                <Field v-for="(platform) in contactPlatforms" :key="platform.id">
                                    <FieldLabel :for="platform.slug">{{ platform.name }}</FieldLabel>
                                    <Input :id="platform.slug" :name="`platform[${platform.slug}]`"
                                        :placeholder="platform.name" />
                                    <InputError :message="errors[`platform.${platform.slug}`]" />
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                        <FieldSeparator />
                        <FieldSet>
                            <FieldLegend>Primary Platform</FieldLegend>
                            <FieldDescription>
                                Primary comunication platform
                            </FieldDescription>
                            <InputError :message="errors.primary_platform" />
                            <RadioGroup name="primary_platform">
                                <Field v-for="(platform) in contactPlatforms" :key="platform.id">
                                    <div class="flex items-center space-x-2">
                                        <RadioGroupItem :id="`${platform.id}`" :value="platform.slug" />
                                        <FieldLabel :for="`${platform.id}`" class="text-sm">
                                            {{ platform.name }}
                                        </FieldLabel>
                                    </div>
                                </Field>
                            </RadioGroup>
                        </FieldSet>
                    </FieldGroup>
                </ScrollArea>

            </div>
            <div class="flex gap-2">
                <Button type="submit" :disabled="processing" class="cursor-pointer">
                    <Spinner v-if="processing" class="animate-spin" />
                    {{ $t('actions.submit') }}
                </Button>
                <Button type="button" :disabled="processing" variant="destructive" class="cursor-pointer"
                    @click="router.visit(customers.index().url)">
                    {{ $t('actions.cancel') }}
                </Button>
            </div>
        </div>
    </Form>
</template>