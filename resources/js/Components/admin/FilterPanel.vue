<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Filter, X, RotateCcw, Check } from '@lucide/vue'
import { Icon, Button, Select } from '@/Components/ui'
import { onClickOutside } from '@vueuse/core'

export interface FilterOption {
  id: number | string
  name: string
}

const props = defineProps<{
  module?: 'product' | 'post' | 'user'
  categories?: FilterOption[]
  regions?: FilterOption[]
  authors?: FilterOption[]
  roles?: string[]
}>()

const isOpen = ref(false)
const panelRef = ref<HTMLElement | null>(null)
const buttonRef = ref<HTMLElement | null>(null)

onClickOutside(
  panelRef,
  () => {
    isOpen.value = false
  },
  { ignore: [buttonRef] }
)

const getUrlParam = (key: string) => {
  if (typeof window === 'undefined') return ''
  return new URLSearchParams(window.location.search).get(key) || ''
}

const filters = reactive({
  category: getUrlParam('filter[product_category_id]'),
  region: getUrlParam('filter[region_id]'),
  stock: getUrlParam('filter[stock_available]'),
  active: getUrlParam('filter[is_active]'),
  status: getUrlParam('filter[status]'),
  author_id: getUrlParam('filter[author_id]'),
  role: getUrlParam('filter[role]'),
})

const activeFilterCount = computed(() => {
  if (props.module === 'post') {
    return [filters.status, filters.author_id].filter(Boolean).length
  } else if (props.module === 'user') {
    return [filters.role, filters.active].filter(Boolean).length
  }
  return [filters.category, filters.region, filters.stock, filters.active].filter(Boolean).length
})

function applyFilters() {
  if (typeof window === 'undefined') return
  const currentUrl = new URL(window.location.href)
  const params = new URLSearchParams(currentUrl.search)

  const mapping: Record<string, string> = props.module === 'post' ? {
    'filter[status]': filters.status,
    'filter[author_id]': filters.author_id,
  } : props.module === 'user' ? {
    'filter[role]': filters.role,
    'filter[is_active]': filters.active,
  } : {
    'filter[product_category_id]': filters.category,
    'filter[region_id]': filters.region,
    'filter[stock_available]': filters.stock,
    'filter[is_active]': filters.active,
  }

  Object.entries(mapping).forEach(([key, val]) => {
    if (val) {
      params.set(key, val)
    } else {
      params.delete(key)
    }
  })

  params.set('page', '1')

  router.get(currentUrl.pathname, Object.fromEntries(params.entries()), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })

  isOpen.value = false
}

function resetFilters() {
  filters.category = ''
  filters.region = ''
  filters.stock = ''
  filters.active = ''
  filters.status = ''
  filters.author_id = ''
  filters.role = ''

  if (typeof window === 'undefined') return
  const currentUrl = new URL(window.location.href)
  const params = new URLSearchParams(currentUrl.search)

  const activeKeys = props.module === 'post' 
    ? ['filter[status]', 'filter[author_id]'] 
    : props.module === 'user'
    ? ['filter[role]', 'filter[is_active]']
    : ['filter[product_category_id]', 'filter[region_id]', 'filter[stock_available]', 'filter[is_active]']
    
  activeKeys.forEach((key) => params.delete(key))
  params.set('page', '1')

  router.get(currentUrl.pathname, Object.fromEntries(params.entries()), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })

  isOpen.value = false
}
</script>

<template>
  <div class="relative inline-block text-left">
    <button
      ref="buttonRef"
      type="button"
      class="inline-flex items-center gap-2 rounded-full border border-border/80 bg-white px-4 py-2 text-sm font-semibold text-fg shadow-xs transition-all hover:bg-muted/50 focus:outline-none cursor-pointer"
      :class="{ 'border-brand ring-2 ring-brand/20 bg-brand-weak/30': isOpen || activeFilterCount > 0 }"
      @click="isOpen = !isOpen"
    >
      <Icon :icon="Filter" :size="16" class="text-fg-muted" />
      <span>Filter {{ module === 'post' ? 'Status & Penulis' : module === 'user' ? 'Peran & Status' : 'Kategori & Distrik' }}</span>
      <span
        v-if="activeFilterCount > 0"
        class="flex size-5 items-center justify-center rounded-full bg-brand text-[11px] font-bold text-white"
      >
        {{ activeFilterCount }}
      </span>
    </button>

    <div
      v-if="isOpen"
      ref="panelRef"
      class="absolute right-0 z-30 mt-2 w-80 sm:w-96 rounded-2xl border border-border/80 bg-white p-5 shadow-xl transition-all"
    >
      <div class="flex items-center justify-between border-b border-border/60 pb-3">
        <div class="flex items-center gap-2">
          <Icon :icon="Filter" :size="16" class="text-brand" />
          <h3 class="text-sm font-bold text-fg">Filter {{ module === 'post' ? 'Berita' : module === 'user' ? 'Pengguna' : 'Produk' }}</h3>
        </div>
        <button
          type="button"
          class="rounded-lg p-1 text-fg-muted hover:bg-muted hover:text-fg transition-colors cursor-pointer"
          @click="isOpen = false"
        >
          <Icon :icon="X" :size="16" />
        </button>
      </div>

      <div v-if="module === 'post'" class="mt-4 space-y-4">
        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Status Publikasi</label>
          <Select v-model="filters.status" class="!min-h-9 text-xs">
            <option value="">Semua Status</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
          </Select>
        </div>

        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Penulis Berita</label>
          <Select v-model="filters.author_id" class="!min-h-9 text-xs">
            <option value="">Semua Penulis</option>
            <option v-for="author in (authors || [])" :key="author.id" :value="author.id">
              {{ author.name }}
            </option>
          </Select>
        </div>
      </div>

      <div v-else-if="module === 'user'" class="mt-4 space-y-4">
        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Peran Pengguna</label>
          <Select v-model="filters.role" class="!min-h-9 text-xs">
            <option value="">Semua Peran</option>
            <option v-for="role in roles" :key="role" :value="role">
              {{ role === 'super_admin' ? 'Super Admin' : 'Admin' }}
            </option>
          </Select>
        </div>

        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Status Aktif</label>
          <Select v-model="filters.active" class="!min-h-9 text-xs">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </Select>
        </div>
      </div>

      <div v-else class="mt-4 space-y-4">
        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Kategori Produk</label>
          <Select v-model="filters.category" class="!min-h-9 text-xs">
            <option value="">Semua Kategori</option>
            <option v-for="cat in (categories || [])" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </Select>
        </div>

        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Kawasan Transmigrasi / Distrik</label>
          <Select v-model="filters.region" class="!min-h-9 text-xs">
            <option value="">Semua Kawasan</option>
            <option v-for="reg in (regions || [])" :key="reg.id" :value="reg.id">
              {{ reg.name }}
            </option>
          </Select>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-1">
          <div>
            <label class="block text-xs font-bold text-fg mb-1.5">Status Stok</label>
            <Select v-model="filters.stock" class="!min-h-9 text-xs">
              <option value="">Semua Status</option>
              <option value="1">Stok Tersedia</option>
              <option value="0">Stok Habis</option>
            </Select>
          </div>

          <div>
            <label class="block text-xs font-bold text-fg mb-1.5">Status Tampilan</label>
            <Select v-model="filters.active" class="!min-h-9 text-xs">
              <option value="">Semua Status</option>
              <option value="1">Aktif (Publik)</option>
              <option value="0">Draft / Nonaktif</option>
            </Select>
          </div>
        </div>
      </div>

      <div class="mt-5 flex items-center justify-between border-t border-border/60 pt-3">
        <button
          type="button"
          class="inline-flex items-center gap-1.5 text-xs font-semibold text-fg-muted hover:text-danger transition-colors cursor-pointer"
          @click="resetFilters"
        >
          <Icon :icon="RotateCcw" :size="14" />
          <span>Reset Filter</span>
        </button>

        <div class="flex items-center gap-2">
          <Button size="sm" type="button" @click="applyFilters">
            <Icon :icon="Check" :size="14" />
            <span>Terapkan</span>
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
