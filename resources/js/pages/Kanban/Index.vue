<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Eye, Filter } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import draggable from 'vuedraggable';
import type { BreadcrumbItem } from '@/types';

interface Task {
    id: number;
    title: string;
    description?: string;
    due_date?: string;
    project?: {
        id: number;
        name: string;
    };
    task_priority?: {
        name: string;
        color: string;
    };
}

interface TaskStatus {
    id: number;
    name: string;
    slug: string;
    color: string;
}

interface TasksByStatus {
    status: TaskStatus;
    tasks: Task[];
}

interface Props {
    tasksByStatus: TasksByStatus[];
    projects: Array<{ id: number; name: string }>;
    filter: {
        project_id?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Kanban Board', href: '/kanban' },
];

const selectedProject = ref(props.filter.project_id || undefined);

// Local state for drag & drop
const columns = ref<TasksByStatus[]>(
    props.tasksByStatus.map(column => ({
        ...column,
        tasks: [...column.tasks],
    }))
);

// Update columns when tasksByStatus changes (after filtering)
watch(() => props.tasksByStatus, (newTasksByStatus) => {
    columns.value = newTasksByStatus.map(column => ({
        ...column,
        tasks: [...column.tasks],
    }));
}, { deep: true });

const applyFilter = () => {
    router.get('/kanban', {
        project_id: selectedProject.value,
    }, {
        preserveScroll: true,
        only: ['tasksByStatus', 'filter'],
    });
};

const clearFilter = () => {
    selectedProject.value = undefined;
    router.get('/kanban', {}, {
        preserveScroll: true,
        only: ['tasksByStatus', 'filter'],
    });
};

const getPriorityColor = (color: string) => {
    const colors = {
        green: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        orange: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        red: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[color as keyof typeof colors] || 'bg-gray-100 text-gray-800';
};

const getStatusColor = (color: string) => {
    const colors = {
        blue: 'border-blue-500',
        yellow: 'border-yellow-500',
        green: 'border-green-500',
        red: 'border-red-500',
    };
    return colors[color as keyof typeof colors] || 'border-gray-500';
};

const onDragEnd = (statusId: number) => {
    const column = columns.value.find(col => col.status.id === statusId);
    if (!column) return;

    // Find all tasks that have changed status
    column.tasks.forEach((task) => {
        // Check if task's original status differs from current column
        const originalColumn = props.tasksByStatus.find(col => 
            col.tasks.some(t => t.id === task.id)
        );
        
        if (originalColumn && originalColumn.status.id !== statusId) {
            // Update task status via API
            router.patch(`/kanban/${task.id}/status`, {
                task_status_id: statusId,
            }, {
                preserveState: true,
                preserveScroll: true,
                only: ['tasksByStatus'],
            });
        }
    });
};
</script>

<template>
    <Head title="Kanban Board" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Kanban Board</h1>
                    <p class="text-muted-foreground mt-1">
                        Visualize and manage your tasks with drag & drop
                    </p>
                </div>
                <Button as-child>
                    <Link href="/tasks/create">
                        <Plus class="mr-2 h-4 w-4" />
                        New Task
                    </Link>
                </Button>
            </div>

            <!-- Filter -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <Filter class="h-4 w-4" />
                        Filter by Project
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4 items-end">
                        <div class="flex-1 max-w-xs space-y-2">
                            <Select v-model="selectedProject">
                                <SelectTrigger>
                                    <SelectValue placeholder="All projects" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="project in projects" :key="project.id" :value="String(project.id)">
                                        {{ project.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <Button @click="applyFilter">
                            Apply
                        </Button>
                        <Button variant="outline" @click="clearFilter">
                            Clear
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Kanban Board -->
            <div class="flex-1 overflow-x-auto pb-4">
                <div class="flex gap-4 h-full min-w-max">
                    <!-- Column for each status -->
                    <div
                        v-for="column in columns"
                        :key="column.status.id"
                        class="flex-shrink-0 w-80 flex flex-col"
                    >
                        <div :class="['border-t-4 rounded-t-lg bg-card p-4', getStatusColor(column.status.color)]">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg">{{ column.status.name }}</h3>
                                <Badge variant="secondary">{{ column.tasks.length }}</Badge>
                            </div>
                        </div>
                        
                        <div class="flex-1 bg-muted/30 rounded-b-lg p-4 min-h-[200px]">
                            <draggable
                                v-model="column.tasks"
                                :group="{ name: 'tasks', pull: true, put: true }"
                                item-key="id"
                                class="space-y-3 min-h-full"
                                :animation="200"
                                ghost-class="opacity-50"
                                @end="() => onDragEnd(column.status.id)"
                            >
                                <template #item="{ element: task }">
                                    <Card class="cursor-move hover:shadow-md transition-shadow">
                                        <CardContent class="p-4 space-y-2">
                                            <div class="flex items-start justify-between gap-2">
                                                <h4 class="font-medium text-sm line-clamp-2">
                                                    {{ task.title }}
                                                </h4>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    class="h-6 w-6 p-0 flex-shrink-0"
                                                    as-child
                                                >
                                                    <Link :href="`/tasks/${task.id}`">
                                                        <Eye class="h-3 w-3" />
                                                    </Link>
                                                </Button>
                                            </div>
                                            
                                            <p v-if="task.description" class="text-xs text-muted-foreground line-clamp-2">
                                                {{ task.description }}
                                            </p>
                                            
                                            <div class="flex flex-wrap gap-2 pt-2">
                                                <Badge v-if="task.task_priority" :class="getPriorityColor(task.task_priority.color)" class="text-xs">
                                                    {{ task.task_priority.name }}
                                                </Badge>
                                                <Badge v-if="task.project" variant="outline" class="text-xs">
                                                    {{ task.project.name }}
                                                </Badge>
                                            </div>
                                            
                                            <div v-if="task.due_date" class="text-xs text-muted-foreground">
                                                📅 {{ new Date(task.due_date).toLocaleDateString() }}
                                            </div>
                                        </CardContent>
                                    </Card>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.sortable-ghost {
    opacity: 0.5;
}
</style>

