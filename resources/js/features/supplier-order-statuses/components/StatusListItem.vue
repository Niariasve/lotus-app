<script setup lang='ts'>
import { Form } from '@inertiajs/vue3';
import { Pencil } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import supplierOrderStatusesRoutes from '@/routes/supplier-order-statuses';
import { type SupplierOrderStatus } from '@/types';
import StatusFormFields from './StatusFormFields.vue';

defineProps<{
    formOptions: {
        preserveScroll: boolean,
        preserveState: boolean,
    },
    isEditing: boolean,
    status: SupplierOrderStatus,
}>();

const emit = defineEmits<{
    cancel: [],
    edit: [statusId: number],
    success: [],
}>();
</script>

<template>
    <div class="rounded-xl border border-border bg-card/60 p-4 shadow-xs">
        <template v-if="isEditing">
            <Form
                v-bind="supplierOrderStatusesRoutes.update.form(status.id)"
                :options="formOptions"
                @success="emit('success')"
                v-slot="{ processing, errors }"
            >
                <StatusFormFields
                    :id-prefix="`status-${status.id}`"
                    :disabled="processing"
                    :errors="errors"
                    :name-default-value="status.name"
                    :description-default-value="status.description ?? ''"
                />

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <Button type="submit" :disabled="processing">
                        Save Changes
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="processing"
                        @click="emit('cancel')"
                    >
                        Cancel
                    </Button>
                </div>
            </Form>
        </template>

        <template v-else>
            <div class="flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-foreground">
                        {{ status.name }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        {{ status.description || 'No description provided.' }}
                    </p>
                </div>

                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    class="shrink-0"
                    @click="emit('edit', status.id)"
                >
                    <Pencil />
                    Edit
                </Button>
            </div>
        </template>
    </div>
</template>
