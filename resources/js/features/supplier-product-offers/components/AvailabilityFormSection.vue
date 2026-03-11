<script setup lang='ts'>
    import InputError from '@/components/InputError.vue';
    import {
        Field,
        FieldDescription,
        FieldGroup,
        FieldLabel,
        FieldLegend,
        FieldSet,
    } from '@/components/ui/field';
    import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

    defineProps<{
        isAvailable: string,
        currentLastCheckedAt: string,
        errors: Record<string, string>
    }>();

    const emit = defineEmits([
        'update:is-available',
    ]);
</script>

<template>
    <FieldSet>
        <FieldLegend>Availability</FieldLegend>
        <FieldDescription>
            Update availability status. Last checked time is managed automatically by the system.
        </FieldDescription>

        <FieldGroup>
            <Field>
                <FieldLabel>Is Available *</FieldLabel>
                <InputError :message="errors.is_available" />

                <RadioGroup name="is_available" :model-value="isAvailable"
                    @update:model-value="emit('update:is-available', $event)">
                    <Field>
                        <div class="flex items-center space-x-2">

                            <RadioGroupItem id="is-available-yes" value="1" />

                            <FieldLabel for="is-available-yes" class="text-sm">
                                Available
                            </FieldLabel>

                        </div>
                    </Field>
                    <Field>
                        <div class="flex items-center space-x-2">

                            <RadioGroupItem id="is-available-no" value="0" />

                            <FieldLabel for="is-available-no" class="text-sm">
                                Unavailable
                            </FieldLabel>
                        </div>
                    </Field>
                </RadioGroup>

                <Field>

                    <FieldLabel>
                        Last Checked At
                    </FieldLabel>

                    <div class="text-sm text-muted-foreground">
                        {{ currentLastCheckedAt }}
                    </div>

                    <FieldDescription>
                        On create it uses current timestamp. On update it changes only if availability changes.
                    </FieldDescription>

                </Field>
            </Field>
        </FieldGroup>
    </FieldSet>
</template>

<style></style>