<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import {
  Sparkles,
  ArrowRight,
  ChevronRight,
  Sprout,
  MapPin,
} from '@lucide/vue'
import { Icon, Badge } from '@/Components/ui'
import { useRupiah } from '@/Composables/useRupiah'
import type { ProductCard } from '@/types/home'

defineProps<{
  heroProduct: ProductCard | null
  heroImage: string | null
  heroIsFeatured: boolean
  grain: string
}>()

const { formatRupiah } = useRupiah()

const stats = [
  { label: 'Luas Distrik Ulilin', value: '3.633,08', unit: 'km²' },
  { label: 'Desa Distrik Ulilin', value: '11', unit: 'Desa' },
  { label: 'Penduduk Ulilin (2024)', value: '10.791', unit: 'Jiwa' },
  { label: 'Distrik Transmigrasi', value: '3', unit: 'Kawasan' },
]
</script>

<template>
  <section class="relative isolate -mt-16 overflow-hidden pt-16 md:-mt-20 lg:min-h-[95vh] lg:pt-20">
    <div
      class="pointer-events-none absolute inset-0 -z-10 opacity-[0.05] mix-blend-multiply"
      :style="{ backgroundImage: grain }"
      aria-hidden="true"
    />

    <div
      class="mx-auto grid w-full max-w-7xl items-center gap-12 px-4 pb-20 pt-24 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:px-8 lg:py-24"
    >
      <div class="space-y-8 lg:col-span-7">
        <div class="flex flex-wrap items-center gap-3">
          <span
            class="inline-flex items-center gap-2 rounded-full border border-border/80 bg-white px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-fg shadow-sm"
          >
            <span class="flex size-5 items-center justify-center rounded-full bg-brand text-on-brand">
              <Icon :icon="Sparkles" :size="12" />
            </span>
            Program Strategis UGM &amp; Kementerian Transmigrasi RI
          </span>
        </div>

        <h1
          class="text-balance text-4xl font-extrabold leading-[1.08] tracking-tight text-fg sm:text-5xl lg:text-6xl"
        >
          Penguatan Kelembagaan &amp; Digitalisasi Rantai Pasok
          <span class="text-brand">Komoditas Merauke</span>
        </h1>

        <p class="max-w-2xl text-base leading-relaxed text-fg-muted sm:text-lg">
          Platform marketplace komunitas resmi etalase komoditas unggulan kawasan transmigrasi
          <strong class="text-fg">Muting, Ulilin, dan Elikobel</strong>. Hubungi petani dan koperasi secara langsung,
          tanpa rantai perantara.
        </p>

        <div class="flex flex-wrap items-center gap-4">
          <Link
            href="/produk"
            class="group inline-flex items-center gap-3 rounded-full bg-brand px-8 py-4 text-base font-bold text-on-brand shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-strong hover:shadow-md active:scale-[0.98]"
          >
            <span>Jelajahi Produk Panen</span>
            <span
              class="flex size-8 items-center justify-center rounded-full bg-on-brand/15 transition-transform duration-300 group-hover:translate-x-1"
            >
              <Icon :icon="ArrowRight" :size="16" />
            </span>
          </Link>

          <Link
            href="/tentang"
            class="inline-flex items-center gap-2 rounded-full border border-border/80 bg-white px-7 py-4 text-base font-bold text-fg shadow-sm transition-all duration-200 hover:border-brand/40 hover:bg-card"
          >
            <span>Tentang Program</span>
            <Icon :icon="ChevronRight" :size="16" class="text-fg-muted" />
          </Link>
        </div>

        <div class="pt-2">
          <p class="mb-2.5 text-xs font-bold uppercase tracking-wider text-fg-muted/80">
            Mitra Strategis Pendamping:
          </p>
          <div
            class="inline-flex flex-wrap items-center gap-6 rounded-2xl border border-border/80 bg-white px-6 py-4 shadow-sm"
          >
            <img
              src="/images/logos/logo-ugm-horizontal.png"
              alt="Universitas Gadjah Mada"
              class="h-9 sm:h-10 w-auto object-contain"
            />
            <div class="h-8 w-px bg-slate-200" />
            <img
              src="/images/logos/logotext-kementerian-transmigrasi.png"
              alt="Kementerian Transmigrasi RI"
              class="h-9 sm:h-10 w-auto object-contain"
            />
          </div>
        </div>
      </div>

      <div class="relative lg:col-span-5 lg:pl-4">
        <component
          :is="heroProduct ? Link : 'div'"
          :href="heroProduct ? `/produk/${heroProduct.slug}` : undefined"
          class="group relative block rounded-[2.25rem] bg-white p-3 shadow-[0_24px_60px_-15px_rgba(20,40,31,0.12)] ring-1 ring-border/80 transition-transform duration-500 ease-out hover:-translate-y-1"
        >
          <div class="relative overflow-hidden rounded-[1.75rem]">
            <div class="relative aspect-[4/5] bg-muted">
              <img
                v-if="heroImage"
                :src="heroImage"
                :alt="heroProduct ? `${heroProduct.name} dari Merauke` : 'Komoditas lokal Merauke'"
                class="size-full object-cover transition-transform duration-700 group-hover:scale-105"
              />
              <div
                v-else
                class="relative flex size-full items-center justify-center overflow-hidden bg-bg"
                aria-hidden="true"
              >
                <Icon :icon="Sprout" :size="120" class="text-brand/20" />
              </div>

              <Badge
                v-if="heroIsFeatured"
                variant="unggulan"
                class="absolute left-5 top-5 shadow-sm"
              >
                Komoditas Unggulan
              </Badge>

              <div
                v-if="heroProduct"
                class="absolute inset-x-3 bottom-3 flex items-end justify-between gap-3 rounded-2xl border border-border/60 bg-white/95 p-4 shadow-md backdrop-blur-md"
              >
                <div class="min-w-0">
                  <p
                    v-if="heroProduct.region"
                    class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-brand"
                  >
                    <Icon :icon="MapPin" :size="13" /> Distrik {{ heroProduct.region.name }}
                  </p>
                  <p class="truncate text-lg font-bold text-fg">
                    {{ heroProduct.name }}
                  </p>
                  <p v-if="heroProduct.farmer" class="truncate text-xs text-fg-muted">
                    Petani: {{ heroProduct.farmer.name }}
                  </p>
                </div>
                <span class="shrink-0 text-xl font-bold tabular-nums text-brand">
                  {{ formatRupiah(Number(heroProduct.price)) }}
                </span>
              </div>
            </div>
          </div>
        </component>
      </div>
    </div>

    <div class="border-y border-border/80 bg-white py-10 shadow-soft">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 divide-y divide-border/80 md:grid-cols-2 md:divide-y-0 lg:grid-cols-4 lg:divide-x">
          <div
            v-for="(st, idx) in stats"
            :key="idx"
            class="px-6 py-5 lg:px-8"
          >
            <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-fg-muted/60">
              0{{ idx + 1 }} / {{ idx === 0 ? 'CAKUPAN DISTRIK' : idx === 1 ? 'KAWASAN TRANSMIGRASI' : idx === 2 ? 'PERMUKIMAN DESA' : 'TRANSAKSI PASAR' }}
            </p>
            <p class="mt-2 text-3xl font-extrabold tabular-nums text-fg lg:text-4xl">
              {{ st.value }}
              <span class="text-base font-semibold text-brand">{{ st.unit }}</span>
            </p>
            <p class="mt-1 text-xs font-medium text-fg-muted">{{ st.label }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
