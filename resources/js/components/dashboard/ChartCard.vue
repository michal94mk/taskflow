<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    PointElement,
    LineElement,
    Filler,
    DoughnutController,
    PieController,
    BarController,
    LineController,
} from 'chart.js';

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    PointElement,
    LineElement,
    Filler,
    DoughnutController,
    PieController,
    BarController,
    LineController
);

interface ChartData {
    labels: string[];
    datasets: Array<{
        label: string;
        data: number[];
        backgroundColor?: string | string[];
        borderColor?: string | string[];
        borderWidth?: number;
        fill?: boolean;
        tension?: number;
    }>;
}

interface Props {
    title: string;
    type: 'pie' | 'bar' | 'line';
    data: ChartData;
    options?: any;
}

const props = defineProps<Props>();
const canvasRef = ref<HTMLCanvasElement>();
let chartInstance: ChartJS | null = null;

const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
            labels: {
                usePointStyle: true,
                padding: 20,
            },
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
                size: 14,
                weight: 'bold',
            },
            bodyFont: {
                size: 13,
            },
            cornerRadius: 4,
            displayColors: true,
        },
    },
    scales: props.type === 'line' ? {
        x: {
            grid: {
                display: false,
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.1)',
            },
        },
    } : undefined,
};

const createChart = () => {
    if (!canvasRef.value) return;

    // Destroy existing chart
    if (chartInstance) {
        chartInstance.destroy();
    }

    const config = {
        type: props.type,
        data: props.data,
        options: { ...defaultOptions, ...props.options },
    };

    chartInstance = new ChartJS(canvasRef.value, config);
};

onMounted(() => {
    createChart();
});

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});

// Watch for data changes
watch(() => props.data, () => {
    createChart();
}, { deep: true });
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-lg font-semibold">{{ title }}</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="h-64 w-full">
                <canvas ref="canvasRef"></canvas>
            </div>
        </CardContent>
    </Card>
</template>
