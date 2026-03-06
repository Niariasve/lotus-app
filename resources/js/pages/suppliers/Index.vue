<script setup lang='ts'>
    import { Head, router } from '@inertiajs/vue3';
    import { Plus } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import DataTable from '@/components/ui/data-table/DataTable.vue';
    import { columns } from '@/features/suppliers/types/columns';
    import AppLayout from '@/layouts/AppLayout.vue';
    import suppliersRoutes from '@/routes/suppliers';
    import { type Supplier, type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Products',
            href: suppliersRoutes.index().url,
        },
    ];

    defineProps<{
        suppliers: Supplier[],
    }>();
</script>

<template>

    <Head title="Suppliers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Suppliers" description="Manage suppliers" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(suppliersRoutes.create().url)" class="cursor-pointer">
                    <Plus />
                    {{ $t('actions.create') }}
                </Button>
            </div>

            <DataTable :columns="columns" :data="suppliers" :filterable-columns="[
                { value: 'name', label: 'Name' },
            ]" />
        </div>
    </AppLayout>
</template>