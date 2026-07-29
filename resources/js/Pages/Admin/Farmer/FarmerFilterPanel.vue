<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Filter, X, RotateCcw, Check } from '@lucide/vue'
import { Icon, Button, Select } from '@/Components/ui'
import { onClickOutside } from '@vueuse/core'

export interface FilterOption {
  id: number | string
  name: string
  region_id?: number | string
  village_id?: number | string
}

const props = defineProps<{
  regions?: FilterOption[]
  villages?: FilterOption[]
  farmerGroups?: FilterOption[]
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

const filterKeys = [
  'filter[region_id]',
  'filter[village_id]',
  'filter[farmer_group_id]',
  'filter[is_active]',
] as const

const filters = reactive({
  region: getUrlParam('filter[region_id]'),
  village: getUrlParam('filter[village_id]'),
  farmerGroup: getUrlParam('filter[farmer_group_id]'),
  active: getUrlParam('filter[is_active]'),
})

const filteredVillages = computed(() => {
  if (!filters.region) return props.villages || []
  return (props.villages || []).filter((v) => Number(v.region_id) === Number(filters.region))
})

const filteredFarmerGroups = computed(() => {
  if (!filters.region) return props.farmerGroups || []
  return (props.farmerGroups || []).filter((g) => Number(g.region_id) === Number(filters.region))
})

watch(
  () => filters.region,
  (newRegion, oldRegion) => {
    if (oldRegion !== undefined && newRegion !== oldRegion) {
      const vExists = filteredVillages.value.some((v) => String(v.id) === String(filters.village))
      if (!vExists) filters.village = ''
      const gExists = filteredFarmerGroups.value.some((g) => String(g.id) === String(filters.farmerGroup))
      if (!gExists) filters.farmerGroup = ''
    }
  }
)

const activeFilterCount = computed(() => {
  return Object.values(filters).filter(Boolean).length
})

function applyFilters() {
  if (typeof window === 'undefined') return
  const currentUrl = new URL(window.location.href)
  const params = new URLSearchParams(currentUrl.search)

  const mapping: Record<string, string> = {
    'filter[region_id]': filters.region,
    'filter[village_id]': filters.village,
    'filter[farmer_group_id]': filters.farmerGroup,
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
  filters.region = ''
  filters.village = ''
  filters.farmerGroup = ''
  filters.active = ''

  if (typeof window === 'undefined') return
  const currentUrl = new URL(window.location.href)
  const params = new URLSearchParams(currentUrl.search)

  filterKeys.forEach((key) => params.delete(key))
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
      <span>Filter Distrik &amp; Status</span>
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
          <h3 class="text-sm font-bold text-fg">Filter Direktori Petani</h3>
        </div>
        <button
          type="button"
          class="rounded-lg p-1 text-fg-muted hover:bg-muted hover:text-fg transition-colors cursor-pointer"
          @click="isOpen = false"
        >
          <Icon :icon="X" :size="16" />
        </button>
      </div>

      <div class="mt-4 space-y-4">
        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Kawasan Transmigrasi / Distrik</label>
          <Select v-model="filters.region" class="!min-h-9 text-xs">
            <option value="">Semua Distrik</option>
            <option v-for="reg in (regions || [])" :key="reg.id" :value="reg.id">
              {{ reg.name }}
            </option>
          </Select>
        </div>

        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Desa / Kampung</label>
          <Select v-model="filters.village" class="!min-h-9 text-xs">
            <option value="">Semua Desa / Kampung</option>
            <option v-for="vil in filteredVillages" :key="vil.id" :value="vil.id">
              {{ vil.name }}
            </option>
          </Select>
        </div>

        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Kelompok Tani</label>
          <Select v-model="filters.farmerGroup" class="!min-h-9 text-xs">
            <option value="">Semua Kelompok Tani</option>
            <option v-for="grp in filteredFarmerGroups" :key="grp.id" :value="grp.id">
              {{ grp.name }}
            </option>
          </Select>
        </div>

        <div>
          <label class="block text-xs font-bold text-fg mb-1.5">Status Tampilan</label>
          <Select v-model="filters.active" class="!min-h-9 text-xs">
            <option value="">Semua Status</option>
            <option value="1">Aktif (Ditampilkan)</option>
            <option value="0">Nonaktif (Disembunyikan)</option>
          </Select>
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
