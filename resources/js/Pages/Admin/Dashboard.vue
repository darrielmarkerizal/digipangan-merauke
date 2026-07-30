<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DashboardKpiCards from "@/Components/admin/DashboardKpiCards.vue";
import DashboardTrendChart from "@/Components/admin/DashboardTrendChart.vue";
import DashboardRegionDistribution from "@/Components/admin/DashboardRegionDistribution.vue";

const props = defineProps<{
    metrics: {
        active_products: number;
        farmers_and_groups: number;
        wa_clicks: number;
        integrated_regions: number;
    };
    region_distribution: Array<{
        name: string;
        count: number;
        percentage: number;
    }>;
    trend_data: Array<{
        x: string;
        y: number;
    }>;
}>();
</script>

<template>
    <AdminLayout
        title="Dashboard Statistik & Dampak Program"
        subtitle="Pusat pembuktian dampak ekonomi komunitas, efektivitas kontak langsung WhatsApp, dan pemantauan sebaran panen per distrik transmigrasi."
    >
        <DashboardKpiCards :metrics="props.metrics" />

        <div class="mt-4 grid grid-cols-1 gap-5 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <DashboardTrendChart :trend-data="props.trend_data" />
            </div>

            <div class="lg:col-span-4">
                <DashboardRegionDistribution :region-distribution="props.region_distribution" />
            </div>
        </div>
    </AdminLayout>
</template>
