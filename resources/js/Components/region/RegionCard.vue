<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowRight, Sprout } from '@lucide/vue'
import { Icon } from '@/Components/ui'
import { useRupiah } from '@/Composables/useRupiah'
import type { RegionCard } from '@/types/home'

const props = defineProps<{ region: RegionCard }>()
const { formatAngka } = useRupiah()

const stats = computed(() => [
  { label: 'Produk', value: props.region.products_count },
  { label: 'Kelompok', value: props.region.farmer_groups_count },
  { label: 'Desa', value: props.region.villages_count },
])
</script>

<template>
  <Link :href="`/wilayah/${region.slug}`" class="group block h-full">
    <article
      class="flex h-full flex-col overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs transition-[transform,box-shadow] duration-300 ease-out hover:-translate-y-1 hover:shadow-soft"
    >
      <div class="relative aspect-[2/1] sm:aspect-[16/9] overflow-hidden bg-muted/30">
        <img
          v-if="region.cover"
          :src="region.cover.card"
          :alt="`Wilayah ${region.name}`"
          loading="lazy"
          class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
        />
        <div
          v-else
          class="flex size-full items-center justify-center bg-brand-weak/30 text-brand/40"
          aria-hidden="true"
        >
          <!-- Using a Map-related icon for regions -->
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
      </div>

      <div class="flex flex-1 flex-col p-6">
        <h3
          class="text-xl font-bold text-fg transition-colors group-hover:text-brand"
        >
          {{ region.name }}
        </h3>

        <dl class="my-5 grid grid-cols-3 gap-2 sm:gap-3">
          <div v-for="s in stats" :key="s.label" class="min-w-0">
            <dt class="truncate text-[11px] sm:text-xs font-medium text-fg-muted">
              {{ s.label }}
            </dt>
            <dd class="mt-1 text-base sm:text-xl font-bold tabular-nums text-fg">
              {{ formatAngka(s.value) }}
            </dd>
          </div>
        </dl>

        <div
          class="mt-auto flex items-center justify-between gap-2 border-t border-border/80 pt-4"
        >
          <span class="text-sm font-semibold text-brand">Lihat wilayah</span>
          <span
            class="flex size-8 items-center justify-center rounded-full bg-brand-weak text-brand transition-transform duration-300 ease-premium group-hover:translate-x-0.5"
            aria-hidden="true"
          >
            <Icon :icon="ArrowRight" :size="16" />
          </span>
        </div>
      </div>
    </article>
  </Link>
</template>
