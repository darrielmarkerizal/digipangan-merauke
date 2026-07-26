<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Sprout, CheckCircle2, MapPin } from '@lucide/vue'
import { Icon } from '@/Components/ui'

const highlights = [
  'Verifikasi kualitas & kepastian stok komoditas',
  'Jaringan langsung Gapoktan & mitra petani daerah',
  'Kolaborasi UGM & Kementerian Transmigrasi RI',
]

const grain =
  "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E\")"
</script>

<template>
  <aside
    class="relative isolate hidden flex-col justify-between overflow-hidden bg-[#064e3b] p-10 text-white lg:flex lg:col-span-6 xl:p-14"
  >
    <div
      class="pointer-events-none absolute inset-0 -z-10 opacity-20 mix-blend-multiply"
      :style="{ backgroundImage: grain }"
      aria-hidden="true"
    />

    <div class="flex items-center justify-between">
      <Link href="/" class="flex items-center gap-3">
        <span
          class="flex size-10 items-center justify-center rounded-xl bg-brand text-white shadow-sm"
        >
          <Icon :icon="Sprout" :size="20" />
        </span>
        <span class="text-xl font-bold tracking-tight text-white">
          DigiPangan<span class="font-medium text-emerald-200"> Merauke</span>
        </span>
      </Link>
      <span
        class="rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-medium tracking-wide text-emerald-200 backdrop-blur-sm"
      >
        Portal Mitra
      </span>
    </div>

    <div class="my-auto max-w-lg space-y-6 py-12">
      <h1 class="text-3xl font-bold leading-tight tracking-tight text-white xl:text-4xl">
        Digitalisasi rantai pasok komoditas pertanian Merauke.
      </h1>

      <p class="text-base leading-relaxed text-emerald-100/90">
        Menghubungkan langsung hasil panen dari tangan petani lokal kawasan transmigrasi Merauke di Papua Selatan dengan pasar nusantara melalui kepastian mutu dan transparansi harga.
      </p>

      <ul class="space-y-3 pt-2">
        <li
          v-for="item in highlights"
          :key="item"
          class="flex items-center gap-3 text-sm text-emerald-100"
        >
          <span
            class="flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/20 text-emerald-300"
          >
            <Icon :icon="CheckCircle2" :size="14" />
          </span>
          <span>{{ item }}</span>
        </li>
      </ul>
    </div>

    <div
      class="rounded-2xl bg-white p-6 shadow-xl transition-all hover:shadow-2xl"
    >
      <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">
        Kolaborasi Strategis
      </span>

      <div class="my-4 flex items-center gap-6">
        <img
          src="/images/logos/logo-ugm-horizontal.png"
          alt="Universitas Gadjah Mada"
          class="h-10 xl:h-11 w-auto object-contain"
        />
        <div class="h-8 w-px bg-slate-200" />
        <img
          src="/images/logos/logotext-kementerian-transmigrasi.png"
          alt="Kementerian Transmigrasi RI"
          class="h-10 xl:h-11 w-auto object-contain"
        />
      </div>

      <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
        <Icon :icon="MapPin" :size="15" class="text-emerald-700" />
        <span>Kawasan Transmigrasi Merauke, Papua Selatan</span>
      </div>
    </div>
  </aside>
</template>
