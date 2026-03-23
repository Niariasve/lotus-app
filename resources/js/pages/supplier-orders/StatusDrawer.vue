<script setup lang='ts'>
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import CreateStatusCard from '@/features/supplier-order-statuses/components/CreateStatusCard.vue';
import StatusListItem from '@/features/supplier-order-statuses/components/StatusListItem.vue';
import { useStatusDrawer } from '@/features/supplier-order-statuses/composables/useStatusDrawer';
import { type SupplierOrderStatus } from '@/types';

defineProps<{
    statuses: SupplierOrderStatus[],
    open: boolean,
}>();

const emit = defineEmits<{
    'update:open': [value: boolean],
}>();

const {
    editingStatusId,
    formOptions,
    handleCreateSuccess,
    handleUpdateSuccess,
    closeDrawer,
    startEditing,
    stopEditing,
    syncDrawerOpenState,
} = useStatusDrawer({ emit });
</script>

<template>
    <Sheet :open="open" @update:open="syncDrawerOpenState">
        <SheetContent
            side="right"
            class="flex h-full w-full max-w-2xl flex-col gap-0 border-l bg-background p-0 sm:max-w-2xl"
        >
            <SheetHeader class="border-b px-6 py-5">
                <SheetTitle>Manage Statuses</SheetTitle>
                <SheetDescription>
                    Create supplier order statuses and update existing lifecycle labels.
                </SheetDescription>
            </SheetHeader>

            <div class="flex-1 overflow-y-auto px-6 py-6">
                <div class="space-y-6">
                    <section class="space-y-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-semibold tracking-tight text-foreground">
                                Existing Statuses
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                Keep lifecycle labels clear so order states stay consistent.
                            </p>
                        </div>

                        <div v-if="statuses.length" class="space-y-3">
                            <StatusListItem
                                v-for="status in statuses"
                                :key="status.id"
                                :form-options="formOptions"
                                :is-editing="editingStatusId === status.id"
                                :status="status"
                                @cancel="stopEditing"
                                @edit="startEditing"
                                @success="handleUpdateSuccess"
                            />
                        </div>

                        <div
                            v-else
                            class="rounded-xl border border-dashed border-border bg-muted/30 px-4 py-6 text-sm text-muted-foreground"
                        >
                            No statuses yet. Create the first one below.
                        </div>
                    </section>

                    <CreateStatusCard
                        :form-options="formOptions"
                        @close="closeDrawer"
                        @success="handleCreateSuccess"
                    />
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
