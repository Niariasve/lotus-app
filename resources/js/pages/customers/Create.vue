<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { ArrowLeft } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import CustomerForm from '@/features/customers/components/CustomerForm.vue';
    import AppLayout from '@/layouts/AppLayout.vue';
    import customers from '@/routes/customers';
    import { type ContactPlatform, type BreadcrumbItem } from '@/types';

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

    defineProps<{
        contactPlatforms: Array<ContactPlatform>
    }>();

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

            <CustomerForm :contact-platforms="contactPlatforms" />
        </div>
    </AppLayout>
</template>
