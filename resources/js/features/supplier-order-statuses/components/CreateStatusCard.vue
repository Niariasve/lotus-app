<script setup lang='ts'>
import { Form } from '@inertiajs/vue3';
import { Plus, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import supplierOrderStatusesRoutes from '@/routes/supplier-order-statuses';
import StatusFormFields from './StatusFormFields.vue';

defineProps<{
    formOptions: {
        preserveScroll: boolean,
        preserveState: boolean,
    },
}>();

const emit = defineEmits<{
    close: [],
    success: [],
}>();
</script>

<template>
    <section class="space-y-4 rounded-2xl border border-border bg-muted/20 p-5">
        <div class="space-y-1">
            <h3 class="text-sm font-semibold tracking-tight text-foreground">
                New Status
            </h3>
            <p class="text-sm text-muted-foreground">
                Add a reusable status for supplier orders.
            </p>
        </div>

        <Form
            v-bind="supplierOrderStatusesRoutes.store.form()"
            :options="formOptions"
            reset-on-success
            @success="emit('success')"
            v-slot="{ processing, errors }"
        >
            <StatusFormFields
                id-prefix="new-status"
                :disabled="processing"
                :errors="errors"
                name-placeholder="Placed"
                description-placeholder="Order is confirmed with the supplier and waiting for shipment updates."
            />

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <Button type="submit" :disabled="processing">
                    <Plus />
                    Create Status
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    :disabled="processing"
                    @click="emit('close')"
                >
                    <X />
                    Close
                </Button>
            </div>
        </Form>
    </section>
</template>
