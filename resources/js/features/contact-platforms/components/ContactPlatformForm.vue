<script setup lang='ts'>
    import { Form, router } from '@inertiajs/vue3';
    import ContactPlatformController from '@/actions/App/Http/Controllers/ContactPlatformController';
    import InputError from '@/components/InputError.vue';
    import { Button } from '@/components/ui/button';
    import {
        Field,
        FieldGroup,
        FieldLabel,
        FieldLegend,
        FieldSet,
    } from '@/components/ui/field'
    import { Input } from '@/components/ui/input';
    import { Spinner } from '@/components/ui/spinner';
    import contactPlatforms from '@/routes/contact-platforms';
    import { type ContactPlatform } from '@/types';

    const props = defineProps<{
        contactPlatform?: ContactPlatform
    }>();

    const controller = () => {
        if (!props.contactPlatform) return ContactPlatformController.store.form();
        else return ContactPlatformController.update.form(props.contactPlatform!.id);
    }
</script>

<template>
    <Form v-bind="controller()" v-slot="{ processing, errors }">
        <div class="flex flex-col gap-4 md:max-w-4xl mx-auto">

            <div class="p-4 space-y-3 border rounded-xl">
                <FieldSet>
                    <FieldLegend>Contact Platform Information</FieldLegend>
                    <FieldGroup>
                        <Field>
                            <FieldLabel for="name">Name *</FieldLabel>
                            <Input id="name" name="name" placeholder="Club Penguin..."
                                :default-value="contactPlatform?.name" :disabled="processing" />
                            <InputError :message="errors.name" />
                        </Field>
                        <Field class="flex flex-col gap-3 justify-left">
                            <div class="flex flex-row gap-3">
                                <input id="is_active" type="checkbox" name="is_active" class="w-4!" :checked="contactPlatform?.is_active" />
                                <FieldLabel for="is_active">Is Active</FieldLabel>
                            </div>
                            <InputError :message="errors.is_active" />
                        </Field>
                    </FieldGroup>
                </FieldSet>
            </div>
            <div class="flex gap-2">
                <Button type="submit" :disabled="processing" class="cursor-pointer">
                    <Spinner v-if="processing" class="animate-spin" />
                    <div v-if="contactPlatform">{{ $t('actions.update') }}</div>
                    <div v-else>{{ $t('actions.submit') }}</div>
                </Button>
                <Button type="button" :disabled="processing" variant="destructive" class="cursor-pointer"
                    @click="router.visit(contactPlatforms.index().url)">
                    {{ $t('actions.cancel') }}
                </Button>
            </div>
        </div>
    </Form>
</template>