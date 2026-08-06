<script setup lang="ts">
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import FarmerLayout from '@/Layouts/FarmerLayout.vue'
import {
  Plus,
  Search,
  Edit2,
  Trash2,
  ShoppingBasket,
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
import { toast } from 'vue-sonner'

const props = defineProps<{
  products?: {
    data: Array<{
      id: number
      name: string
      price: string | number
      stock_available: boolean
      is_active: boolean
      category?: { name: string }
      unit?: { name: string; symbol?: string }
      photos?: Array<{ thumb: string; original: string }>
    }>
    links?: any
    meta?: any
  }
}>()

const { search } = useSearch()

const productList = computed(() => {
  const rawData = props.products?.data
  const items = Array.isArray(rawData) ? rawData : (rawData as any)?.data || []
  return items.map((p: any) => ({
    id: p.id,
    name: p.name,
    category: p.category?.name || 'Umum',
    price:
      typeof p.price === 'number'
        ? `Rp ${new Intl.NumberFormat('id-ID').format(p.price)}`
        : `Rp ${p.price}`,
    unit: p.unit?.symbol || p.unit?.name || '',
    stock_available: p.stock_available,
    is_active: p.is_active,
    image_url: p.photos?.[0]?.thumb || p.photos?.[0]?.original || null,
  }))
})

const executeDelete = (id: number) => {
  router.delete(`/petani/dashboard/produk/${id}`, {
    onSuccess: () => {
      toast.success('Produk berhasil dihapus.')
    },
    onError: () => {
      toast.error('Gagal menghapus produk.')
    },
  })
}
</script>

<template>
  <FarmerLayout
    title="Produk Saya"
    subtitle="Kelola katalog produk hasil panen yang Anda pasarkan di etalase publik DigiPangan Merauke."
  >
    <template #actions>
      <Link href="/petani/dashboard/produk/tambah">
        <Button size="sm" class="gap-1.5 font-semibold">
          <Icon :icon="Plus" :size="16" />
          <span>Tambah Produk</span>
        </Button>
      </Link>
    </template>

    <div class="space-y-4">
      <div class="relative flex-1 max-w-md">
        <Icon
          :icon="Search"
          :size="16"
          class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted pointer-events-none"
        />
        <input
          v-model="search"
          type="text"
          placeholder="Cari nama produk..."
          class="w-full h-10 pl-10 pr-4 rounded-xl border border-border/80 bg-white text-sm text-fg placeholder:text-fg-muted/60 transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
        />
      </div>

      <div class="overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-fg">
            <thead class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-5 py-3.5">Produk</th>
                <th scope="col" class="px-4 py-3.5">Kategori</th>
                <th scope="col" class="px-4 py-3.5">Harga</th>
                <th scope="col" class="px-4 py-3.5">Status</th>
                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
              <tr v-if="productList.length === 0">
                <td colspan="5" class="px-5 py-12 text-center">
                  <EmptyState
                    title="Belum ada produk"
                    description="Tambahkan produk hasil panen pertama Anda untuk mulai berjualan."
                    :icon="ShoppingBasket"
                  />
                </td>
              </tr>
              <tr
                v-for="item in productList"
                :key="item.id"
                class="transition-colors hover:bg-muted/20"
              >
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="size-10 shrink-0 overflow-hidden rounded-lg bg-muted/40">
                      <img v-if="item.image_url" :src="item.image_url" alt="" class="size-full object-cover" />
                    </div>
                    <span class="font-bold text-fg">{{ item.name }}</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-fg-muted">{{ item.category }}</td>
                <td class="px-4 py-4 font-semibold text-fg">
                  {{ item.price }} <span class="text-xs text-fg-muted">/ {{ item.unit }}</span>
                </td>
                <td class="px-4 py-4">
                  <div class="flex flex-wrap gap-1.5">
                    <Badge :variant="item.is_active ? 'brand' : 'neutral'">
                      {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                    </Badge>
                    <Badge :variant="item.stock_available ? 'brand' : 'danger'">
                      {{ item.stock_available ? 'Stok Ada' : 'Stok Habis' }}
                    </Badge>
                  </div>
                </td>
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <Link :href="`/petani/dashboard/produk/${item.id}/edit`">
                      <Button
                        variant="secondary"
                        size="sm"
                        class="size-8 p-0"
                        title="Edit Produk"
                      >
                        <Icon :icon="Edit2" :size="14" />
                      </Button>
                    </Link>
                    <AlertDialog
                      title="Hapus Produk?"
                      :description="`Apakah Anda yakin ingin menghapus produk '${item.name}'? Tindakan ini tidak dapat dibatalkan.`"
                      confirm-label="Ya, Hapus"
                      cancel-label="Batal"
                      :destructive="true"
                      @confirm="executeDelete(item.id)"
                    >
                      <template #trigger>
                        <button
                          type="button"
                          class="inline-flex size-8 items-center justify-center rounded-lg border border-danger/30 bg-danger-weak/40 text-danger transition-all hover:bg-danger hover:text-white hover:border-danger shadow-xs cursor-pointer"
                          title="Hapus Produk"
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

      <Pagination :links="products?.links" :meta="products?.meta" />
    </div>
  </FarmerLayout>
</template>
