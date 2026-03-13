<script setup lang='ts'>
    import { ExternalLink } from 'lucide-vue-next';
    import { computed } from 'vue';
    import { Button } from '@/components/ui/button';

    const props = defineProps<{
        url: string,
    }>();

    const externalUrl = computed(() => {
        const value = props.url?.trim() ?? '';
        if (!value) return '#';
        if (/^https?:\/\//i.test(value)) return value;
        return `https://${value}`;
    });

    const openExternal = () => {
        window.open(externalUrl.value, '_blank', 'noopener,noreferrer');
    };
</script>

<template>
    <Button type="button" size="icon-sm" class="rounded-full cursor-pointer" variant="outline"
        @click="openExternal">
        <ExternalLink />
    </Button>
</template>
