<script setup lang='ts'>
import { Link, router } from '@inertiajs/vue3';
import { Eye, LoaderCircle, MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import Button from '@/components/ui/button/Button.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import supplierOrdersRoutes from '@/routes/supplier-orders';
import { type SupplierOrder } from '../types/supplierOrders';

defineProps<{
    supplierOrder: SupplierOrder,
}>();

const openDeleteAlert = ref(false);
const processing = ref(false);

const handleDelete = (supplierOrderId: number): void => {
    processing.value = true;

    router.delete(supplierOrdersRoutes.destroy(supplierOrderId), {
        onFinish: () => {
            processing.value = false;
            openDeleteAlert.value = false;
        },
    });
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="h-8 w-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuLabel>Actions</DropdownMenuLabel>
            <DropdownMenuSeparator />

            <Link
                :href="supplierOrdersRoutes.show(supplierOrder.id).url"
                :method="supplierOrdersRoutes.show(supplierOrder.id).method"
            >
                <DropdownMenuItem>
                    <Eye class="h-4 w-4" />
                    View
                </DropdownMenuItem>
            </Link>

            <Link
                :href="supplierOrdersRoutes.edit(supplierOrder.id).url"
                :method="supplierOrdersRoutes.edit(supplierOrder.id).method"
            >
                <DropdownMenuItem>
                    <Pencil class="h-4 w-4" />
                    Edit
                </DropdownMenuItem>
            </Link>

            <DropdownMenuItem class="text-destructive" @click="openDeleteAlert = true">
                <Trash2 class="h-4 w-4 text-destructive" />
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>

    <AlertDialog :open="openDeleteAlert">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                    This action cannot be undone. This will permanently delete supplier order
                    <span class="font-extrabold">{{ supplierOrder.order_number }}</span>.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="openDeleteAlert = false" :disabled="processing">
                    Cancel
                </AlertDialogCancel>
                <Button @click="handleDelete(supplierOrder.id)" type="submit" as-child :disabled="processing">
                    <AlertDialogAction>
                        <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                        Continue
                    </AlertDialogAction>
                </Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
