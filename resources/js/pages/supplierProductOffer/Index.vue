<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { Plus } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import DataTable from '@/components/ui/data-table/DataTable.vue';
    import { columns } from '@/features/supplier-product-offers/types/columns';
    import AppLayout from '@/layouts/AppLayout.vue';
    import supplierProductOfferRoutes from '@/routes/supplier-product-offer';
    import { type SupplierProductOffer, type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Supplier Product Offers',
            href: supplierProductOfferRoutes.index().url,
        },
    ];

    defineProps<{
        supplierProductOffers: SupplierProductOffer[],
    }>();
</script>

<template>
    <Head title="Supplier Product Offers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Supplier Product Offers" description="Manage supplier pricing for each product"
                class="mb-0" />

            <div class="flex items-center justify-end">
                <Button @click="router.visit(supplierProductOfferRoutes.create().url)" class="cursor-pointer">
                    <Plus />
                    {{ $t('actions.create') }}
                </Button>
            </div>

            <DataTable :columns="columns" :data="supplierProductOffers" :filterable-columns="[
                { value: 'supplier', label: 'Supplier' },
                { value: 'product', label: 'Product' },
                { value: 'availability', label: 'Availability' },
            ]" />
        </div>
    </AppLayout>
</template>
