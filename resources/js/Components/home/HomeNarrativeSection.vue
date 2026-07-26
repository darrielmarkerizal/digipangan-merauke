<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowRight } from '@lucide/vue'
import { Icon } from '@/Components/ui'
</script>

<template>
  <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
    <div class="grid grid-cols-1 items-start gap-12 lg:grid-cols-12 lg:gap-16">
      <div class="space-y-6 lg:col-span-5 lg:sticky lg:top-24">
        <span class="text-xs font-mono font-bold uppercase tracking-widest text-brand">
          01. Visi &amp; Latar Belakang Program
        </span>
        <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-fg sm:text-4xl">
          Menutup Kesenjangan Potensi Alam Merauke dengan Digitalisasi
        </h2>
        <p class="text-base leading-relaxed text-fg-muted">
          Distrik Ulilin, Muting, dan Elikobel memiliki kekayaan komoditas berlimpah mulai dari padi,
          jagung, rambutan, hingga hortikultura. Namun tantangan pascapanen dan rantai pasok panjang
          sering menekan margin petani.
        </p>
        <p class="text-base leading-relaxed text-fg-muted">
          <strong class="text-fg">DigiPangan Merauke</strong> hadir sebagai solusi nyata perpanjangan
          tangan program Pengabdian kepada Masyarakat UGM bersama Kementerian Transmigrasi RI untuk
          membangun kedaulatan pangan nasional dari Papua Selatan.
        </p>
        <div class="pt-2">
          <Link
            href="/tentang"
            class="inline-flex items-center gap-2 text-sm font-bold text-brand hover:underline"
          >
            <span>Pelajari Metodologi Lengkap Tim UGM</span>
            <Icon :icon="ArrowRight" :size="15" />
          </Link>
        </div>
      </div>

      <div class="divide-y divide-border/80 border-y border-border/80 lg:col-span-7">
        <article class="py-7 first:pt-0 last:pb-0">
          <p class="text-xs font-mono font-bold tracking-widest text-brand">01</p>
          <h3 class="mt-2 text-xl font-bold text-fg">Penguatan Koperasi Tani</h3>
          <p class="mt-2 text-sm leading-relaxed text-fg-muted">
            Memperkuat kelembagaan dan tata kelola koperasi sebagai pusat ekonomi masyarakat di kawasan transmigrasi Merauke.
          </p>
        </article>

        <article class="py-7">
          <p class="text-xs font-mono font-bold tracking-widest text-brand">02</p>
          <h3 class="mt-2 text-xl font-bold text-fg">Peningkatan Kapasitas SDM</h3>
          <p class="mt-2 text-sm leading-relaxed text-fg-muted">
            Meningkatkan kemampuan kewirausahaan, manajemen usaha tani, dan literasi keuangan masyarakat secara terukur.
          </p>
        </article>

        <article class="py-7">
          <p class="text-xs font-mono font-bold tracking-widest text-brand">03</p>
          <h3 class="mt-2 text-xl font-bold text-fg">Digitalisasi Pemasaran</h3>
          <p class="mt-2 text-sm leading-relaxed text-fg-muted">
            Memanfaatkan platform marketplace komunitas untuk pemasaran langsung, informasi harga pasar, dan promosi produk desa.
          </p>
        </article>

        <article class="py-7">
          <p class="text-xs font-mono font-bold tracking-widest text-brand">04</p>
          <h3 class="mt-2 text-xl font-bold text-fg">Pascapanen &amp; Nilai Tambah</h3>
          <p class="mt-2 text-sm leading-relaxed text-fg-muted">
            Meningkatkan teknologi pascapanen, pengemasan standar, penyimpanan, dan diversifikasi produk agar bernilai jual tinggi.
          </p>
        </article>
      </div>
    </div>
  </section>
</template>
