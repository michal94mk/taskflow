<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Search, FileText, FolderOpen, Loader2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

interface SearchResult {
    id: number;
    title: string;
    description?: string;
    type: 'task' | 'project';
    url: string;
    project?: string;
    status?: string;
    priority?: string;
}

const isOpen = ref(false);
const query = ref('');
const results = ref<{ tasks: SearchResult[]; projects: SearchResult[] }>({
    tasks: [],
    projects: [],
});
const isLoading = ref(false);
const selectedIndex = ref(0);

let debounceTimer: NodeJS.Timeout;

const search = async () => {
    if (!query.value.trim()) {
        results.value = { tasks: [], projects: [] };
        return;
    }

    isLoading.value = true;

    try {
        const response = await fetch(`/search?q=${encodeURIComponent(query.value)}`);
        const data = await response.json();
        results.value = data;
        selectedIndex.value = 0;
    } catch (error) {
        console.error('Search error:', error);
    } finally {
        isLoading.value = false;
    }
};

watch(query, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(search, 300);
});

const allResults = ref<SearchResult[]>([]);

watch(results, (newResults) => {
    allResults.value = [
        ...newResults.projects,
        ...newResults.tasks,
    ];
}, { deep: true });

const selectResult = (result: SearchResult) => {
    router.visit(result.url);
    closeDialog();
};

const closeDialog = () => {
    isOpen.value = false;
    query.value = '';
    results.value = { tasks: [], projects: [] };
    selectedIndex.value = 0;
};

const handleKeydown = (e: KeyboardEvent) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        isOpen.value = true;
    }

    if (!isOpen.value) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex.value = Math.min(selectedIndex.value + 1, allResults.value.length - 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex.value = Math.max(selectedIndex.value - 1, 0);
    } else if (e.key === 'Enter' && allResults.value[selectedIndex.value]) {
        e.preventDefault();
        selectResult(allResults.value[selectedIndex.value]);
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});

defineExpose({ open: () => isOpen.value = true });
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="max-w-2xl p-0">
            <div class="flex flex-col">
                <!-- Search Input -->
                <div class="flex items-center border-b px-4 py-3">
                    <Search class="h-5 w-5 text-muted-foreground mr-3" />
                    <Input
                        v-model="query"
                        placeholder="Search tasks and projects... (Ctrl+K)"
                        class="border-0 focus-visible:ring-0 focus-visible:ring-offset-0"
                        autofocus
                    />
                    <Loader2 v-if="isLoading" class="h-5 w-5 animate-spin text-muted-foreground" />
                </div>

                <!-- Results -->
                <div class="max-h-[400px] overflow-y-auto p-2">
                    <!-- Projects -->
                    <div v-if="results.projects.length > 0" class="mb-4">
                        <div class="px-2 py-1 text-xs font-semibold text-muted-foreground">
                            Projects
                        </div>
                        <button
                            v-for="(project, index) in results.projects"
                            :key="`project-${project.id}`"
                            @click="selectResult(project)"
                            :class="[
                                'w-full text-left px-3 py-2 rounded-md hover:bg-accent transition-colors',
                                selectedIndex === index && 'bg-accent'
                            ]"
                        >
                            <div class="flex items-start gap-3">
                                <FolderOpen class="h-5 w-5 text-muted-foreground mt-0.5 flex-shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium">{{ project.title }}</div>
                                    <div v-if="project.description" class="text-sm text-muted-foreground truncate">
                                        {{ project.description }}
                                    </div>
                                    <div v-if="project.status" class="mt-1">
                                        <Badge variant="outline" class="text-xs">
                                            {{ project.status }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Tasks -->
                    <div v-if="results.tasks.length > 0">
                        <div class="px-2 py-1 text-xs font-semibold text-muted-foreground">
                            Tasks
                        </div>
                        <button
                            v-for="(task, index) in results.tasks"
                            :key="`task-${task.id}`"
                            @click="selectResult(task)"
                            :class="[
                                'w-full text-left px-3 py-2 rounded-md hover:bg-accent transition-colors',
                                selectedIndex === (results.projects.length + index) && 'bg-accent'
                            ]"
                        >
                            <div class="flex items-start gap-3">
                                <FileText class="h-5 w-5 text-muted-foreground mt-0.5 flex-shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium">{{ task.title }}</div>
                                    <div v-if="task.description" class="text-sm text-muted-foreground truncate">
                                        {{ task.description }}
                                    </div>
                                    <div class="flex gap-2 mt-1 flex-wrap">
                                        <Badge v-if="task.project" variant="outline" class="text-xs">
                                            {{ task.project }}
                                        </Badge>
                                        <Badge v-if="task.status" variant="outline" class="text-xs">
                                            {{ task.status }}
                                        </Badge>
                                        <Badge v-if="task.priority" variant="outline" class="text-xs">
                                            {{ task.priority }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Empty State -->
                    <div v-if="query && !isLoading && results.tasks.length === 0 && results.projects.length === 0" class="text-center py-8 text-muted-foreground">
                        <Search class="h-12 w-12 mx-auto mb-2 opacity-50" />
                        <p>No results found for "{{ query }}"</p>
                    </div>

                    <!-- Initial State -->
                    <div v-if="!query" class="text-center py-8 text-muted-foreground">
                        <Search class="h-12 w-12 mx-auto mb-2 opacity-50" />
                        <p>Start typing to search tasks and projects...</p>
                        <p class="text-xs mt-2">Use ↑↓ to navigate, Enter to select</p>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

