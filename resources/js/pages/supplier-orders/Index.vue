<script setup lang='ts'>
import { Head, router } from '@inertiajs/vue3';
import { ListFilter, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { columns } from '@/features/supplier-orders/types/columns';
import AppLayout from '@/layouts/AppLayout.vue';
import supplierOrdersRoutes from '@/routes/supplier-orders';
import { type BreadcrumbItem, type SupplierOrder, type SupplierOrderStatus } from '@/types';
import StatusDrawer from './StatusDrawer.vue';

type PaginatedSupplierOrders = {
    data: SupplierOrder[],
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Supplier Orders',
        href: supplierOrdersRoutes.index().url,
    },
];

const props = defineProps<{
    orders: SupplierOrder[] | PaginatedSupplierOrders,
    statuses: SupplierOrderStatus[],
}>();

const isStatusDrawerOpen = ref(false);

const orderRows = computed(() => {
    return Array.isArray(props.orders) ? props.orders : props.orders.data;
});
</script>

<template>
    <Head :title="$t('supplier_orders.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading
                :title="$t('supplier_orders.title')"
                :description="$t('supplier_orders.description')"
                class="mb-0"
            />

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <Button
                    type="button"
                    variant="outline"
                    class="cursor-pointer"
                    @click="isStatusDrawerOpen = true"
                >
                    <ListFilter />
                    Manage Statuses
                </Button>

                <Button
                    type="button"
                    class="cursor-pointer"
                    @click="router.visit(supplierOrdersRoutes.create().url)"
                >
                    <Plus />
                    Create Order
                </Button>
            </div>

            <DataTable
                :columns="columns"
                :data="orderRows"
                :filterable-columns="[
                    { value: 'order_number', label: 'Order Number' },
                    { value: 'supplier', label: 'Supplier' },
                    { value: 'status', label: 'Status' },
                    { value: 'tracking', label: 'Tracking' },
                ]"
            />

            <StatusDrawer
                :open="isStatusDrawerOpen"
                :statuses="statuses"
                @update:open="isStatusDrawerOpen = $event"
            />
        </div>
    </AppLayout>
</template>
