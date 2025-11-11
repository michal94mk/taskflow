<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import GlobalSearch from '@/components/GlobalSearch.vue';
import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';
import type { BreadcrumbItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const searchRef = ref<InstanceType<typeof GlobalSearch>>();
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Search Button -->
        <Button
            variant="outline"
            size="sm"
            @click="searchRef?.open()"
            class="gap-2"
        >
            <Search class="h-4 w-4" />
            <span class="hidden sm:inline">Search</span>
            <kbd class="hidden sm:inline pointer-events-none h-5 select-none items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium opacity-100">
                <span class="text-xs">⌘</span>K
            </kbd>
        </Button>

        <GlobalSearch ref="searchRef" />
    </header>
</template>
