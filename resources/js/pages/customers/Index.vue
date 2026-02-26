<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { Plus } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import DataTable from '@/components/ui/data-table/DataTable.vue';
    import { columns } from '@/features/customers/types/columns';
    import AppLayout from '@/layouts/AppLayout.vue';
    import customersRoutes from '@/routes/customers';
    import { type Customer, type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Customers',
            href: customersRoutes.index().url,
        },
    ];

    defineProps<{
        customers: Customer[],
    }>();
</script>

<template>

    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Customers" description="Manage customers" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(customersRoutes.create().url)" class="cursor-pointer">
                    <Plus />
                    {{ $t('actions.create') }}
                </Button>
            </div>

            <DataTable :columns="columns" :data="customers" :filterable-columns="[
                { value: 'full_name', label: 'Full Name' },
                { value: 'primary_contact_platform', label: 'Contact Platform' }
            ]" />
        </div>
    </AppLayout>
</template>
