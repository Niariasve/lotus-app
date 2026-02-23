<script setup lang="ts">
    import { usePage } from '@inertiajs/vue3';
    import { Form } from '@inertiajs/vue3';
    import { computed, ref } from 'vue';
    import LocaleController from '@/actions/App/Http/Controllers/Settings/LocaleController';
    import Heading from '@/components/Heading.vue';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select'
    import { Button } from './ui/button';
    import { Spinner } from './ui/spinner';

    const page = usePage();
    const locale = computed(() => page.props.locale ?? 'en');
    const selectedLocale = ref(locale.value);
</script>

<template>
    <div class="space-y-6">
        <Heading variant="small" title="Language" description="Change your preferred language" />


        <Form v-bind="LocaleController.update.form()" class="flex items-center gap-4"
            :options="{ preserveScroll: true }" v-slot="{ processing, recentlySuccessful }">
            <input type="hidden" name="locale" :value="selectedLocale">

            <Select :default-value="locale" v-model="selectedLocale">
                <SelectTrigger class="min-w-45">
                    <SelectValue placeholder="Select a language" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="en">
                        English
                    </SelectItem>
                    <SelectItem value="es">
                        Spanish
                    </SelectItem>
                </SelectContent>
            </Select>

            <Button type="submit" :disabled="processing">
                <Spinner v-if="processing" class="animate-spin" />
                Save
            </Button>

            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                    Saved.
                </p>
            </Transition>
        </Form>

    </div>
</template>