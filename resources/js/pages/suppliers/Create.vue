<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { ArrowLeft } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import SupplierForm from '@/features/suppliers/components/SupplierForm.vue';
    import AppLayout from '@/layouts/AppLayout.vue';
    import suppliers from '@/routes/suppliers';
    import { type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Suppliers',
            href: suppliers.index().url,
        },
        {
            title: 'Create',
            href: suppliers.create().url,
        }
    ];
</script>

<template>

    <Head title="Create Supplier" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Create Supplier" description="Create supplier" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(suppliers.index().url)" class="cursor-pointer" variant="destructive">
                    <ArrowLeft />
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <SupplierForm />
        </div>
    </AppLayout>
</template>