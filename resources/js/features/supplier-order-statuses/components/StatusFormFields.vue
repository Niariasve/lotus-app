<script setup lang='ts'>
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

defineProps<{
    descriptionPlaceholder?: string,
    disabled: boolean,
    errors: Record<string, string>,
    idPrefix: string,
    nameDefaultValue?: string,
    namePlaceholder?: string,
    descriptionDefaultValue?: string,
}>();
</script>

<template>
    <div class="space-y-4">
        <div class="space-y-2">
            <Label :for="`${idPrefix}-name`">Name</Label>
            <Input
                :id="`${idPrefix}-name`"
                name="name"
                :default-value="nameDefaultValue"
                :placeholder="namePlaceholder"
                :disabled="disabled"
                maxlength="150"
                required
            />
            <InputError :message="errors.name" />
        </div>

        <div class="space-y-2">
            <Label :for="`${idPrefix}-description`">Description</Label>
            <Textarea
                :id="`${idPrefix}-description`"
                name="description"
                :default-value="descriptionDefaultValue"
                :placeholder="descriptionPlaceholder"
                :disabled="disabled"
                rows="3"
            />
            <InputError :message="errors.description" />
        </div>
    </div>
</template>
