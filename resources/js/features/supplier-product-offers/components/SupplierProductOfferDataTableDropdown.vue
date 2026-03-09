<script setup lang="ts">
    import { Link, router } from '@inertiajs/vue3';
    import { MoreHorizontal, LoaderCircle, Trash2, Pencil } from 'lucide-vue-next';
    import { ref } from 'vue';
    import { destroy, edit } from '@/actions/App/Http/Controllers/SupplierProductOfferController';
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
    import { type SupplierProductOffer } from '../types/supplierProductOffers';

    const props = defineProps<{
        supplierProductOffer: SupplierProductOffer,
    }>();

    const openDeleteAlert = ref(false);
    const processing = ref(false);

    const handleDelete = () => {
        processing.value = true;

        router.delete(destroy(props.supplierProductOffer.id), {
            onFinish: () => {
                processing.value = false;
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
            <Link :href="edit(supplierProductOffer.id).url" :method="edit(supplierProductOffer.id).method">
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
                    This action cannot be undone. This will permanently delete the offer for
                    <span class="font-extrabold">{{ supplierProductOffer.product?.name ?? `product #${supplierProductOffer.product_id}` }}</span>
                    from
                    <span class="font-extrabold">{{ supplierProductOffer.supplier?.name ?? `supplier #${supplierProductOffer.supplier_id}` }}</span>.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="openDeleteAlert = false" :disabled="processing">Cancel</AlertDialogCancel>
                <Button @click="handleDelete" type="submit" as-child :disabled="processing">
                    <AlertDialogAction>
                        <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                        Continue
                    </AlertDialogAction>
                </Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
