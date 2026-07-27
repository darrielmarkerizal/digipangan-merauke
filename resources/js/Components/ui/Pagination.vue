<script setup lang="ts">
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ChevronLeft, ChevronRight } from '@lucide/vue'

export interface PaginationMeta {
  current_page: number
  from?: number | null
  last_page: number
  per_page?: number
  to?: number | null
  total?: number
}

export interface PaginationLinks {
  first?: string | null
  last?: string | null
  prev?: string | null
  next?: string | null
}

const props = defineProps<{
  meta?: PaginationMeta
  links?: PaginationLinks | Array<{ url: string | null; label: string; active: boolean }>
  perPageOptions?: number[]
}>()

const emits = defineEmits<{
  (e: 'page-change', page: number): void
  (e: 'per-page-change', perPage: number): void
}>()

const currentPage = computed(() => props.meta?.current_page ?? 1)
const lastPage = computed(() => props.meta?.last_page ?? 1)
const fromItem = computed(() => props.meta?.from ?? 0)
const toItem = computed(() => props.meta?.to ?? 0)
const totalItems = computed(() => props.meta?.total ?? 0)
const currentPerPage = computed(() => props.meta?.per_page ?? 15)

const perPageList = computed(() => props.perPageOptions || [10, 15, 25, 50, 100])

const prevUrl = computed(() => {
  if (props.links && !Array.isArray(props.links)) {
    return props.links.prev ?? null
  }
  return null
})

const nextUrl = computed(() => {
  if (props.links && !Array.isArray(props.links)) {
    return props.links.next ?? null
  }
  return null
})

const pageNumbers = computed(() => {
  const current = currentPage.value
  const last = Math.max(lastPage.value, 1)
  const delta = 1
  const range: (number | string)[] = []

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
      range.push(i)
    } else if (range[range.length - 1] !== '...') {
      range.push('...')
    }
  }

  return range
})

function getPageUrl(page: number | string): string | null {
  if (typeof page !== 'number') return null
  if (typeof window === 'undefined') return null
  const currentUrl = new URL(window.location.href)
  currentUrl.searchParams.set('page', String(page))
  return `${currentUrl.pathname}${currentUrl.search}`
}

function handlePerPageChange(e: Event) {
  const val = Number((e.target as HTMLSelectElement).value)
  const currentUrl = new URL(window.location.href)
  currentUrl.searchParams.set('per_page', String(val))
  currentUrl.searchParams.set('page', '1')

  emits('per-page-change', val)
  router.get(currentUrl.pathname, Object.fromEntries(currentUrl.searchParams.entries()), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}
</script>

<template>
  <div class="flex flex-col items-center justify-between gap-4 py-3 sm:flex-row border-t border-border/60 pt-4">
    <div class="flex flex-wrap items-center gap-3 text-xs font-medium text-fg-muted">
      <div class="flex items-center gap-2">
        <span>Tampilkan</span>
        <select
          :value="currentPerPage"
          class="rounded-lg border border-border/80 bg-white px-2 py-1 text-xs font-bold text-fg shadow-xs focus:border-brand focus:outline-none cursor-pointer"
          @change="handlePerPageChange"
        >
          <option v-for="option in perPageList" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
        <span>baris</span>
      </div>

      <span class="text-border">|</span>

      <div>
        <template v-if="totalItems > 0 && fromItem && toItem">
          Menampilkan <span class="font-semibold text-fg">{{ fromItem }}</span> - <span class="font-semibold text-fg">{{ toItem }}</span> dari <span class="font-semibold text-fg">{{ totalItems }}</span> data
        </template>
        <template v-else>
          Total <span class="font-semibold text-fg">{{ totalItems }}</span> data
        </template>
      </div>
    </div>

    <div class="flex items-center gap-1.5">
      <Component
        :is="prevUrl ? Link : 'button'"
        :href="prevUrl || undefined"
        :disabled="!prevUrl"
        class="inline-flex size-8 items-center justify-center rounded-lg border border-border/80 bg-white text-fg transition-all hover:bg-muted/50 disabled:pointer-events-none disabled:opacity-40 shadow-xs"
        aria-label="Halaman Sebelumnya"
        @click="!prevUrl && currentPage > 1 && emits('page-change', currentPage - 1)"
      >
        <ChevronLeft class="size-4" />
      </Component>

      <template v-for="(page, idx) in pageNumbers" :key="idx">
        <span
          v-if="page === '...'"
          class="flex size-8 items-center justify-center text-xs text-fg-muted select-none"
        >
          ...
        </span>
        <Component
          :is="getPageUrl(page) && page !== currentPage ? Link : 'button'"
          v-else
          :href="getPageUrl(page) || undefined"
          :class="[
            'inline-flex size-8 items-center justify-center rounded-lg text-xs font-semibold transition-all shadow-xs',
            page === currentPage
              ? 'bg-brand text-white border border-brand'
              : 'bg-white border border-border/80 text-fg hover:bg-muted/50'
          ]"
          @click="typeof page === 'number' && emits('page-change', page)"
        >
          {{ page }}
        </Component>
      </template>

      <Component
        :is="nextUrl ? Link : 'button'"
        :href="nextUrl || undefined"
        :disabled="!nextUrl"
        class="inline-flex size-8 items-center justify-center rounded-lg border border-border/80 bg-white text-fg transition-all hover:bg-muted/50 disabled:pointer-events-none disabled:opacity-40 shadow-xs"
        aria-label="Halaman Selanjutnya"
        @click="!nextUrl && currentPage < lastPage && emits('page-change', currentPage + 1)"
      >
        <ChevronRight class="size-4" />
      </Component>
    </div>
  </div>
</template>
