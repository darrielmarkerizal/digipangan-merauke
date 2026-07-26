<script setup lang="ts">
import RegionCard from '@/Components/region/RegionCard.vue'
import type { RegionCard as RegionCardType } from '@/types/home'

defineProps<{
  regions: RegionCardType[]
  grain: string
}>()
</script>

<template>
  <section class="border-y border-border/80 bg-white py-24 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-brand">
          3 Kawasan Transmigrasi Merauke
        </span>
        <h2 class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl">
          Jelajahi Potensi Alam per Distrik
        </h2>
        <p class="text-base text-fg-muted">
          Dari sawah pangan Muting, hortikultura Ulilin, hingga perkebunan Elikobel. Setiap kawasan
          memiliki karakter agraris unik yang siap disuplai ke pasar.
        </p>
      </div>

      <div class="mt-14 grid grid-cols-1 gap-8 md:grid-cols-3">
        <RegionCard
          v-for="region in regions"
          :key="region.slug"
          :region="region"
        />
      </div>
    </div>
  </section>
</template>
