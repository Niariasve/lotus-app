<script setup lang='ts'>
import type { AcceptableValue } from 'reka-ui';
import InputError from '@/components/InputError.vue';
import {
    Card,
    CardContent,
    CardDescription,
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
import { Textarea } from '@/components/ui/textarea';
import { type Supplier, type SupplierOrderStatus } from '@/types';

defineProps<{
    disabled: boolean,
    emptyStatusValue: string,
    errors: Record<string, string>,
    statusId: string,
    statusSelectValue: string,
    statuses: SupplierOrderStatus[],
    supplierId: string,
    suppliers: Supplier[],
}>();

const emit = defineEmits<{
    'update:status-id': [value: AcceptableValue],
    'update:supplier-id': [value: AcceptableValue],
}>();
</script>

<template>
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
                            <Input id="order_number" name="order_number" :disabled="disabled" required />
                            <InputError :message="errors.order_number" />
                        </Field>

                        <Field>
                            <FieldLabel for="supplier_id_trigger">
                                {{ $t('supplier_orders.fields.supplier') }}
                            </FieldLabel>
                            <Select
                                :model-value="supplierId"
                                :disabled="disabled"
                                @update:model-value="emit('update:supplier-id', $event)"
                            >
                                <SelectTrigger id="supplier_id_trigger" class="w-full">
                                    <SelectValue :placeholder="$t('supplier_orders.fields.supplier')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="supplier in suppliers"
                                        :key="supplier.id"
                                        :value="String(supplier.id)"
                                    >
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
                            <Select
                                :model-value="statusSelectValue"
                                :disabled="disabled"
                                @update:model-value="emit('update:status-id', $event)"
                            >
                                <SelectTrigger id="status_id_trigger" class="w-full">
                                    <SelectValue :placeholder="$t('supplier_orders.fields.status')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="emptyStatusValue">
                                        {{ $t('supplier_orders.create.empty_status') }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="status in statuses"
                                        :key="status.id"
                                        :value="String(status.id)"
                                    >
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
                            <Textarea id="tracking" name="tracking" :disabled="disabled" rows="4" />
                            <InputError :message="errors.tracking" />
                        </Field>

                        <Field>
                            <FieldLabel for="ordered_at">
                                {{ $t('supplier_orders.fields.ordered_at') }}
                            </FieldLabel>
                            <Input id="ordered_at" name="ordered_at" type="date" :disabled="disabled" />
                            <InputError :message="errors.ordered_at" />
                        </Field>

                        <Field>
                            <FieldLabel for="shipped_at">
                                {{ $t('supplier_orders.fields.shipped_at') }}
                            </FieldLabel>
                            <Input id="shipped_at" name="shipped_at" type="date" :disabled="disabled" />
                            <InputError :message="errors.shipped_at" />
                        </Field>

                        <Field class="md:col-span-2">
                            <FieldLabel for="arrived_at">
                                {{ $t('supplier_orders.fields.arrived_at') }}
                            </FieldLabel>
                            <Input id="arrived_at" name="arrived_at" type="date" :disabled="disabled" required />
                            <InputError :message="errors.arrived_at" />
                        </Field>
                    </div>
                </FieldSet>
            </FieldGroup>
        </CardContent>
    </Card>
</template>
