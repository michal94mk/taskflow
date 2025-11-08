import { computed } from 'vue';
import { useAppearance } from './useAppearance';

export function useDarkMode() {
    const { appearance, updateAppearance } = useAppearance();

    const isDark = computed(() => {
        if (appearance.value === 'system') {
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        return appearance.value === 'dark';
    });

    const toggle = () => {
        const newTheme = isDark.value ? 'light' : 'dark';
        updateAppearance(newTheme);
    };

    return {
        isDark,
        toggle,
        appearance,
        updateAppearance,
    };
}

