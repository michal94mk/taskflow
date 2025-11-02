<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Card,
    CardContent,
    CardDescription,
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
import { Plus, Eye, Pencil, Trash2, Search, Filter } from 'lucide-vue-next';
import { ref, watch } from 'vue';
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
    task_status?: {
        name: string;
        color: string;
    };
    task_priority?: {
        name: string;
        color: string;
    };
}

interface Props {
    tasks: {
        data: Task[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    projects: Array<{ id: number; name: string }>;
    statuses: Array<{ id: number; name: string; color: string }>;
    priorities: Array<{ id: number; name: string; color: string }>;
    filters: {
        project_id?: string;
        status_id?: string;
        priority_id?: string;
        search?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tasks', href: '/tasks' },
];

const searchQuery = ref(props.filters.search || '');
const selectedProject = ref(props.filters.project_id || '');
const selectedStatus = ref(props.filters.status_id || '');
const selectedPriority = ref(props.filters.priority_id || '');

const applyFilters = () => {
    router.get('/tasks', {
        search: searchQuery.value,
        project_id: selectedProject.value,
        status_id: selectedStatus.value,
        priority_id: selectedPriority.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedProject.value = undefined;
    selectedStatus.value = undefined;
    selectedPriority.value = undefined;
    router.get('/tasks');
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
        blue: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        green: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        red: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[color as keyof typeof colors] || 'bg-gray-100 text-gray-800';
};

const deleteTask = (id: number) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(`/tasks/${id}`);
    }
};
</script>

<template>
    <Head title="Tasks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Tasks</h1>
                    <p class="text-muted-foreground mt-1">
                        Manage and track your tasks
                    </p>
                </div>
                <Button as-child>
                    <Link href="/tasks/create">
                        <Plus class="mr-2 h-4 w-4" />
                        New Task
                    </Link>
                </Button>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Filter class="h-5 w-5" />
                        Filters
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-4">
                        <!-- Search -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Search</label>
                            <div class="relative">
                                <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                <Input
                                    v-model="searchQuery"
                                    placeholder="Search tasks..."
                                    class="pl-9"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Project Filter -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Project</label>
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

                        <!-- Status Filter -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Status</label>
                            <Select v-model="selectedStatus">
                                <SelectTrigger>
                                    <SelectValue placeholder="All statuses" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="status in statuses" :key="status.id" :value="String(status.id)">
                                        {{ status.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Priority Filter -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Priority</label>
                            <Select v-model="selectedPriority">
                                <SelectTrigger>
                                    <SelectValue placeholder="All priorities" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="priority in priorities" :key="priority.id" :value="String(priority.id)">
                                        {{ priority.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="mt-4 flex gap-2">
                        <Button @click="applyFilters">
                            Apply Filters
                        </Button>
                        <Button variant="outline" @click="clearFilters">
                            Clear
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Tasks List -->
            <div v-if="tasks.data.length > 0" class="space-y-3">
                <Card v-for="task in tasks.data" :key="task.id">
                    <CardContent class="flex items-center justify-between p-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <h3 class="font-semibold text-lg">{{ task.title }}</h3>
                                <Badge v-if="task.task_status" :class="getStatusColor(task.task_status.color)">
                                    {{ task.task_status.name }}
                                </Badge>
                                <Badge v-if="task.task_priority" :class="getPriorityColor(task.task_priority.color)">
                                    {{ task.task_priority.name }}
                                </Badge>
                            </div>
                            <p v-if="task.description" class="text-sm text-muted-foreground mt-1 line-clamp-2">
                                {{ task.description }}
                            </p>
                            <div class="flex items-center gap-4 mt-2 text-sm text-muted-foreground">
                                <span v-if="task.project">📁 {{ task.project.name }}</span>
                                <span v-if="task.due_date">📅 {{ new Date(task.due_date).toLocaleDateString() }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/tasks/${task.id}`">
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/tasks/${task.id}/edit`">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button variant="outline" size="sm" @click="deleteTask(task.id)">
                                <Trash2 class="h-4 w-4 text-red-500" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <Card v-else>
                <CardContent class="flex flex-col items-center justify-center py-16">
                    <p class="text-muted-foreground mb-4 text-center">
                        No tasks found. Create your first task to get started.
                    </p>
                    <Button as-child>
                        <Link href="/tasks/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Create Task
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
