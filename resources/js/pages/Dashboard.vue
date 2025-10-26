<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import KpiCard from '@/components/dashboard/KpiCard.vue';
import RecentTasks from '@/components/dashboard/RecentTasks.vue';
import QuickActions from '@/components/dashboard/QuickActions.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { CheckCircle, Clock, AlertTriangle, ListTodo } from 'lucide-vue-next';

interface Props {
    kpi: {
        totalTasks: number;
        completedTasks: number;
        inProgressTasks: number;
        overdueTasks: number;
    };
    recentTasks: Array<{
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
    }>;
    projects: Array<{
        id: number;
        name: string;
        description?: string;
        tasks_count: number;
    }>;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- KPI Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <KpiCard
                    title="Total Tasks"
                    :value="kpi.totalTasks"
                    :icon="ListTodo"
                    color="blue"
                    description="All tasks assigned to you"
                />
                <KpiCard
                    title="Completed"
                    :value="kpi.completedTasks"
                    :icon="CheckCircle"
                    color="green"
                    description="Tasks completed"
                />
                <KpiCard
                    title="In Progress"
                    :value="kpi.inProgressTasks"
                    :icon="Clock"
                    color="yellow"
                    description="Currently working on"
                />
                <KpiCard
                    title="Overdue"
                    :value="kpi.overdueTasks"
                    :icon="AlertTriangle"
                    color="red"
                    description="Past due date"
                />
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Recent Tasks - Takes 2 columns -->
                <div class="lg:col-span-2">
                    <RecentTasks :tasks="recentTasks" />
                </div>
                
                <!-- Quick Actions - Takes 1 column -->
                <div>
                    <QuickActions />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
