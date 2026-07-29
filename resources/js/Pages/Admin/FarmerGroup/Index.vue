<script setup lang="ts">
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
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

interface FarmerGroupItem {
  id: number
  name: string
  farmers_count?: number
  created_at?: string
  updated_at?: string
}

const props = defineProps<{
  farmerGroups?: {
    data: FarmerGroupItem[]
    links?: any
    meta?: any
  }
}>()

const { search } = useSearch()
const { getSortDirection, sortBy } = useSort()

const farmerGroupList = computed(() => {
  const rawData = props.farmerGroups?.data
  const items = Array.isArray(rawData) ? rawData : (rawData as any)?.data || []
  return items.map((c: any) => ({
    id: c.id,
    name: c.name,
    farmers_count: c.farmers_count ?? 0,
    created_at: c.created_at,
  }))
})

const executeDelete = (id: number) => {
  router.delete(`/admin/kelompok-tani/${id}`, {
    onSuccess: () => {
      toast.success('Kelompok tani berhasil dihapus.')
    },
    onError: () => {
      toast.error('Gagal menghapus kelompok tani (kemungkinan masih digunakan oleh petani terkait).')
    },
  })
}
</script>

<template>
  <AdminLayout
    title="Kelola Kelompok Tani (Gapoktan)"
    subtitle="Manajemen kelembagaan kelompok tani atau koperasi tani per desa dan distrik transmigrasi di Merauke."
  >
    <template #actions>
      <Link href="/admin/kelompok-tani/create">
        <Button size="sm" class="gap-1.5 font-semibold">
          <Icon :icon="Plus" :size="16" />
          <span>Tambah Kelompok Tani</span>
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
            placeholder="Cari kelompok tani..."
            class="w-full h-10 pl-10 pr-4 rounded-xl border border-border/80 bg-white text-sm text-fg placeholder:text-fg-muted/60 transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
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
                    <span>Nama Kelompok Tani</span>
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
                <th scope="col" class="px-4 py-3.5">Jumlah Petani</th>
                <th scope="col" class="px-4 py-3.5">Tanggal Dibuat</th>
                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
              <tr v-if="farmerGroupList.length === 0">
                <td colspan="4" class="px-5 py-12 text-center">
                  <EmptyState
                    title="Tidak ada kelompok tani ditemukan"
                    description="Belum ada data kelompok tani yang sesuai dengan pencarian."
                    :icon="Users"
                  />
                </td>
              </tr>
              <tr
                v-for="item in farmerGroupList"
                :key="item.id"
                class="transition-colors hover:bg-muted/20"
              >
                <td class="px-5 py-4 font-bold text-fg">
                  {{ item.name }}
                </td>
                <td class="px-4 py-4">
                  <Badge variant="brand" class="gap-1">
                    <Icon :icon="Users" :size="12" />
                    <span>{{ item.farmers_count }} Petani</span>
                  </Badge>
                </td>
                <td class="px-4 py-4 text-xs text-fg-muted">
                  {{ item.created_at ? formatTanggal(item.created_at) : '-' }}
                </td>
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <Link :href="`/admin/kelompok-tani/${item.id}`">
                      <Button
                        variant="secondary"
                        size="sm"
                        class="size-8 p-0"
                        title="Lihat Detail Kelompok Tani"
                      >
                        <Icon :icon="Eye" :size="14" />
                      </Button>
                    </Link>
                    <Link :href="`/admin/kelompok-tani/${item.id}/edit`">
                      <Button
                        variant="secondary"
                        size="sm"
                        class="size-8 p-0"
                        title="Edit Kelompok Tani"
                      >
                        <Icon :icon="Edit2" :size="14" />
                      </Button>
                    </Link>
                    <AlertDialog
                      title="Hapus Kelompok Tani?"
                      :description="`Apakah Anda yakin ingin menghapus kelompok tani '${item.name}'? Tindakan ini tidak dapat dibatalkan.`"
                      confirm-label="Ya, Hapus"
                      cancel-label="Batal"
                      :destructive="true"
                      @confirm="executeDelete(item.id)"
                    >
                      <template #trigger>
                        <button
                          type="button"
                          class="inline-flex size-8 items-center justify-center rounded-lg border border-danger/30 bg-danger-weak/40 text-danger transition-all hover:bg-danger hover:text-white hover:border-danger shadow-xs cursor-pointer"
                          title="Hapus Kelompok Tani"
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
        :links="farmerGroups?.links"
        :meta="farmerGroups?.meta"
      />
    </div>
  </AdminLayout>
</template>
