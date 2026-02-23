<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { Plus } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import AppLayout from '@/layouts/AppLayout.vue';
    import customers from '@/routes/customers';
    import { type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Customers',
            href: customers.index().url,
        },
    ];

</script>

<template>

    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Customers" description="Manage customers" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(customers.create().url)" class="cursor-pointer">
                    <Plus />
                    Create Customer
                </Button>
            </div>

            <section class="rounded-xl border border-sidebar-border/70 bg-background p-3 dark:border-sidebar-border">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-180 text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left">
                                <th class="py-2 pr-3 font-medium">Name</th>
                                <th class="py-2 pr-3 font-medium">Email</th>
                                <th class="py-2 pr-3 font-medium">Phone</th>
                                <th class="py-2 pr-3 font-medium">Status</th>
                                <th class="py-2 pr-0 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="py-10 text-center text-muted-foreground">
                                    Table content will be added here.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
