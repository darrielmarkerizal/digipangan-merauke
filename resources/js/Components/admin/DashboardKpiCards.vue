<script setup lang="ts">
import { computed } from "vue";
import {
    Users,
    ShoppingBasket,
    MessageCircle,
    MapPin,
} from "@lucide/vue";
import { Icon } from "@/Components/ui";

const props = defineProps<{
    metrics: {
        active_products: number;
        farmers_and_groups: number;
        wa_clicks: number;
        integrated_regions: number;
    };
}>();

const kpiCards = computed(() => [
    {
        label: "Produk Aktif Tayang",
        value: props.metrics.active_products,
        unit: "Komoditas",
        delta: "Total produk ter-publish",
        icon: ShoppingBasket,
    },
    {
        label: "Petani & Gapoktan Terdaftar",
        value: props.metrics.farmers_and_groups,
        unit: "Mitra",
        delta: "Berdampak pada komunitas",
        icon: Users,
    },
    {
        label: "Klik WA Hubungi Penjual",
        value: props.metrics.wa_clicks,
        unit: "Interaksi",
        delta: "Kontak langsung WhatsApp",
        icon: MessageCircle,
    },
    {
        label: "Kawasan Terintegrasi",
        value: props.metrics.integrated_regions,
        unit: "Kawasan",
        delta: "Distrik/Wilayah aktif",
        icon: MapPin,
    },
]);
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div
            v-for="kpi in kpiCards"
            :key="kpi.label"
            class="flex flex-col justify-between rounded-xl border border-border/80 bg-white p-4 shadow-xs"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-fg-muted">{{ kpi.label }}</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
                    <Icon :icon="kpi.icon" :size="16" />
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold tabular-nums text-fg">
                    {{ kpi.value }}
                    <span class="text-xs font-semibold text-brand">{{ kpi.unit }}</span>
                </p>
                <p class="mt-1 text-xs font-medium text-fg-muted/80">
                    {{ kpi.delta }}
                </p>
            </div>
        </div>
    </div>
</template>
