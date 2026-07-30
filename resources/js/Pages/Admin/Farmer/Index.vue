<script setup lang="ts">
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FarmerFilterPanel from "@/Components/admin/Farmer/FarmerFilterPanel.vue"
import {
  Plus,
  Search,
  Edit2,
  Trash2,
  ArrowUp,
  ArrowDown,
  ArrowUpDown,
  Users,
  Eye,
} from '@lucide/vue'
import {
  Icon,
  Badge,
  Button,
  Pagination,
  EmptyState,
  AlertDialog,
} from '@/Components/ui'
import { useSearch } from '@/Composables/useSearch'
import { useSort } from '@/Composables/useSort'
import { formatTanggal } from '@/lib/format'
import { toast } from 'vue-sonner'

interface FarmerItem {
  id: number
  name: string
  slug?: string
  phone: string
  land_area_ha?: string | number | null
  is_active: boolean
  region?: { id: number; name: string }
  village?: { id: number; name: string } | null
  farmer_group?: { id: number; name: string } | null
  commodities?: Array<{ id: number; name: string }>
  photo?: { id?: number; original?: string; thumb?: string; card?: string } | null
  created_at?: string
  updated_at?: string
}

const props = defineProps<{
  farmers?: {
    data: FarmerItem[]
    links?: any
    meta?: any
  }
  regions?: Array<{ id: number; name: string }>
  villages?: Array<{ id: number; name: string; region_id?: number }>
  farmerGroups?: Array<{ id: number; name: string; region_id?: number; village_id?: number }>
}>()

const { search } = useSearch()
const { getSortDirection, sortBy } = useSort()

const farmerList = computed(() => {
  const rawData = props.farmers?.data
  const items = Array.isArray(rawData) ? rawData : (rawData as any)?.data || []
  return items.map((f: any) => ({
    id: f.id,
    name: f.name,
    phone: f.phone,
    land_area_ha: f.land_area_ha,
    is_active: f.is_active,
    region: f.region,
    village: f.village,
    farmer_group: f.farmer_group,
    commodities: f.commodities || [],
    photo: f.photo,
    created_at: f.created_at,
  }))
})

const getInitials = (name: string) => {
  if (!name) return 'PT'
  return name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}

const executeDelete = (id: number) => {
  router.delete(`/admin/petani/${id}`, {
    onSuccess: () => {
      toast.success('Data petani berhasil dihapus.')
    },
    onError: () => {
      toast.error('Gagal menghapus data petani (kemungkinan terkait dengan produk panen).')
    },
  })
}
</script>

<template>
  <AdminLayout
    title="Kelola Profil Petani &amp; Mitra"
    subtitle="Manajemen data petani lokal, koneksi nomor WhatsApp aktif, foto profil asli, dan penetapan komoditas budidaya."
  >
    <template #actions>
      <Link href="/admin/petani/create">
        <Button size="sm" class="gap-1.5 font-semibold">
          <Icon :icon="Plus" :size="16" />
          <span>Tambah Petani</span>
        </Button>
      </Link>
    </template>

    <div class="space-y-4">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-md">
          <Icon
            :icon="Search"
            :size="16"
            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted pointer-events-none"
          />
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama petani atau nomor telepon..."
            class="w-full h-10 pl-10 pr-4 rounded-xl border border-border/80 bg-white text-sm text-fg placeholder:text-fg-muted/60 transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
          />
        </div>

        <div class="flex items-center justify-end">
          <FarmerFilterPanel
            :regions="regions"
            :villages="villages"
            :farmer-groups="farmerGroups"
          />
        </div>
      </div>

      <div class="overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-fg">
            <thead class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider">
              <tr>
                <th
                  scope="col"
                  class="px-5 py-3.5 cursor-pointer select-none group hover:text-brand"
                  @click="sortBy('name')"
                >
                  <div class="flex items-center gap-1.5">
                    <span>Petani / Mitra</span>
                    <Icon
                      v-if="getSortDirection('name') === 'asc'"
                      :icon="ArrowUp"
                      :size="14"
                      class="text-brand font-bold"
                    />
                    <Icon
                      v-else-if="getSortDirection('name') === 'desc'"
                      :icon="ArrowDown"
                      :size="14"
                      class="text-brand font-bold"
                    />
                    <Icon
                      v-else
                      :icon="ArrowUpDown"
                      :size="14"
                      class="opacity-40 group-hover:opacity-100 transition-opacity"
                    />
                  </div>
                </th>
                <th scope="col" class="px-4 py-3.5">Distrik &amp; Desa</th>
                <th scope="col" class="px-4 py-3.5">Kelompok Tani</th>
                <th scope="col" class="px-4 py-3.5">Luas Lahan</th>
                <th scope="col" class="px-4 py-3.5">Komoditas Budidaya</th>
                <th scope="col" class="px-4 py-3.5">Status</th>
                <th scope="col" class="px-4 py-3.5">Tanggal Dibuat</th>
                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
              <tr v-if="farmerList.length === 0">
                <td colspan="8" class="px-5 py-12 text-center">
                  <EmptyState
                    title="Tidak ada petani ditemukan"
                    description="Belum ada data petani yang sesuai dengan pencarian atau filter."
                    :icon="Users"
                  />
                </td>
              </tr>
              <tr
                v-for="item in farmerList"
                :key="item.id"
                class="transition-colors hover:bg-muted/20"
              >
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="size-10 shrink-0 overflow-hidden rounded-full bg-brand-weak/30 border border-brand/20 flex items-center justify-center font-bold text-brand text-sm">
                      <img
                        v-if="item.photo?.thumb || item.photo?.original"
                        :src="item.photo.thumb || item.photo.original"
                        :alt="item.name"
                        class="size-full object-cover"
                      />
                      <span v-else>{{ getInitials(item.name) }}</span>
                    </div>
                    <div>
                      <Link :href="`/admin/petani/${item.id}`" class="font-bold text-fg hover:text-brand transition-colors">
                        {{ item.name }}
                      </Link>
                      <div class="text-xs text-fg-muted mt-0.5">
                        <span>{{ item.phone }}</span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <div class="font-semibold text-fg">{{ item.region?.name || '-' }}</div>
                  <div class="text-xs text-fg-muted">{{ item.village?.name || '-' }}</div>
                </td>
                <td class="px-4 py-4 text-xs text-fg font-medium">
                  {{ item.farmer_group?.name || '-' }}
                </td>
                <td class="px-4 py-4 text-xs text-fg">
                  {{ item.land_area_ha ? `${item.land_area_ha} Ha` : '-' }}
                </td>
                <td class="px-4 py-4">
                  <div class="flex flex-wrap gap-1 max-w-xs">
                    <Badge
                      v-for="c in item.commodities"
                      :key="c.id"
                      variant="brand"
                      class="text-[11px] px-2 py-0.5"
                    >
                      {{ c.name }}
                    </Badge>
                    <span
                      v-if="!item.commodities || item.commodities.length === 0"
                      class="text-xs text-fg-muted"
                    >
                      -
                    </span>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <Badge :variant="item.is_active ? 'success' : 'neutral'">
                    {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                  </Badge>
                </td>
                <td class="px-4 py-4 text-xs text-fg-muted">
                  {{ item.created_at ? formatTanggal(item.created_at) : '-' }}
                </td>
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <Link
                      :href="`/admin/petani/${item.id}`"
                      class="inline-flex size-8 items-center justify-center rounded-lg border border-border bg-white text-fg-muted hover:bg-muted hover:text-fg transition-colors"
                      title="Lihat Detail Petani"
                    >
                      <Icon :icon="Eye" :size="14" />
                    </Link>
                    <Link
                      :href="`/admin/petani/${item.id}/edit`"
                      class="inline-flex size-8 items-center justify-center rounded-lg border border-border bg-white text-fg-muted hover:bg-muted hover:text-fg transition-colors"
                      title="Edit Petani"
                    >
                      <Icon :icon="Edit2" :size="14" />
                    </Link>
                    <AlertDialog
                      title="Hapus Profil Petani?"
                      :description="`Apakah Anda yakin ingin menghapus data petani '${item.name}'? Tindakan ini tidak dapat dibatalkan.`"
                      confirm-label="Ya, Hapus Petani"
                      cancel-label="Batal"
                      :destructive="true"
                      @confirm="executeDelete(item.id)"
                    >
                      <template #trigger>
                        <button
                          type="button"
                          class="inline-flex size-8 items-center justify-center rounded-lg border border-danger/30 bg-danger-weak/40 text-danger transition-all hover:bg-danger hover:text-white hover:border-danger shadow-xs cursor-pointer"
                          title="Hapus Petani"
                        >
                          <Icon :icon="Trash2" :size="14" />
                        </button>
                      </template>
                    </AlertDialog>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <Pagination
        :links="farmers?.links"
        :meta="farmers?.meta"
      />
    </div>
  </AdminLayout>
</template>
