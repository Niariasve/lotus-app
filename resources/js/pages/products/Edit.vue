<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { ArrowLeft } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import ProductForm from '@/features/products/components/ProductForm.vue';
    import AppLayout from '@/layouts/AppLayout.vue';
    import products from '@/routes/products';
    import { type Product, type BreadcrumbItem } from '@/types';

    const props = defineProps<{
        product: Product,
    }>();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Products',
            href: products.index().url,
        },
        {
            title: 'Edit',
            href: products.edit(props.product.id).url,
        }
    ];
</script>

<template>

    <Head :title="`Edit ${product.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading :title="`Edit ${product.name}`" description="Edit product" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(products.index().url)" class="cursor-pointer" variant="destructive">
                    <ArrowLeft />
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <ProductForm :product="product" />
        </div>
    </AppLayout>
</template>