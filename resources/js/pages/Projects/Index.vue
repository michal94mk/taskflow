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
import { Plus, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Project {
    id: number;
    name: string;
    description?: string;
    status: 'active' | 'on_hold' | 'completed' | 'cancelled';
    start_date?: string;
    end_date?: string;
    tasks_count: number;
    created_at: string;
}

interface Props {
    projects: {
        data: Project[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Projects', href: '/projects' },
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

const deleteProject = (id: number) => {
    if (confirm('Are you sure you want to delete this project?')) {
        router.delete(`/projects/${id}`);
    }
};
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Projects</h1>
                    <p class="text-muted-foreground mt-1">
                        Manage your projects and track their progress
                    </p>
                </div>
                <Button as-child>
                    <Link href="/projects/create">
                        <Plus class="mr-2 h-4 w-4" />
                        New Project
                    </Link>
                </Button>
            </div>

            <!-- Projects Grid -->
            <div v-if="projects.data.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="project in projects.data" :key="project.id">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <CardTitle class="text-xl">{{ project.name }}</CardTitle>
                                <CardDescription v-if="project.description" class="mt-2">
                                    {{ project.description }}
                                </CardDescription>
                            </div>
                            <Badge :class="getStatusColor(project.status)">
                                {{ getStatusLabel(project.status) }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <!-- Stats -->
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Tasks</span>
                                <span class="font-medium">{{ project.tasks_count }}</span>
                            </div>

                            <!-- Dates -->
                            <div v-if="project.start_date || project.end_date" class="text-sm">
                                <div v-if="project.start_date" class="flex justify-between">
                                    <span class="text-muted-foreground">Start</span>
                                    <span>{{ new Date(project.start_date).toLocaleDateString() }}</span>
                                </div>
                                <div v-if="project.end_date" class="flex justify-between">
                                    <span class="text-muted-foreground">End</span>
                                    <span>{{ new Date(project.end_date).toLocaleDateString() }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-2">
                                <Button variant="outline" size="sm" as-child class="flex-1">
                                    <Link :href="`/projects/${project.id}`">
                                        <Eye class="mr-2 h-4 w-4" />
                                        View
                                    </Link>
                                </Button>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="`/projects/${project.id}/edit`">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="deleteProject(project.id)"
                                >
                                    <Trash2 class="h-4 w-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <Card v-else>
                <CardContent class="flex flex-col items-center justify-center py-16">
                    <p class="text-muted-foreground mb-4 text-center">
                        No projects yet. Create your first project to get started.
                    </p>
                    <Button as-child>
                        <Link href="/projects/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Create Project
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
