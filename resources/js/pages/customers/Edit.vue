<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { ArrowLeft } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import CustomerForm from '@/features/customers/components/CustomerForm.vue';
    import AppLayout from '@/layouts/AppLayout.vue';
    import customers from '@/routes/customers';
    import { type ContactPlatform, type BreadcrumbItem, type Customer, type CustomerContact } from '@/types';

    const props = defineProps<{
        customer: Customer;
        contactPlatforms: Array<ContactPlatform>,
        customerContacts: Array<CustomerContact>,
        primary: ContactPlatform,
    }>();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Customers',
            href: customers.index().url,
        },
        {
            title: 'Edit',
            href: customers.edit(props.customer.id).url,
        }
    ];

</script>

<template>

    <Head :title="`Edit ${customer.full_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Create Customer" description="Create customer" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(customers.index().url)" class="cursor-pointer" variant="destructive">
                    <ArrowLeft />
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <CustomerForm :contact-platforms="contactPlatforms" :customer="customer"
                :customer-contacts="customerContacts" :primary="primary" />
        </div>
    </AppLayout>
</template>
