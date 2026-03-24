<script setup lang='ts'>
import { ReceiptText } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { type Supplier, type SupplierOrderStatus } from '@/types';

defineProps<{
    disabled: boolean,
    formatMoney: (amount: number) => string,
    itemCount: number,
    orderTotal: number,
    selectedStatus: SupplierOrderStatus | null,
    selectedSupplier: Supplier | null,
}>();

const emit = defineEmits<{
    cancel: [],
}>();
</script>

<template>
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

            <div class="flex items-center justify-between gap-3 mb-4">
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
            <Button type="submit" class="w-full cursor-pointer" :disabled="disabled">
                {{ $t('actions.submit') }}
            </Button>
            <Button type="button" variant="outline" class="w-full cursor-pointer" :disabled="disabled" @click="emit('cancel')">
                {{ $t('actions.cancel') }}
            </Button>
        </CardFooter>
    </Card>
</template>
