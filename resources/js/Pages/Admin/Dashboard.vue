<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  Users,
  ShoppingBasket,
  MessageCircle,
  TrendingUp,
  MapPin,
  CheckCircle2,
} from '@lucide/vue'
import { Icon } from '@/Components/ui'

const kpiCards = [
  {
    label: 'Produk Aktif Tayang',
    value: '42',
    unit: 'Komoditas',
    delta: '+12% bln ini',
    icon: ShoppingBasket,
  },
  {
    label: 'Petani & Gapoktan Terdaftar',
    value: '128',
    unit: 'Mitra',
    delta: '11 Desa Ulilin/Muting',
    icon: Users,
  },
  {
    label: 'Klik WA Hubungi Penjual',
    value: '1.420',
    unit: 'Interaksi',
    delta: '+28.4% efektivitas',
    icon: MessageCircle,
  },
  {
    label: 'Kawasan Terintegrasi',
    value: '3',
    unit: 'Distrik',
    delta: 'Muting, Ulilin, Elikobel',
    icon: MapPin,
  },
]
</script>

<template>
  <AdminLayout
    title="Dashboard Statistik & Dampak Program"
    subtitle="Pusat pembuktian dampak ekonomi komunitas, efektivitas kontak langsung WhatsApp, dan pemantauan sebaran panen per distrik transmigrasi."
  >
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

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12">
      <div class="rounded-xl border border-border/80 bg-white p-5 shadow-xs lg:col-span-8">
        <div class="flex items-center justify-between border-b border-border/80 pb-3">
          <div>
            <h2 class="text-sm font-bold text-fg">
              Tren Kontak Pembeli via WhatsApp (12 Bulan)
            </h2>
            <p class="text-xs text-fg-muted">
              Pencatatan interaksi langsung tombol Hubungi Penjual (PRD Bagian 12).
            </p>
          </div>
          <span class="rounded-full bg-brand-weak px-2.5 py-1 text-[11px] font-bold text-brand">
            Live Metric
          </span>
        </div>

        <div class="mt-4 flex h-52 flex-col items-center justify-center rounded-xl border border-dashed border-border/80 bg-muted/20 text-center p-4">
          <Icon :icon="TrendingUp" :size="36" class="text-brand/40" />
          <p class="mt-2.5 text-xs font-semibold text-fg">
            Modul Visualisasi ApexCharts siap dihubungkan
          </p>
          <p class="mt-1 text-[11px] text-fg-muted max-w-sm">
            Data interaksi pembeli dengan petani dipetakan ke dalam grafik deret waktu harian &amp; bulanan.
          </p>
        </div>
      </div>

      <div class="rounded-xl border border-border/80 bg-white p-5 shadow-xs lg:col-span-4">
        <div class="border-b border-border/80 pb-3">
          <h2 class="text-sm font-bold text-fg">Sebaran per Wilayah</h2>
          <p class="text-xs text-fg-muted">
            Persentase komoditas di 3 kawasan utama.
          </p>
        </div>

        <div class="mt-4 space-y-4">
          <div class="space-y-1.5">
            <div class="flex justify-between text-xs font-semibold">
              <span class="text-fg">Distrik Muting</span>
              <span class="font-bold tabular-nums text-brand">42%</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
              <div class="h-full rounded-full bg-brand" style="width: 42%" />
            </div>
          </div>

          <div class="space-y-1.5">
            <div class="flex justify-between text-xs font-semibold">
              <span class="text-fg">Distrik Ulilin (11 Desa)</span>
              <span class="font-bold tabular-nums text-brand">38%</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
              <div class="h-full rounded-full bg-brand" style="width: 38%" />
            </div>
          </div>

          <div class="space-y-1.5">
            <div class="flex justify-between text-xs font-semibold">
              <span class="text-fg">Distrik Elikobel</span>
              <span class="font-bold tabular-nums text-brand">20%</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
              <div class="h-full rounded-full bg-brand" style="width: 20%" />
            </div>
          </div>
        </div>

        <div class="mt-5 rounded-xl border border-brand/20 bg-brand-weak/60 p-3.5 text-xs text-fg-muted">
          <div class="flex items-center gap-2 font-bold text-brand">
            <Icon :icon="CheckCircle2" :size="15" />
            <span>Audit Trail Aktif</span>
          </div>
          <p class="mt-1 text-[11px]">
            Seluruh perubahan data dikelola secara jujur dan berstandar institusi.
          </p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
