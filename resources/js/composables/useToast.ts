import { toast } from 'vue-sonner';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

export function useToast() {
    const page = usePage();

    // Watch for flash messages from Laravel
    watch(
        () => page.props.flash,
        (flash: any) => {
            if (flash?.success) {
                toast.success(flash.success);
            }
            if (flash?.error) {
                toast.error(flash.error);
            }
            if (flash?.info) {
                toast.info(flash.info);
            }
            if (flash?.warning) {
                toast.warning(flash.warning);
            }
        },
        { deep: true }
    );

    return {
        success: (message: string) => toast.success(message),
        error: (message: string) => toast.error(message),
        info: (message: string) => toast.info(message),
        warning: (message: string) => toast.warning(message),
        loading: (message: string) => toast.loading(message),
        promise: toast.promise,
    };
}

