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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Props {
    projects: Array<{ id: number; name: string }>;
    statuses: Array<{ id: number; name: string; color: string }>;
    priorities: Array<{ id: number; name: string; color: string }>;
}

const props = defineProps<Props>();

// Get project_id from URL query parameter
const urlParams = new URLSearchParams(window.location.search);
const preselectedProjectId = urlParams.get('project_id');

const form = useForm({
    title: '',
    description: '',
    project_id: preselectedProjectId || '',
    task_status_id: props.statuses[0]?.id.toString() || '',
    task_priority_id: props.priorities[1]?.id.toString() || '', // Medium by default
    due_date: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tasks', href: '/tasks' },
    { title: 'Create', href: '/tasks/create' },
];

const submit = () => {
    form.post('/tasks', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create Task" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" @click="$inertia.visit('/tasks')">
                    <ArrowLeft class="h-4 w-4" />
                </Button>
                <div>
                    <h1 class="text-3xl font-bold">Create Task</h1>
                    <p class="text-muted-foreground mt-1">
                        Add a new task to track your work
                    </p>
                </div>
            </div>

            <!-- Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Task Details</CardTitle>
                    <CardDescription>
                        Fill in the information below to create a new task
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Title -->
                        <div class="space-y-2">
                            <Label for="title">Title *</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="Enter task title"
                                required
                            />
                            <p v-if="form.errors.title" class="text-sm text-red-500">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Enter task description"
                            />
                            <p v-if="form.errors.description" class="text-sm text-red-500">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Project -->
                        <div class="space-y-2">
                            <Label for="project">Project *</Label>
                            <Select v-model="form.project_id" required>
                                <SelectTrigger>
                                    <SelectValue placeholder="Select project" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="project in projects" :key="project.id" :value="String(project.id)">
                                        {{ project.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.project_id" class="text-sm text-red-500">
                                {{ form.errors.project_id }}
                            </p>
                        </div>

                        <!-- Status and Priority -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="status">Status *</Label>
                                <Select v-model="form.task_status_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="status in statuses" :key="status.id" :value="String(status.id)">
                                            {{ status.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.task_status_id" class="text-sm text-red-500">
                                    {{ form.errors.task_status_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="priority">Priority *</Label>
                                <Select v-model="form.task_priority_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select priority" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="priority in priorities" :key="priority.id" :value="String(priority.id)">
                                            {{ priority.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.task_priority_id" class="text-sm text-red-500">
                                    {{ form.errors.task_priority_id }}
                                </p>
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="space-y-2">
                            <Label for="due_date">Due Date</Label>
                            <Input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                            />
                            <p v-if="form.errors.due_date" class="text-sm text-red-500">
                                {{ form.errors.due_date }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Task' }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit('/tasks')"
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
