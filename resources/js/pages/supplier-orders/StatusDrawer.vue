<script setup lang='ts'>
import { Form } from '@inertiajs/vue3';
import { Pencil, Plus, X } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Textarea } from '@/components/ui/textarea';
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
    createStatusForm,
    editingStatusId,
    formOptions,
    handleCreateSuccess,
    handleUpdateSuccess,
    closeDrawer,
    startEditing,
    stopEditing,
    syncDrawerOpenState,
    updateStatusForm,
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
                            <div
                                v-for="status in statuses"
                                :key="status.id"
                                class="rounded-xl border border-border bg-card/60 p-4 shadow-xs"
                            >
                                <template v-if="editingStatusId === status.id">
                                    <Form
                                        v-bind="updateStatusForm(status.id)"
                                        :options="formOptions"
                                        @success="handleUpdateSuccess"
                                        v-slot="{ processing, errors }"
                                    >
                                        <div class="space-y-4">
                                            <div class="space-y-2">
                                                <Label :for="`status-name-${status.id}`">Name</Label>
                                                <Input
                                                    :id="`status-name-${status.id}`"
                                                    name="name"
                                                    :default-value="status.name"
                                                    :disabled="processing"
                                                    maxlength="150"
                                                    required
                                                />
                                                <InputError :message="errors.name" />
                                            </div>

                                            <div class="space-y-2">
                                                <Label :for="`status-description-${status.id}`">
                                                    Description
                                                </Label>
                                                <Textarea
                                                    :id="`status-description-${status.id}`"
                                                    name="description"
                                                    :default-value="status.description ?? ''"
                                                    :disabled="processing"
                                                    rows="3"
                                                />
                                                <InputError :message="errors.description" />
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <Button type="submit" :disabled="processing">
                                                    Save Changes
                                                </Button>
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    :disabled="processing"
                                                    @click="stopEditing"
                                                >
                                                    Cancel
                                                </Button>
                                            </div>
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
                                            @click="startEditing(status.id)"
                                        >
                                            <Pencil />
                                            Edit
                                        </Button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div
                            v-else
                            class="rounded-xl border border-dashed border-border bg-muted/30 px-4 py-6 text-sm text-muted-foreground"
                        >
                            No statuses yet. Create the first one below.
                        </div>
                    </section>

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
                            v-bind="createStatusForm()"
                            :options="formOptions"
                            reset-on-success
                            @success="handleCreateSuccess"
                            v-slot="{ processing, errors }"
                        >
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="new-status-name">Name</Label>
                                    <Input
                                        id="new-status-name"
                                        name="name"
                                        placeholder="Placed"
                                        :disabled="processing"
                                        maxlength="150"
                                        required
                                    />
                                    <InputError :message="errors.name" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="new-status-description">Description</Label>
                                    <Textarea
                                        id="new-status-description"
                                        name="description"
                                        placeholder="Order is confirmed with the supplier and waiting for shipment updates."
                                        :disabled="processing"
                                        rows="3"
                                    />
                                    <InputError :message="errors.description" />
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <Button type="submit" :disabled="processing">
                                        <Plus />
                                        Create Status
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        :disabled="processing"
                                        @click="closeDrawer"
                                    >
                                        <X />
                                        Close
                                    </Button>
                                </div>
                            </div>
                        </Form>
                    </section>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
