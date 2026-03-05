<script setup lang='ts'>
    import { Head, router } from '@inertiajs/vue3';
    import { Plus } from 'lucide-vue-next';
    import { onUnmounted } from 'vue';
    import { toast } from 'vue-sonner';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import DataTable from '@/components/ui/data-table/DataTable.vue';
    import { columns } from '@/features/products/types/columns';
    import AppLayout from '@/layouts/AppLayout.vue';
    import productsRoutes from '@/routes/products';
    import { type Product, type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Products',
            href: productsRoutes.index().url,
        },
    ];

    defineProps<{
        products: Product[],
    }>();

    onUnmounted(
        router.on('flash', (event) => {
            if (event.detail.flash.created) {
                toast.success(event.detail.flash.created, {
                    position: 'top-center',
                    duration: 5000,
                });
            }
        })
    )
</script>

<template>

    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Products" description="Manage products" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(productsRoutes.create().url)" class="cursor-pointer">
                    <Plus />
                    {{ $t('actions.create') }}
                </Button>
            </div>

            <DataTable :columns="columns" :data="products" :filterable-columns="[
                { value: 'sku', label: 'SKU' },
                { value: 'name', label: 'Name' },
                { value: 'brand', label: 'Brand' },
                { value: 'line', label: 'Line' }
            ]" />
        </div>
    </AppLayout>
</template>