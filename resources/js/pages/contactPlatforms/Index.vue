<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { Pencil, Plus } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import {
        Table,
        TableBody,
        TableCaption,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
    } from '@/components/ui/table'
    import AppLayout from '@/layouts/AppLayout.vue';
    import contactPlatformsRoutes from '@/routes/contact-platforms';
    import { type BreadcrumbItem, type ContactPlatform } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Contact Platforms',
            href: contactPlatformsRoutes.index().url,
        },
    ];

    defineProps<{
        contactPlatforms: ContactPlatform[],
    }>();
</script>

<template>

    <Head title="Contact Platforms" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Contact Platforms" description="Manage contact platforms" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(contactPlatformsRoutes.create().url)" class="cursor-pointer">
                    <Plus />
                    {{ $t('actions.create') }}
                </Button>
            </div>

            <Table>
                <TableCaption>A list of all contact platforms</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Id</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Slug</TableHead>
                        <TableHead>Is Active</TableHead>
                        <TableHead>Created At</TableHead>
                        <TableHead>Updated At</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(platform, index) in contactPlatforms" :key="index">
                        <TableCell>{{ platform.id }}</TableCell>
                        <TableCell>{{ platform.name }}</TableCell>
                        <TableCell>{{ platform.slug }}</TableCell>
                        <TableCell>{{ platform.is_active }}</TableCell>
                        <TableCell>{{ platform.created_at }}</TableCell>
                        <TableCell>{{ platform.updated_at }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button @click="router.visit(contactPlatformsRoutes.edit(platform.id).url)" class="cursor-pointer">
                                <Pencil class="w-4 h-4" />
                                Edit
                            </Button>
                            <Button class="cursor-pointer" variant="secondary">
                                <div v-if="platform.is_active">Deactivate</div>
                                <div v-else>Activate</div>
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
