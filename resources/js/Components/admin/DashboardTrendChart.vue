<script setup lang="ts">
import { computed } from "vue";
import VueApexCharts from "vue3-apexcharts";
import { type ApexOptions } from "apexcharts";

const props = defineProps<{
    trendData: Array<{
        x: string;
        y: number;
    }>;
}>();

const chartOptions: ApexOptions = {
    chart: {
        type: 'area',
        fontFamily: 'inherit',
        toolbar: { show: false },
        zoom: { enabled: false },
        animations: { enabled: true }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 3,
        colors: ['#0f8a48']
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.05,
            stops: [0, 100],
            colorStops: [
                {
                    offset: 0,
                    color: "#0f8a48",
                    opacity: 0.4
                },
                {
                    offset: 100,
                    color: "#0f8a48",
                    opacity: 0.05
                }
            ]
        }
    },
    xaxis: {
        type: 'category',
        labels: {
            style: { colors: '#737373', fontSize: '11px' }
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: '#737373', fontSize: '11px' }
        }
    },
    grid: {
        borderColor: '#f0f0f0',
        strokeDashArray: 4,
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: true } }
    },
    tooltip: {
        theme: 'light',
        y: {
            formatter: (val: number) => val + " interaksi"
        }
    }
};

const chartSeries = computed(() => [{
    name: 'Klik Hubungi Penjual',
    data: props.trendData
}]);
</script>

<template>
    <div class="rounded-xl border border-border/80 bg-white p-5 shadow-xs h-full flex flex-col">
        <div class="flex items-center justify-between border-b border-border/80 pb-3">
            <div>
                <h2 class="text-sm font-bold text-fg">
                    Tren Kontak Pembeli via WhatsApp (12 Bulan)
                </h2>
                <p class="text-xs text-fg-muted">
                    Pencatatan interaksi langsung tombol Hubungi Penjual.
                </p>
            </div>
            <span class="rounded-full bg-brand-weak px-2.5 py-1 text-[11px] font-bold text-brand">
                Live Metric
            </span>
        </div>

        <div class="mt-4 flex-1 w-full min-h-[250px]">
            <VueApexCharts
                type="area"
                height="100%"
                :options="chartOptions"
                :series="chartSeries"
            />
        </div>
    </div>
</template>
