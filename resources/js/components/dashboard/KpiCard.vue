<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { computed } from 'vue';
import type { Component } from 'vue';

interface Props {
    title: string;
    value: number;
    icon: Component;
    color: 'blue' | 'green' | 'yellow' | 'red';
    trend?: {
        value: number;
        isPositive: boolean;
    };
    description?: string;
}

const props = defineProps<Props>();

const colorClasses = computed(() => {
    const colors = {
        blue: {
            bg: 'bg-blue-50 dark:bg-blue-950/20',
            border: 'border-blue-200 dark:border-blue-800',
            icon: 'text-blue-600 dark:text-blue-400',
            value: 'text-blue-900 dark:text-blue-100',
        },
        green: {
            bg: 'bg-green-50 dark:bg-green-950/20',
            border: 'border-green-200 dark:border-green-800',
            icon: 'text-green-600 dark:text-green-400',
            value: 'text-green-900 dark:text-green-100',
        },
        yellow: {
            bg: 'bg-yellow-50 dark:bg-yellow-950/20',
            border: 'border-yellow-200 dark:border-yellow-800',
            icon: 'text-yellow-600 dark:text-yellow-400',
            value: 'text-yellow-900 dark:text-yellow-100',
        },
        red: {
            bg: 'bg-red-50 dark:bg-red-950/20',
            border: 'border-red-200 dark:border-red-800',
            icon: 'text-red-600 dark:text-red-400',
            value: 'text-red-900 dark:text-red-100',
        },
    };
    
    return colors[props.color];
});
</script>

<template>
    <Card :class="[colorClasses.bg, colorClasses.border]">
        <CardContent class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-muted-foreground">
                        {{ title }}
                    </p>
                    <p :class="['text-3xl font-bold', colorClasses.value]">
                        {{ value.toLocaleString() }}
                    </p>
                    <p v-if="description" class="text-xs text-muted-foreground mt-1">
                        {{ description }}
                    </p>
                </div>
                <div :class="['p-3 rounded-lg', colorClasses.bg]">
                    <component :is="icon" :class="['h-6 w-6', colorClasses.icon]" />
                </div>
            </div>
            
            <div v-if="trend" class="mt-4 flex items-center">
                <Badge 
                    :variant="trend.isPositive ? 'default' : 'destructive'"
                    class="text-xs"
                >
                    <span class="mr-1">
                        {{ trend.isPositive ? '↗' : '↘' }}
                    </span>
                    {{ Math.abs(trend.value) }}%
                </Badge>
                <span class="text-xs text-muted-foreground ml-2">
                    vs last period
                </span>
            </div>
        </CardContent>
    </Card>
</template>
