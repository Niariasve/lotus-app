<script setup lang="ts">
    import { Head, router } from '@inertiajs/vue3';
    import { ArrowLeft } from 'lucide-vue-next';
    import Heading from '@/components/Heading.vue';
    import { Button } from '@/components/ui/button';
    import ContactPlatformForm from '@/features/contact-platforms/components/ContactPlatformForm.vue';
    import AppLayout from '@/layouts/AppLayout.vue';
    import contactPlatforms from '@/routes/contact-platforms';
    import { type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Contact Platforms',
            href: contactPlatforms.index().url,
        },
        {
            title: 'Create',
            href: contactPlatforms.create().url,
        }
    ];
</script>

<template>

    <Head title="Create Contact Platforms" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-3">
            <Heading title="Create Contact Platforms" description="Create contact platforms" class="mb-0" />
            <div class="flex items-center justify-end">
                <Button @click="router.visit(contactPlatforms.index().url)" class="cursor-pointer" variant="destructive">
                    <ArrowLeft />
                    {{ $t('actions.cancel') }}
                </Button>
            </div>

            <ContactPlatformForm />
        </div>
    </AppLayout>
</template>
