<script setup lang="ts">
import { Icon, Badge } from "@/Components/ui";
import { Clock, MessageSquare, Box } from "@lucide/vue";

defineProps<{
    recentActivities: Array<{
        id: string;
        type: string;
        title: string;
        description: string;
        status: string;
        timestamp: number;
        date_human: string;
    }>;
}>();
</script>

<template>
    <div class="h-full rounded-2xl border border-border/80 bg-white p-5 shadow-xs">
        <h3 class="font-bold text-fg">Aktivitas Terbaru</h3>
        <p class="mb-4 text-xs text-fg-muted">Daftar produk baru dan interaksi WA</p>

        <div v-if="recentActivities.length === 0" class="text-sm text-fg-muted text-center py-4">
            Belum ada aktivitas.
        </div>
        
        <div class="space-y-4">
            <div v-for="activity in recentActivities" :key="activity.id" class="flex gap-3">
                <div class="flex-shrink-0 mt-1">
                    <div v-if="activity.type === 'product'" class="flex h-8 w-8 items-center justify-center rounded-full bg-brand/10 text-brand">
                        <Icon :icon="Box" :size="14" />
                    </div>
                    <div v-else class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <Icon :icon="MessageSquare" :size="14" />
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-fg">{{ activity.title }}</p>
                        <span class="text-xs text-fg-muted flex items-center gap-1">
                            <Icon :icon="Clock" :size="12" />
                            {{ activity.date_human }}
                        </span>
                    </div>
                    <p class="text-xs text-fg-muted mt-0.5 leading-relaxed">{{ activity.description }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
