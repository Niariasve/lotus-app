import { router } from "@inertiajs/vue3";
import { onMounted, onUnmounted } from "vue";
import { toast, type ExternalToast } from "vue-sonner";

type FlashType = 'success' | 'error' | 'info' | 'warning';
type FlashPayload = {
    type?: FlashType,
    message?: string,
    description?: string,
}

export function useFlashToast(options?: ExternalToast) {
    let cleanup: undefined | (() => void);

    const baseOptions: ExternalToast = {
        position: 'top-center',
        duration: 5000,
        ...options,
    };

    onMounted(() => {
        cleanup = router.on('flash', (event) => {
            const flash = event.detail?.flash as FlashPayload | undefined;
            if (!flash) return;

            const message = flash.message ?? '';
            const description = flash.description;

            if (!message && !description) return;

            if (flash.type === 'success') {
                toast.success(message, { ...baseOptions, description });
                return;
            }

            if (flash.type === 'error') {
                toast.error(message, { ...baseOptions, description });
                return;
            }

            if (flash.type === 'warning') {
                toast.warning(message, { ...baseOptions, description });
                return;
            }

            toast(message || 'Info', { ...baseOptions, description });
        });
    });

    onUnmounted(() => {
        cleanup?.();
    });
}