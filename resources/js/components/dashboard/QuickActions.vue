<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { Plus, FolderPlus, ListTodo, FolderOpen } from 'lucide-vue-next';

const quickActions = [
    {
        title: 'New Task',
        description: 'Create a new task',
        icon: Plus,
        href: '#', // TODO: Link to task creation
        variant: 'default' as const,
    },
    {
        title: 'New Project',
        description: 'Create a new project',
        icon: FolderPlus,
        href: '#', // TODO: Link to project creation
        variant: 'outline' as const,
    },
    {
        title: 'View All Tasks',
        description: 'See all your tasks',
        icon: ListTodo,
        href: '#', // TODO: Link to tasks index
        variant: 'outline' as const,
    },
    {
        title: 'View All Projects',
        description: 'See all your projects',
        icon: FolderOpen,
        href: '#', // TODO: Link to projects index
        variant: 'outline' as const,
    },
];
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-lg font-semibold">Quick Actions</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="grid grid-cols-2 gap-3">
                <Button 
                    v-for="action in quickActions" 
                    :key="action.title"
                    :variant="action.variant"
                    class="h-auto p-4 flex flex-col items-center space-y-2"
                    as-child
                >
                    <Link :href="action.href">
                        <component :is="action.icon" class="h-5 w-5" />
                        <div class="text-center">
                            <div class="font-medium text-sm">{{ action.title }}</div>
                            <div class="text-xs text-muted-foreground">{{ action.description }}</div>
                        </div>
                    </Link>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
