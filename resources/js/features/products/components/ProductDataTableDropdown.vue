<script setup lang="ts">
    import { Link, router } from '@inertiajs/vue3';
    import { MoreHorizontal, LoaderCircle, Trash2, Pencil } from 'lucide-vue-next';
    import { ref } from 'vue';
    import { destroy, edit } from '@/actions/App/Http/Controllers/ProductController';
    import {
        AlertDialog,
        AlertDialogAction,
        AlertDialogCancel,
        AlertDialogContent,
        AlertDialogDescription,
        AlertDialogFooter,
        AlertDialogHeader,
        AlertDialogTitle,
    } from "@/components/ui/alert-dialog"
    import Button from '@/components/ui/button/Button.vue';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuLabel,
        DropdownMenuSeparator,
        DropdownMenuTrigger
    } from '@/components/ui/dropdown-menu'
    import { type Product } from '@/types';


    const props = defineProps<{
        product: Product,
    }>();

    const openDeleteAlert = ref(false);
    const processing = ref(false);
    const handleDelete = () => {
        processing.value = true;

        router.delete(destroy(props.product.id), {
            onFinish: () => {
                processing.value = false;
            }
        });
    }
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="w-8 h-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="w-4 h-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuLabel>Actions</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <Link :href="edit(product.id).url" :method="edit(product.id).method">
                <DropdownMenuItem>
                    <Pencil class="w-4 h-4" />
                    Edit
                </DropdownMenuItem>
            </Link>
            <DropdownMenuItem class="text-destructive" @click="openDeleteAlert = true">
                <Trash2 class="w-4 h-4 text-destructive" />
                Delete
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
    <AlertDialog :open="openDeleteAlert">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                    This action cannot be undone. This will permanently delete this
                    product <span class="font-extrabold">{{ product.name }}</span> and remove it's data from our
                    servers.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="openDeleteAlert = false" :disabled="processing">Cancel</AlertDialogCancel>
                <Button @click="handleDelete" type="submit" as-child :disabled="processing">
                    <AlertDialogAction>
                        <LoaderCircle v-if="processing" class="w-4 h-4 animate-spin" />
                        Continue
                    </AlertDialogAction>
                </Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>