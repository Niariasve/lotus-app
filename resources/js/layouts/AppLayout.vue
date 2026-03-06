<script setup lang="ts">
import { useFlashToast } from '@/composables/useFlashToast';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

useFlashToast();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <slot />
    </AppLayout>
</template>
