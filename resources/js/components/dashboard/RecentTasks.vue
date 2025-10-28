<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { Calendar, Clock, User } from 'lucide-vue-next';

interface Task {
    id: number;
    title: string;
    description?: string;
    due_date?: string;
    created_at: string;
    project?: {
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

defineProps<{
    tasks: Task[];
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pl-PL', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString('pl-PL', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getPriorityColor = (color: string) => {
    const colors = {
        green: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        orange: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        red: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[color as keyof typeof colors] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
};

const getStatusColor = (color: string) => {
    const colors = {
        blue: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        green: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        red: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[color as keyof typeof colors] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
};
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-lg font-semibold">Recent Tasks</CardTitle>
            <Button variant="outline" size="sm" as-child>
                <Link href="#">
                    View All
                </Link>
            </Button>
        </CardHeader>
        <CardContent>
            <div v-if="tasks.length === 0" class="text-center py-8 text-muted-foreground">
                <p>No tasks found</p>
            </div>
            <div v-else class="space-y-4">
                <div 
                    v-for="task in tasks" 
                    :key="task.id"
                    class="flex items-start space-x-3 p-3 rounded-lg border border-border/50 hover:bg-muted/50 transition-colors"
                >
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-sm truncate">
                            {{ task.title }}
                        </h4>
                        <p v-if="task.description" class="text-xs text-muted-foreground mt-1 line-clamp-2">
                            {{ task.description }}
                        </p>
                        <div class="flex items-center space-x-2 mt-2">
                            <div v-if="task.project" class="flex items-center space-x-1">
                                <User class="h-3 w-3 text-muted-foreground" />
                                <span class="text-xs text-muted-foreground">{{ task.project.name }}</span>
                            </div>
                            <div v-if="task.due_date" class="flex items-center space-x-1">
                                <Calendar class="h-3 w-3 text-muted-foreground" />
                                <span class="text-xs text-muted-foreground">{{ formatDate(task.due_date) }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <Clock class="h-3 w-3 text-muted-foreground" />
                                <span class="text-xs text-muted-foreground">{{ formatDateTime(task.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <Badge 
                            v-if="task.task_status" 
                            :class="getStatusColor(task.task_status.color)"
                            class="text-xs"
                        >
                            {{ task.task_status.name }}
                        </Badge>
                        <Badge 
                            v-if="task.task_priority" 
                            :class="getPriorityColor(task.task_priority.color)"
                            class="text-xs"
                        >
                            {{ task.task_priority.name }}
                        </Badge>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
