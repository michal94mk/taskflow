<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import KpiCard from '@/components/dashboard/KpiCard.vue';
import RecentTasks from '@/components/dashboard/RecentTasks.vue';
import QuickActions from '@/components/dashboard/QuickActions.vue';
import ChartCard from '@/components/dashboard/ChartCard.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { CheckCircle, Clock, AlertTriangle, ListTodo } from 'lucide-vue-next';
import { computed } from 'vue';

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
    charts: {
        tasksByStatus: Array<{
            name: string;
            color: string;
            count: number;
        }>;
        tasksByPriority: Array<{
            name: string;
            color: string;
            count: number;
        }>;
        timeline: Array<{
            date: string;
            created: number;
            completed: number;
        }>;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Chart data computed properties
const tasksByStatusChartData = computed(() => {
    const colorMap: Record<string, string> = {
        blue: '#3B82F6',
        yellow: '#F59E0B',
        green: '#10B981',
        red: '#EF4444',
    };

    return {
        labels: props.charts.tasksByStatus.map(item => item.name),
        datasets: [{
            label: 'Tasks by Status',
            data: props.charts.tasksByStatus.map(item => item.count),
            backgroundColor: props.charts.tasksByStatus.map(item => colorMap[item.color] || '#6B7280'),
            borderWidth: 0,
        }],
    };
});

const tasksByPriorityChartData = computed(() => {
    const colorMap: Record<string, string> = {
        green: '#10B981',
        yellow: '#F59E0B',
        orange: '#F97316',
        red: '#EF4444',
    };

    return {
        labels: props.charts.tasksByPriority.map(item => item.name),
        datasets: [{
            label: 'Tasks by Priority',
            data: props.charts.tasksByPriority.map(item => item.count),
            backgroundColor: props.charts.tasksByPriority.map(item => colorMap[item.color] || '#6B7280'),
            borderWidth: 0,
        }],
    };
});

const timelineChartData = computed(() => {
    const labels = props.charts.timeline.map(item => 
        new Date(item.date).toLocaleDateString('pl-PL', { month: 'short', day: 'numeric' })
    );

    return {
        labels,
        datasets: [
            {
                label: 'Created',
                data: props.charts.timeline.map(item => item.created),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
            },
            {
                label: 'Completed',
                data: props.charts.timeline.map(item => item.completed),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
            },
        ],
    };
});
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

            <!-- Charts Section -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Tasks by Status - Pie Chart -->
                <div>
                    <ChartCard
                        title="Tasks by Status"
                        type="pie"
                        :data="tasksByStatusChartData"
                    />
                </div>
                
                <!-- Tasks by Priority - Bar Chart -->
                <div>
                    <ChartCard
                        title="Tasks by Priority"
                        type="bar"
                        :data="tasksByPriorityChartData"
                    />
                </div>
                
                <!-- Tasks Timeline - Line Chart -->
                <div>
                    <ChartCard
                        title="Tasks Timeline (30 days)"
                        type="line"
                        :data="timelineChartData"
                    />
                </div>
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
