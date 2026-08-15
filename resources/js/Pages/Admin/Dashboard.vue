<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DashboardKpiCards from "@/Components/admin/DashboardKpiCards.vue";
import DashboardTrendChart from "@/Components/admin/DashboardTrendChart.vue";
import DashboardRegionDistribution from "@/Components/admin/DashboardRegionDistribution.vue";
import DashboardRecentActivities from "@/Components/admin/DashboardRecentActivities.vue";
import DashboardPopularProducts from "@/Components/admin/DashboardPopularProducts.vue";

const props = defineProps<{
    is_district_admin?: boolean;
    district_name?: string;
    metrics: {
        active_products: number;
        farmers_and_groups: number;
        wa_clicks: number;
        integrated_regions: number;
        total_posts?: number;
        active_faqs?: number;
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
    recent_activities: Array<{
        id: string;
        type: string;
        title: string;
        description: string;
        status: string;
        timestamp: number;
        date_human: string;
    }>;
    popular_products: Array<{
        id: number;
        name: string;
        region: string;
        contact_count: number;
    }>;
}>();
</script>

<template>
    <AdminLayout
        :title="props.is_district_admin ? `Dashboard Distrik ${props.district_name ?? ''}` : 'Dashboard Statistik & Dampak Program'"
        :subtitle="props.is_district_admin
            ? `Pusat pemantauan komoditas pangan, petani, interaksi pembeli WhatsApp, dan sebaran per kampung di Distrik ${props.district_name ?? ''}.`
            : 'Pusat pembuktian dampak ekonomi komunitas, efektivitas kontak langsung WhatsApp, dan pemantauan sebaran panen per distrik transmigrasi.'"
    >
        <DashboardKpiCards :metrics="props.metrics" :is-district-admin="props.is_district_admin" :district-name="props.district_name" />

        <div class="mt-4 grid grid-cols-1 gap-5 lg:grid-cols-12">
            <div class="lg:col-span-8 flex flex-col gap-5">
                <DashboardTrendChart :trend-data="props.trend_data" />
            </div>

            <div class="lg:col-span-4 flex flex-col gap-5">
                <DashboardRegionDistribution
                    :region-distribution="props.region_distribution"
                    :is-district-admin="props.is_district_admin"
                />
            </div>

            <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-5">
                <DashboardPopularProducts
                    :popular-products="props.popular_products"
                />
                <DashboardRecentActivities
                    :recent-activities="props.recent_activities"
                />
            </div>
        </div>
    </AdminLayout>
</template>
