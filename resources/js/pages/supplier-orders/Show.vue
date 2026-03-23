<script setup lang='ts'>
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate } from '@/lib/utils';
import supplierOrdersRoutes from '@/routes/supplier-orders';
import { type BreadcrumbItem, type SupplierOrder } from '@/types';

const props = defineProps<{
    order: SupplierOrder,
    order_total: number,
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Supplier Orders',
        href: supplierOrdersRoutes.index().url,
    },
    {
        title: props.order.order_number,
        href: supplierOrdersRoutes.show(props.order.id).url,
    },
];

const currencyFormatter = computed(() => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: props.order.supplier?.currency ?? 'USD',
    });
});

const formatMoney = (amount: number | string): string => {
    return currencyFormatter.value.format(Number(amount || 0));
};

const lineTotal = (quantity: number, unitCost: number | string): string => {
    return formatMoney(quantity * Number(unitCost || 0));
};
</script>

<template>
    <Head :title="$t('supplier_orders.show.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-3">
                    <Badge variant="outline">{{ $t('actions.view') }}</Badge>
                    <Heading
                        :title="$t('supplier_orders.show.title')"
                        :description="$t('supplier_orders.show.description')"
                        class="mb-0"
                    />
                </div>

                <div class="flex flex-wrap gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        class="cursor-pointer"
                        @click="router.visit(supplierOrdersRoutes.edit(order.id).url)"
                    >
                        {{ $t('actions.edit') }}
                    </Button>
                    <Button
                        type="button"
                        variant="ghost"
                        class="cursor-pointer"
                        @click="router.visit(supplierOrdersRoutes.index().url)"
                    >
                        {{ $t('actions.cancel') }}
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
                <div class="space-y-6">
                    <Card class="gap-0 overflow-hidden pt-0">
                        <CardHeader class="border-b bg-muted/30 pt-6">
                            <CardTitle>{{ order.order_number }}</CardTitle>
                            <CardDescription>
                                {{ $t('supplier_orders.show.order_details_description') }}
                            </CardDescription>
                        </CardHeader>

                        <CardContent class="pt-6">
                            <div class="grid gap-5 md:grid-cols-2">
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-muted-foreground">
                                        {{ $t('supplier_orders.fields.supplier') }}
                                    </p>
                                    <p class="font-medium text-foreground">
                                        {{ order.supplier?.name ?? `Supplier #${order.supplier_id}` }}
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-muted-foreground">
                                        {{ $t('supplier_orders.fields.status') }}
                                    </p>
                                    <p class="font-medium text-foreground">
                                        {{ order.status?.name ?? $t('supplier_orders.no_status') }}
                                    </p>
                                </div>

                                <div class="space-y-1 md:col-span-2">
                                    <p class="text-sm font-medium text-muted-foreground">
                                        {{ $t('supplier_orders.fields.tracking') }}
                                    </p>
                                    <p class="whitespace-pre-wrap font-medium text-foreground">
                                        {{ order.tracking ?? $t('supplier_orders.no_tracking') }}
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-muted-foreground">
                                        {{ $t('supplier_orders.fields.ordered_at') }}
                                    </p>
                                    <p class="font-medium text-foreground">
                                        {{ formatDate(order.ordered_at) }}
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-muted-foreground">
                                        {{ $t('supplier_orders.fields.shipped_at') }}
                                    </p>
                                    <p class="font-medium text-foreground">
                                        {{ formatDate(order.shipped_at) }}
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-muted-foreground">
                                        {{ $t('supplier_orders.fields.arrived_at') }}
                                    </p>
                                    <p class="font-medium text-foreground">
                                        {{ formatDate(order.arrived_at) }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="gap-0 overflow-hidden pt-0">
                        <CardHeader class="border-b bg-muted/30 pt-6">
                            <CardTitle>{{ $t('supplier_orders.fields.items') }}</CardTitle>
                            <CardDescription>
                                {{ $t('supplier_orders.show.items_description') }}
                            </CardDescription>
                        </CardHeader>

                        <CardContent class="pt-6">
                            <div class="overflow-hidden rounded-xl border border-border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Product</TableHead>
                                            <TableHead>{{ $t('supplier_orders.fields.quantity') }}</TableHead>
                                            <TableHead>{{ $t('supplier_orders.fields.unit_cost') }}</TableHead>
                                            <TableHead class="text-right">
                                                {{ $t('supplier_orders.fields.line_total') }}
                                            </TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="item in order.items ?? []" :key="item.id">
                                            <TableCell class="font-medium text-foreground">
                                                {{ item.product?.name ?? `Product #${item.product_id}` }}
                                            </TableCell>
                                            <TableCell>{{ item.quantity }}</TableCell>
                                            <TableCell>{{ formatMoney(item.unit_cost) }}</TableCell>
                                            <TableCell class="text-right font-medium text-foreground">
                                                {{ lineTotal(item.quantity, item.unit_cost) }}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="xl:sticky xl:top-6 xl:self-start">
                    <Card class="gap-0 overflow-hidden pt-0">
                        <CardHeader class="border-b bg-muted/30 pt-6">
                            <CardTitle>{{ $t('supplier_orders.show.summary') }}</CardTitle>
                            <CardDescription>
                                {{ $t('supplier_orders.show.summary_description') }}
                            </CardDescription>
                        </CardHeader>

                        <CardContent class="space-y-4 pt-6">
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-muted-foreground">
                                        {{ $t('supplier_orders.fields.items') }}
                                    </span>
                                    <span class="font-medium text-foreground">
                                        {{ order.items?.length ?? 0 }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-muted-foreground">
                                        {{ $t('supplier_orders.fields.status') }}
                                    </span>
                                    <span class="text-right font-medium text-foreground">
                                        {{ order.status?.name ?? $t('supplier_orders.no_status') }}
                                    </span>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between gap-3">
                                <span class="font-medium text-foreground">
                                    {{ $t('supplier_orders.fields.order_total') }}
                                </span>
                                <span class="text-lg font-semibold text-foreground">
                                    {{ formatMoney(order_total) }}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
