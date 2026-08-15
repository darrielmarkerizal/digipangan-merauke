<script setup lang="ts">
import { CheckCircle2 } from "@lucide/vue";
import { Icon } from "@/Components/ui";

const props = defineProps<{
    isDistrictAdmin?: boolean;
    regionDistribution: Array<{
        name: string;
        count: number;
        percentage: number;
    }>;
}>();
</script>

<template>
    <div class="rounded-xl border border-border/80 bg-white p-5 shadow-xs h-full flex flex-col">
        <div class="border-b border-border/80 pb-3">
            <h2 class="text-sm font-bold text-fg">
                {{ isDistrictAdmin ? "Sebaran per Desa / Kampung" : "Sebaran per Wilayah" }}
            </h2>
            <p class="text-xs text-fg-muted">
                {{ isDistrictAdmin ? "Persentase komoditas di setiap kampung." : "Persentase komoditas di seluruh kawasan." }}
            </p>
        </div>

        <div class="mt-4 space-y-4 flex-1">
            <div v-if="regionDistribution.length === 0" class="text-xs text-center text-fg-muted italic py-4">
                Belum ada data wilayah.
            </div>
            
            <div v-for="region in regionDistribution" :key="region.name" class="space-y-1.5">
                <div class="flex justify-between text-xs font-semibold">
                    <span class="text-fg">{{ region.name }}</span>
                    <span class="font-bold tabular-nums text-brand">{{ region.percentage }}%</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                    <div
                        class="h-full rounded-full bg-brand transition-all duration-500"
                        :style="{ width: `${region.percentage}%` }"
                    />
                </div>
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-brand/20 bg-brand-weak/60 p-3.5 text-xs text-fg-muted">
            <div class="flex items-center gap-2 font-bold text-brand">
                <Icon :icon="CheckCircle2" :size="15" />
                <span>Audit Trail Aktif</span>
            </div>
            <p class="mt-1 text-[11px]">
                Seluruh perubahan data dikelola secara jujur dan
                berstandar institusi.
            </p>
        </div>
    </div>
</template>
