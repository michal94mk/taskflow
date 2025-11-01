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
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Trash2, Calendar, FolderOpen } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Task {
    id: number;
    title: string;
    description?: string;
    due_date?: string;
    created_at: string;
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
    task: Task;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tasks', href: '/tasks' },
    { title: props.task.title, href: `/tasks/${props.task.id}` },
];

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

const deleteTask = () => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(`/tasks/${props.task.id}`);
    }
};
</script>

<template>
    <Head :title="task.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" @click="$inertia.visit('/tasks')">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold">{{ task.title }}</h1>
                            <Badge v-if="task.task_status" :class="getStatusColor(task.task_status.color)">
                                {{ task.task_status.name }}
                            </Badge>
                            <Badge v-if="task.task_priority" :class="getPriorityColor(task.task_priority.color)">
                                {{ task.task_priority.name }}
                            </Badge>
                        </div>
                        <p v-if="task.description" class="text-muted-foreground mt-1">
                            {{ task.description }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="`/tasks/${task.id}/edit`">
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button variant="destructive" @click="deleteTask">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <!-- Task Details -->
            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Task Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="task.project">
                            <div class="flex items-center gap-2 text-sm text-muted-foreground mb-1">
                                <FolderOpen class="h-4 w-4" />
                                <span>Project</span>
                            </div>
                            <Link :href="`/projects/${task.project.id}`" class="font-medium hover:underline">
                                {{ task.project.name }}
                            </Link>
                        </div>
                        <div v-if="task.due_date">
                            <div class="flex items-center gap-2 text-sm text-muted-foreground mb-1">
                                <Calendar class="h-4 w-4" />
                                <span>Due Date</span>
                            </div>
                            <p class="font-medium">
                                {{ new Date(task.due_date).toLocaleDateString('en-US', { 
                                    weekday: 'long', 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric' 
                                }) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Created</p>
                            <p class="font-medium">
                                {{ new Date(task.created_at).toLocaleDateString('en-US', { 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric' 
                                }) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Description</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="task.description" class="text-muted-foreground whitespace-pre-wrap">
                            {{ task.description }}
                        </p>
                        <p v-else class="text-muted-foreground italic">
                            No description provided.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
