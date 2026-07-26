<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import {
  Users,
  ShieldCheck,
  BookOpen,
  Handshake,
  Cpu,
  ArrowRight,
} from '@lucide/vue'
import { Icon } from '@/Components/ui'

const pillars = [
  {
    num: '01',
    title: 'Aksi Partisipasi',
    icon: Users,
    desc: 'Masyarakat dilibatkan aktif sejak awal perencanaan hingga evaluasi untuk membangun kepemilikan program yang kuat.',
  },
  {
    num: '02',
    title: 'Pendampingan Intensif',
    icon: ShieldCheck,
    desc: 'Pembinaan berkala fokus pada koperasi tani, kelembagaan ekonomi, dan digitalisasi pascapanen.',
  },
  {
    num: '03',
    title: 'Learning by Doing',
    icon: BookOpen,
    desc: 'Praktik langsung pemasaran digital, pengemasan standar, dan manajemen keuangan usaha tani.',
  },
  {
    num: '04',
    title: 'Kolaborasi Multi Pihak',
    icon: Handshake,
    desc: 'Sinergi akademisi UGM, Kementerian Transmigrasi, pemerintah daerah, dan kelompok masyarakat.',
  },
  {
    num: '05',
    title: 'Dukungan Teknologi',
    icon: Cpu,
    desc: 'Pemanfaatan marketplace komunitas DigiPangan untuk menghubungkan komoditas lokal langsung ke pembeli.',
  },
]
</script>

<template>
  <section class="border-y border-border/80 bg-white py-24 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-brand">
          Pendekatan Terintegrasi &amp; Berkelanjutan
        </span>
        <h2 class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl">
          5 Pilar Metodologi Program Pendampingan
        </h2>
        <p class="text-base text-fg-muted">
          Metodologi komprehensif yang dirancang oleh Tim UGM bersama Kementerian Transmigrasi RI
          untuk membangun ekosistem ekonomi lokal yang produktif dan berdaya saing.
        </p>
      </div>

      <div class="mt-14 grid grid-cols-1 divide-y divide-border/80 overflow-hidden rounded-2xl border border-border/80 bg-white shadow-sm md:grid-cols-3 md:divide-x md:divide-y-0 lg:grid-cols-5">
        <div
          v-for="pilar in pillars"
          :key="pilar.num"
          class="flex flex-col justify-between p-7 transition-colors hover:bg-bg/60"
        >
          <div>
            <div class="flex items-center justify-between">
              <span class="text-2xl font-black text-brand/80">{{ pilar.num }}.</span>
              <div class="flex size-10 items-center justify-center rounded-xl bg-brand/10 text-brand">
                <Icon :icon="pilar.icon" :size="20" />
              </div>
            </div>
            <h3 class="mt-6 text-base font-bold text-fg">{{ pilar.title }}</h3>
            <p class="mt-2.5 text-sm leading-relaxed text-fg-muted">{{ pilar.desc }}</p>
          </div>
          <div class="mt-6 border-t border-border/60 pt-4 text-[11px] font-semibold text-fg-muted/80">
            Metodologi Resmi Program
          </div>
        </div>
      </div>

      <div class="mt-10 flex flex-col items-center justify-between gap-6 rounded-3xl border border-brand bg-brand-strong p-8 text-white shadow-soft md:flex-row md:p-10">
        <div class="space-y-2 text-center md:text-left">
          <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
            Hasil Akhir Program
          </p>
          <p class="text-xl font-bold text-white sm:text-2xl">
            Terbentuk Ekosistem Ekonomi Lokal yang Mandiri, Inklusif, dan Berdaya Saing
          </p>
        </div>
        <Link
          href="/tentang"
          class="inline-flex shrink-0 items-center gap-2.5 rounded-full bg-white px-7 py-3.5 text-sm font-bold text-brand shadow-sm transition-all duration-200 hover:bg-emerald-50 hover:shadow-md"
        >
          <span>Baca Dokumen Laporan Lengkap</span>
          <Icon :icon="ArrowRight" :size="16" />
        </Link>
      </div>
    </div>
  </section>
</template>
