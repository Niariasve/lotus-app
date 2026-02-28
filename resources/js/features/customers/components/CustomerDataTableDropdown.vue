<script setup lang="ts">
    import { Link, router } from '@inertiajs/vue3';
    import { MoreHorizontal, LoaderCircle, Trash2, Send } from 'lucide-vue-next';
    import { ref } from 'vue';
    import { toast } from 'vue-sonner';
    import { destroy, edit } from '@/actions/App/Http/Controllers/CustomerController';
    import Button from '@/components/ui/button/Button.vue';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuLabel,
        DropdownMenuSeparator,
        DropdownMenuTrigger
    } from '@/components/ui/dropdown-menu'
    import { Customer } from '@/types';
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


    const props = defineProps<{
        customer: Customer,
    }>();

    const openDeleteAlert = ref(false);
    const processing = ref(false);
    const handleDelete = () => {
        processing.value = true;

        router.delete(destroy(props.customer.id), {
            onSuccess: () => {
                toast('Client has been deleted', {
                    description: 'The client has been deleted succesfully',
                    duration: 5000,
                    position: 'top-center',
                });
                openDeleteAlert.value = false;
            },
            onError: () => {
                toast('Something went wrong...', {
                    description: 'The client could not be deleted',
                    duration: 5000,
                    position: 'top-center'
                });
            },
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
            <DropdownMenuItem @click="console.log(customer.id)">
                <Send class="w-4 h-4" />
                New order
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <Link :href="edit(customer.id).url" :method="edit(customer.id).method">
                <DropdownMenuItem>
                    <MoreHorizontal class="w-4 h-4" />
                    See more
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
          client <span class="font-extrabold">{{ customer.full_name }}</span> and remove it's data from our servers.
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