<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Moon, Sun } from 'lucide-vue-next';
import { useDarkMode } from '@/composables/useDarkMode';

const { isDark, toggle } = useDarkMode();
</script>

<template>
    <Button
        variant="ghost"
        size="icon"
        @click="toggle"
        class="transition-transform hover:scale-110"
    >
        <Sun v-if="isDark" class="h-5 w-5" />
        <Moon v-else class="h-5 w-5" />
    </Button>
</template>

