<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { ArrowLeft } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import SupplierProductOfferForm from '@/features/supplier-product-offers/components/SupplierProductOfferForm.vue';
    import AppLayout from '@/layouts/AppLayout.vue';
    import supplierProductOfferRoutes from '@/routes/supplier-product-offer';
    import { type Product, type Supplier, type SupplierProductOffer, type BreadcrumbItem } from '@/types';

    const props = defineProps<{
        supplierProductOffer: SupplierProductOffer,
        suppliers: Supplier[],
        products: Product[],
    }>();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Supplier Product Offers',
            href: supplierProductOfferRoutes.index().url,
        },
        {
            title: 'Edit',
            href: supplierProductOfferRoutes.edit(props.supplierProductOffer.id).url,
        },
    ];
</script>

<template>
    <Head :title="`Edit Offer #${supplierProductOffer.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading :title="`Edit Offer #${supplierProductOffer.id}`" description="Edit supplier pricing for a product"
                class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(supplierProductOfferRoutes.index().url)" class="cursor-pointer"
                    variant="destructive">
                    <ArrowLeft />
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <SupplierProductOfferForm :supplier-product-offer="supplierProductOffer" :suppliers="suppliers"
                :products="products" />
        </div>
    </AppLayout>
</template>
