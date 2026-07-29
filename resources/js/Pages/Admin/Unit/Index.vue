<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  Plus,
  Search,
  Edit2,
  Trash2,
  ArrowUp,
  ArrowDown,
  ArrowUpDown,
  X,
  Save,
  Scale,
  Layers,
} from '@lucide/vue'
import {
  Icon,
  Badge,
  Button,
  Pagination,
  EmptyState,
  AlertDialog,
  Field,
  Input,
  Switch,
} from '@/Components/ui'
import { useSearch } from '@/Composables/useSearch'
import { useSort } from '@/Composables/useSort'
import { formatTanggal } from '@/lib/format'
import { toast } from 'vue-sonner'

interface UnitItem {
  id: number
  name: string
  symbol: string
  is_active: boolean
  products_count?: number
  created_at?: string
  updated_at?: string
}

const props = defineProps<{
  units?: {
    data: UnitItem[]
    links?: any
    meta?: any
  }
}>()

const { search } = useSearch()
const { getSortDirection, sortBy } = useSort()

const unitList = computed(() => {
  const rawData = props.units?.data
  const items = Array.isArray(rawData) ? rawData : (rawData as any)?.data || []
  return items.map((u: any) => ({
    id: u.id,
    name: u.name,
    symbol: u.symbol,
    is_active: u.is_active ?? true,
    products_count: u.products_count ?? 0,
    created_at: u.created_at,
  }))
})

const isModalOpen = ref(false)
const editingUnit = ref<UnitItem | null>(null)

const form = useForm({
  name: '',
  symbol: '',
  is_active: true,
})

const openCreateModal = () => {
  editingUnit.value = null
  form.reset()
  form.is_active = true
  form.clearErrors()
  isModalOpen.value = true
}

const openEditModal = (item: UnitItem) => {
  editingUnit.value = item
  form.name = item.name
  form.symbol = item.symbol
  form.is_active = item.is_active
  form.clearErrors()
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingUnit.value = null
  form.reset()
  form.clearErrors()
}

const handleSubmit = () => {
  if (editingUnit.value) {
    form.put(`/admin/satuan/${editingUnit.value.id}`, {
      onSuccess: () => {
        toast.success('Satuan berhasil diperbarui.')
        closeModal()
      },
      onError: () => {
        toast.error('Gagal memperbarui satuan.')
      },
    })
  } else {
    form.post('/admin/satuan', {
      onSuccess: () => {
        toast.success('Satuan baru berhasil ditambahkan.')
        closeModal()
      },
      onError: () => {
        toast.error('Gagal menambahkan satuan.')
      },
    })
  }
}

const executeDelete = (id: number) => {
  router.delete(`/admin/satuan/${id}`, {
    onSuccess: () => {
      toast.success('Satuan berhasil dihapus.')
    },
    onError: () => {
      toast.error('Gagal menghapus satuan (kemungkinan masih digunakan oleh produk).')
    },
  })
}
</script>

<template>
  <AdminLayout
    title="Kelola Satuan & Berat"
    subtitle="Manajemen standar satuan takaran pangan lokal seperti kg, ikat, karung, gram, atau ekor agar tidak hardcoded."
  >
    <template #actions>
      <Button size="sm" class="gap-1.5 font-semibold" @click="openCreateModal">
        <Icon :icon="Plus" :size="16" />
        <span>Tambah Satuan</span>
      </Button>
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
            placeholder="Cari satuan..."
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
                    <span>Nama Satuan</span>
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
                <th scope="col" class="px-4 py-3.5">Simbol</th>
                <th scope="col" class="px-4 py-3.5">Jumlah Produk</th>
                <th scope="col" class="px-4 py-3.5">Status</th>
                <th scope="col" class="px-4 py-3.5">Tanggal Dibuat</th>
                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
              <tr v-if="unitList.length === 0">
                <td colspan="6" class="px-5 py-12 text-center">
                  <EmptyState
                    title="Tidak ada satuan ditemukan"
                    description="Belum ada data satuan yang sesuai dengan pencarian."
                    :icon="Scale"
                  />
                </td>
              </tr>
              <tr
                v-for="item in unitList"
                :key="item.id"
                class="transition-colors hover:bg-muted/20"
              >
                <td class="px-5 py-4 font-bold text-fg">
                  {{ item.name }}
                </td>
                <td class="px-4 py-4">
                  <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-md bg-muted text-xs font-semibold font-mono text-fg">
                    {{ item.symbol }}
                  </span>
                </td>
                <td class="px-4 py-4">
                  <Badge variant="brand" class="gap-1">
                    <Icon :icon="Layers" :size="12" />
                    <span>{{ item.products_count }} Produk</span>
                  </Badge>
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
                    <Button
                      variant="secondary"
                      size="sm"
                      class="size-8 p-0"
                      title="Edit Satuan"
                      @click="openEditModal(item)"
                    >
                      <Icon :icon="Edit2" :size="14" />
                    </Button>
                    <AlertDialog
                      title="Hapus Satuan?"
                      :description="`Apakah Anda yakin ingin menghapus satuan '${item.name} (${item.symbol})'? Tindakan ini tidak dapat dibatalkan.`"
                      confirm-label="Ya, Hapus Satuan"
                      cancel-label="Batal"
                      :destructive="true"
                      @confirm="executeDelete(item.id)"
                    >
                      <template #trigger>
                        <button
                          type="button"
                          class="inline-flex size-8 items-center justify-center rounded-lg border border-danger/30 bg-danger-weak/40 text-danger transition-all hover:bg-danger hover:text-white hover:border-danger shadow-xs cursor-pointer"
                          title="Hapus Satuan"
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
        :links="units?.links"
        :meta="units?.meta"
      />
    </div>

    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-fg/50 backdrop-blur-xs transition-opacity"
    >
      <div class="w-full max-w-md overflow-hidden rounded-card border border-border bg-card shadow-soft animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between border-b border-border px-6 py-4">
          <h3 class="text-base font-bold text-fg">
            {{ editingUnit ? 'Edit Satuan' : 'Tambah Satuan' }}
          </h3>
          <button
            type="button"
            class="rounded-lg p-1 text-fg-muted hover:bg-muted hover:text-fg transition-colors"
            @click="closeModal"
          >
            <Icon :icon="X" :size="18" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <Field label="Nama Satuan" :error="form.errors.name" required>
            <Input
              v-model="form.name"
              placeholder="Contoh: Kilogram, Ikat, Karung"
              required
            />
          </Field>

          <Field label="Simbol" :error="form.errors.symbol" required>
            <Input
              v-model="form.symbol"
              placeholder="Contoh: kg, ikat, krg"
              required
            />
          </Field>

          <Field label="Status" :error="form.errors.is_active">
            <div class="flex items-center gap-3">
              <Switch v-model="form.is_active" />
              <span
                class="text-sm text-fg cursor-pointer select-none"
                @click="form.is_active = !form.is_active"
              >
                {{ form.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
          </Field>

          <div class="flex justify-end gap-3 pt-2">
            <Button type="button" variant="secondary" @click="closeModal">
              Batal
            </Button>
            <Button type="submit" :loading="form.processing" class="gap-1.5">
              <Icon :icon="Save" :size="16" />
              <span>Simpan</span>
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
