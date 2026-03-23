<script setup lang='ts'>
    import { Form, Head, router } from '@inertiajs/vue3';
    import { PackagePlus, ReceiptText, Trash2 } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import InputError from '@/components/InputError.vue';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardFooter,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import {
        Field,
        FieldDescription,
        FieldGroup,
        FieldLabel,
        FieldLegend,
        FieldSet,
    } from '@/components/ui/field';
    import { Input } from '@/components/ui/input';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';
    import { Separator } from '@/components/ui/separator';
    import { Textarea } from '@/components/ui/textarea';
    import { useCreateSupplierOrderForm } from '@/features/supplier-orders/composables/useCreateSupplierOrderForm';
    import AppLayout from '@/layouts/AppLayout.vue';
    import supplierOrdersRoutes from '@/routes/supplier-orders';
    import {
        type BreadcrumbItem,
        type Product,
        type Supplier,
        type SupplierOrderStatus,
    } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Supplier Orders',
            href: supplierOrdersRoutes.index().url,
        },
        {
            title: 'Create',
            href: supplierOrdersRoutes.create().url,
        },
    ];

    const props = defineProps<{
        products: Product[],
        suppliers: Supplier[],
        statuses: SupplierOrderStatus[],
    }>();

    const {
        addItem,
        EMPTY_STATUS_VALUE,
        formatMoney,
        itemCount,
        itemError,
        itemName,
        items,
        lineTotal,
        orderTotal,
        removeItem,
        selectedStatus,
        selectedSupplier,
        setItemProduct,
        setStatusId,
        setSupplierId,
        statusId,
        statusSelectValue,
        supplierId,
    } = useCreateSupplierOrderForm(props);
</script>

<template>

    <Head :title="$t('supplier_orders.create.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-3">
                    <Badge variant="outline">{{ $t('actions.create') }}</Badge>
                    <Heading :title="$t('supplier_orders.create.title')"
                        :description="$t('supplier_orders.create.description')" class="mb-0" />
                </div>

                <Button type="button" variant="ghost" class="cursor-pointer self-start"
                    @click="router.visit(supplierOrdersRoutes.index().url)">
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <Form v-bind="supplierOrdersRoutes.store.form()" :options="{ preserveScroll: true }"
                v-slot="{ processing, errors }">
                <input type="hidden" name="supplier_id" :value="supplierId" />
                <input type="hidden" name="status_id" :value="statusId" />

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="space-y-6">
                        <Card class="gap-0 overflow-hidden pt-0">
                            <CardHeader class="border-b bg-muted/30 pt-6">
                                <CardTitle>
                                    {{ $t('supplier_orders.create.order_details') }}
                                </CardTitle>
                                <CardDescription>
                                    {{ $t('supplier_orders.create.order_details_description') }}
                                </CardDescription>
                            </CardHeader>

                            <CardContent class="pt-6">
                                <FieldGroup>
                                    <FieldSet>
                                        <FieldLegend class="sr-only">
                                            {{ $t('supplier_orders.create.order_details') }}
                                        </FieldLegend>

                                        <div class="grid gap-5 md:grid-cols-2">
                                            <Field>
                                                <FieldLabel for="order_number">
                                                    {{ $t('supplier_orders.fields.order_number') }}
                                                </FieldLabel>
                                                <Input id="order_number" name="order_number" :disabled="processing"
                                                    required />
                                                <InputError :message="errors.order_number" />
                                            </Field>

                                            <Field>
                                                <FieldLabel for="supplier_id_trigger">
                                                    {{ $t('supplier_orders.fields.supplier') }}
                                                </FieldLabel>
                                                <Select :model-value="supplierId" @update:model-value="setSupplierId"
                                                    :disabled="processing">
                                                    <SelectTrigger id="supplier_id_trigger" class="w-full">
                                                        <SelectValue
                                                            :placeholder="$t('supplier_orders.fields.supplier')" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem v-for="supplier in suppliers" :key="supplier.id"
                                                            :value="String(supplier.id)">
                                                            {{ supplier.name }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <InputError :message="errors.supplier_id" />
                                            </Field>

                                            <Field>
                                                <FieldLabel for="status_id_trigger">
                                                    {{ $t('supplier_orders.fields.status') }}
                                                </FieldLabel>
                                                <Select :model-value="statusSelectValue"
                                                    @update:model-value="setStatusId" :disabled="processing">
                                                    <SelectTrigger id="status_id_trigger" class="w-full">
                                                        <SelectValue
                                                            :placeholder="$t('supplier_orders.fields.status')" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem :value="EMPTY_STATUS_VALUE">
                                                            {{ $t('supplier_orders.create.empty_status') }}
                                                        </SelectItem>
                                                        <SelectItem v-for="status in statuses" :key="status.id"
                                                            :value="String(status.id)">
                                                            {{ status.name }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <FieldDescription>
                                                    {{ $t('supplier_orders.create.status_description') }}
                                                </FieldDescription>
                                                <InputError :message="errors.status_id" />
                                            </Field>

                                            <Field class="md:col-span-2">
                                                <FieldLabel for="tracking">
                                                    {{ $t('supplier_orders.fields.tracking') }}
                                                </FieldLabel>
                                                <Textarea id="tracking" name="tracking" :disabled="processing"
                                                    rows="4" />
                                                <InputError :message="errors.tracking" />
                                            </Field>

                                            <Field>
                                                <FieldLabel for="ordered_at">
                                                    {{ $t('supplier_orders.fields.ordered_at') }}
                                                </FieldLabel>
                                                <Input id="ordered_at" name="ordered_at" type="date"
                                                    :disabled="processing" />
                                                <InputError :message="errors.ordered_at" />
                                            </Field>

                                            <Field>
                                                <FieldLabel for="shipped_at">
                                                    {{ $t('supplier_orders.fields.shipped_at') }}
                                                </FieldLabel>
                                                <Input id="shipped_at" name="shipped_at" type="date"
                                                    :disabled="processing" />
                                                <InputError :message="errors.shipped_at" />
                                            </Field>

                                            <Field class="md:col-span-2">
                                                <FieldLabel for="arrived_at">
                                                    {{ $t('supplier_orders.fields.arrived_at') }}
                                                </FieldLabel>
                                                <Input id="arrived_at" name="arrived_at" type="date"
                                                    :disabled="processing" required />
                                                <InputError :message="errors.arrived_at" />
                                            </Field>
                                        </div>
                                    </FieldSet>
                                </FieldGroup>
                            </CardContent>
                        </Card>

                        <Card class="gap-0 overflow-hidden pt-0">
                            <CardHeader class="border-b bg-muted/30 pt-6">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="space-y-1">
                                        <CardTitle>{{ $t('supplier_orders.fields.items') }}</CardTitle>
                                        <CardDescription>
                                            {{ $t('supplier_orders.create.items_description') }}
                                        </CardDescription>
                                    </div>

                                    <Button type="button" variant="outline" class="cursor-pointer"
                                        :disabled="processing" @click="addItem">
                                        <PackagePlus />
                                        {{ $t('supplier_orders.create.add_item') }}
                                    </Button>
                                </div>
                            </CardHeader>

                            <CardContent class="space-y-4 pt-6">
                                <div v-for="(item, index) in items" :key="item.key"
                                    class="rounded-2xl border border-border bg-muted/20 p-4">
                                    <input type="hidden" :name="itemName(index, 'product_id')"
                                        :value="item.productId" />

                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <Badge variant="secondary">
                                            {{ $t('supplier_orders.fields.items') }} {{ index + 1 }}
                                        </Badge>

                                        <Button type="button" variant="ghost" size="sm"
                                            class="cursor-pointer text-destructive"
                                            :disabled="processing || items.length === 1" @click="removeItem(item.key)">
                                            <Trash2 />
                                            {{ $t('supplier_orders.create.remove_item') }}
                                        </Button>
                                    </div>

                                    <div
                                        class="grid gap-4 md:grid-cols-[minmax(0,1.5fr)_minmax(0,0.7fr)_minmax(0,0.8fr)]">
                                        <Field>
                                            <FieldLabel :for="`product_${item.key}`">
                                                Product
                                            </FieldLabel>
                                            <Select :model-value="item.productId"
                                                @update:model-value="setItemProduct(item.key, $event)"
                                                :disabled="processing">
                                                <SelectTrigger :id="`product_${item.key}`" class="w-full">
                                                    <SelectValue placeholder="Product" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="product in products" :key="product.id"
                                                        :value="String(product.id)">
                                                        {{ product.name }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            <InputError :message="itemError(errors, index, 'product_id')" />
                                        </Field>

                                        <Field>
                                            <FieldLabel :for="`quantity_${item.key}`">
                                                {{ $t('supplier_orders.fields.quantity') }}
                                            </FieldLabel>
                                            <Input :id="`quantity_${item.key}`" :name="itemName(index, 'quantity')"
                                                v-model="item.quantity" type="number" min="1" step="1"
                                                :disabled="processing" required />
                                            <InputError :message="itemError(errors, index, 'quantity')" />
                                        </Field>

                                        <Field>
                                            <FieldLabel :for="`unit_cost_${item.key}`">
                                                {{ $t('supplier_orders.fields.unit_cost') }}
                                            </FieldLabel>
                                            <Input :id="`unit_cost_${item.key}`" :name="itemName(index, 'unit_cost')"
                                                v-model="item.unitCost" type="number" min="0" step="0.01"
                                                :disabled="processing" required />
                                            <InputError :message="itemError(errors, index, 'unit_cost')" />
                                        </Field>
                                    </div>

                                    <Separator class="my-4" />

                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <span class="text-muted-foreground">
                                            {{ $t('supplier_orders.fields.line_total') }}
                                        </span>
                                        <span class="font-semibold text-foreground">
                                            {{ formatMoney(lineTotal(item)) }}
                                        </span>
                                    </div>
                                </div>

                                <InputError :message="errors.items" />
                            </CardContent>
                        </Card>
                    </div>

                    <div class="xl:sticky xl:top-6 xl:self-start">
                        <Card class="gap-0 overflow-hidden pt-0">
                            <CardHeader class="border-b bg-muted/30 pt-6">
                                <CardTitle>{{ $t('supplier_orders.create.summary') }}</CardTitle>
                                <CardDescription>
                                    {{ $t('supplier_orders.create.summary_description') }}
                                </CardDescription>
                            </CardHeader>

                            <CardContent class="space-y-4 pt-6">
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-muted-foreground">
                                            {{ $t('supplier_orders.fields.supplier') }}
                                        </span>
                                        <span class="text-right font-medium text-foreground">
                                            {{ selectedSupplier?.name ?? '—' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-muted-foreground">
                                            {{ $t('supplier_orders.fields.status') }}
                                        </span>
                                        <span class="text-right font-medium text-foreground">
                                            {{ selectedStatus?.name ?? $t('supplier_orders.create.empty_status') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-muted-foreground">
                                            {{ $t('supplier_orders.fields.items') }}
                                        </span>
                                        <span class="font-medium text-foreground">
                                            {{ itemCount }}
                                        </span>
                                    </div>
                                </div>

                                <Separator />

                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2 text-foreground">
                                        <ReceiptText class="size-4 text-muted-foreground" />
                                        <span class="font-medium">
                                            {{ $t('supplier_orders.fields.order_total') }}
                                        </span>
                                    </div>
                                    <span class="text-lg font-semibold text-foreground">
                                        {{ formatMoney(orderTotal) }}
                                    </span>
                                </div>
                            </CardContent>

                            <CardFooter class="flex-col gap-3 border-t bg-muted/20 pt-6">
                                <Button type="submit" class="w-full cursor-pointer" :disabled="processing">
                                    {{ $t('actions.submit') }}
                                </Button>
                                <Button type="button" variant="outline" class="w-full cursor-pointer"
                                    :disabled="processing" @click="router.visit(supplierOrdersRoutes.index().url)">
                                    {{ $t('actions.cancel') }}
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
