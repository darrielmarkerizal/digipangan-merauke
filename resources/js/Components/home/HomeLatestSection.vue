<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowRight } from '@lucide/vue'
import { Icon, EmptyState } from '@/Components/ui'
import ProductCard from '@/Components/product/ProductCard.vue'
import type { ProductCard as ProductCardType } from '@/types/home'

defineProps<{
  showLatest: boolean
  latestList: ProductCardType[]
}>()
</script>

<template>
  <section v-if="showLatest" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
    <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
      <div class="space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-brand">
          Keterbaruan Lahan Komunitas
        </span>
        <h2 class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl">
          Hasil Panen Terbaru
        </h2>
        <p class="text-base text-fg-muted">
          Produk segar yang baru masuk etalase resmi dari seluruh kelompok tani Merauke.
        </p>
      </div>

      <Link
        href="/produk"
        class="inline-flex items-center gap-2 rounded-full border border-border/80 bg-white px-6 py-3 text-sm font-bold text-fg shadow-sm transition-all hover:border-brand/40 hover:bg-card"
      >
        <span>Lihat Semua Produk</span>
        <Icon :icon="ArrowRight" :size="16" />
      </Link>
    </div>

    <div
      v-if="latestList.length > 0"
      class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
    >
      <ProductCard
        v-for="prod in latestList"
        :key="prod.slug"
        :product="prod"
      />
    </div>

    <div v-else class="mt-12">
      <EmptyState
        title="Belum ada produk baru lainnya"
        description="Semua produk saat ini sudah ditampilkan pada kategori unggulan di atas."
      />
    </div>
  </section>
</template>
