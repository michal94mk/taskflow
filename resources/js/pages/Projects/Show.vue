<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Trash2, Plus } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Task {
    id: number;
    title: string;
    description?: string;
    task_status?: {
        name: string;
        color: string;
    };
    task_priority?: {
        name: string;
        color: string;
    };
}

interface Project {
    id: number;
    name: string;
    description?: string;
    status: string;
    start_date?: string;
    end_date?: string;
    tasks: Task[];
    created_at: string;
}

interface Props {
    project: Project;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Projects', href: '/projects' },
    { title: props.project.name, href: `/projects/${props.project.id}` },
];

const getStatusColor = (status: string) => {
    const colors = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        on_hold: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[status as keyof typeof colors] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
    const labels = {
        active: 'Active',
        on_hold: 'On Hold',
        completed: 'Completed',
        cancelled: 'Cancelled',
    };
    return labels[status as keyof typeof labels] || status;
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

const getTaskStatusColor = (color: string) => {
    const colors = {
        blue: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        green: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        red: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[color as keyof typeof colors] || 'bg-gray-100 text-gray-800';
};

const deleteProject = () => {
    if (confirm('Are you sure you want to delete this project? All associated tasks will also be deleted.')) {
        router.delete(`/projects/${props.project.id}`);
    }
};
</script>

<template>
    <Head :title="project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" @click="$inertia.visit('/projects')">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold">{{ project.name }}</h1>
                            <Badge :class="getStatusColor(project.status)">
                                {{ getStatusLabel(project.status) }}
                            </Badge>
                        </div>
                        <p v-if="project.description" class="text-muted-foreground mt-1">
                            {{ project.description }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="`/projects/${project.id}/edit`">
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button variant="destructive" @click="deleteProject">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <!-- Project Details -->
            <div class="grid gap-6 lg:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Project Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="project.start_date">
                            <p class="text-sm text-muted-foreground">Start Date</p>
                            <p class="font-medium">
                                {{ new Date(project.start_date).toLocaleDateString() }}
                            </p>
                        </div>
                        <div v-if="project.end_date">
                            <p class="text-sm text-muted-foreground">End Date</p>
                            <p class="font-medium">
                                {{ new Date(project.end_date).toLocaleDateString() }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Created</p>
                            <p class="font-medium">
                                {{ new Date(project.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Tasks Section -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Tasks</CardTitle>
                                <CardDescription>
                                    {{ project.tasks.length }} task(s) in this project
                                </CardDescription>
                            </div>
                            <Button size="sm" as-child>
                                <Link href="#">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Task
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="project.tasks.length > 0" class="space-y-3">
                            <div
                                v-for="task in project.tasks"
                                :key="task.id"
                                class="flex items-center justify-between rounded-lg border p-3 hover:bg-muted/50"
                            >
                                <div class="flex-1">
                                    <h4 class="font-medium">{{ task.title }}</h4>
                                    <p v-if="task.description" class="text-sm text-muted-foreground mt-1">
                                        {{ task.description }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <Badge
                                        v-if="task.task_status"
                                        :class="getTaskStatusColor(task.task_status.color)"
                                    >
                                        {{ task.task_status.name }}
                                    </Badge>
                                    <Badge
                                        v-if="task.task_priority"
                                        :class="getPriorityColor(task.task_priority.color)"
                                    >
                                        {{ task.task_priority.name }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-8 text-center text-muted-foreground">
                            No tasks in this project yet.
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
