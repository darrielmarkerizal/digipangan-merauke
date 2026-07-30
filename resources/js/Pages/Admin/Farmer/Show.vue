<script setup lang="ts">
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  ArrowLeft,
  Edit2,
  Trash2,
  MapPin,
  Sprout,
  Package,
  ExternalLink,
} from '@lucide/vue'
import {
  Icon,
  Badge,
  Button,
  AlertDialog,
  EmptyState,
} from '@/Components/ui'
import { formatRupiah, formatTanggal } from '@/lib/format'
import { toast } from 'vue-sonner'

interface FarmerDetail {
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
  products?: Array<{
    id: number
    name: string
    price: string | number
    stock_available: boolean
    is_active: boolean
    unit?: { id: number; name: string; symbol?: string } | null
  }>
  photo?: { id?: number; original?: string; thumb?: string; card?: string } | null
  created_at?: string
  updated_at?: string
}

const props = defineProps<{
  farmer: FarmerDetail
}>()

const getInitials = (name: string) => {
  if (!name) return 'PT'
  return name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}

const cleanPhoneForWa = computed(() => {
  if (!props.farmer.phone) return ''
  let cleaned = props.farmer.phone.replace(/\D/g, '')
  if (cleaned.startsWith('0')) {
    cleaned = '62' + cleaned.substring(1)
  }
  return cleaned
})

const executeDelete = () => {
  router.delete(`/admin/petani/${props.farmer.id}`, {
    onSuccess: () => {
      toast.success('Data profil petani berhasil dihapus.')
    },
    onError: () => {
      toast.error('Gagal menghapus petani. Kemungkinan masih ada produk yang terikat.')
    },
  })
}
</script>

<template>
  <AdminLayout
    :title="`Detail Petani: ${farmer.name}`"
    subtitle="Rincian informasi identitas, nomor WhatsApp aktif, desa asal, serta daftar produk dan komoditas budidaya petani."
  >
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <Link
          href="/admin/petani"
          class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
        >
          <Icon :icon="ArrowLeft" :size="16" />
          <span>Kembali ke Direktori Petani</span>
        </Link>

        <div class="flex items-center gap-2.5">
          <Link :href="`/admin/petani/${farmer.id}/edit`">
            <Button size="sm" variant="secondary" class="gap-1.5 font-semibold">
              <Icon :icon="Edit2" :size="15" />
              <span>Edit Profil Petani</span>
            </Button>
          </Link>
          <AlertDialog
            title="Hapus Profil Petani?"
            :description="`Apakah Anda yakin ingin menghapus profil petani '${farmer.name}'? Tindakan ini tidak dapat dibatalkan.`"
            confirm-label="Ya, Hapus Petani"
            cancel-label="Batal"
            :destructive="true"
            @confirm="executeDelete"
          >
            <template #trigger>
              <Button
                size="sm"
                variant="danger-secondary"
                class="gap-1.5 font-semibold cursor-pointer"
              >
                <Icon :icon="Trash2" :size="15" />
                <span>Hapus Petani</span>
              </Button>
            </template>
          </AlertDialog>
        </div>
      </div>

      <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <div
              class="size-24 sm:size-28 shrink-0 overflow-hidden rounded-full bg-brand-weak/30 border-2 border-brand/20 flex items-center justify-center font-bold text-brand text-2xl shadow-xs"
            >
              <img
                v-if="farmer.photo?.card || farmer.photo?.original || farmer.photo?.thumb"
                :src="farmer.photo?.card || farmer.photo?.original || farmer.photo?.thumb"
                :alt="farmer.name"
                class="size-full object-cover"
              />
              <span v-else>{{ getInitials(farmer.name) }}</span>
            </div>

            <div class="space-y-2">
              <div class="flex flex-wrap items-center gap-2">
                <Badge :variant="farmer.is_active ? 'success' : 'neutral'" class="text-xs">
                  {{ farmer.is_active ? 'Aktif (Ditampilkan)' : 'Nonaktif (Disembunyikan)' }}
                </Badge>
                <span class="text-xs text-fg-muted">ID: #PTN-00{{ farmer.id }}</span>
              </div>

              <h1 class="text-2xl sm:text-3xl font-extrabold text-fg tracking-tight">
                {{ farmer.name }}
              </h1>

              <div class="flex flex-wrap items-center gap-4 text-xs text-fg-muted pt-1">
                <div>
                  <span>Nomor Kontak: </span>
                  <span class="font-bold text-fg">{{ farmer.phone }}</span>
                </div>
                <div>
                  <span>Luas Lahan: </span>
                  <span class="font-bold text-fg">{{ farmer.land_area_ha ? `${farmer.land_area_ha} Ha` : 'Tidak tercatat' }}</span>
                </div>
                <div v-if="farmer.created_at">
                  <span>Terdaftar: </span>
                  <span class="font-semibold text-fg">{{ formatTanggal(farmer.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2 self-start md:self-center">
            <a
              v-if="cleanPhoneForWa"
              :href="`https://wa.me/${cleanPhoneForWa}`"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-1.5 rounded-xl border border-success/30 bg-success-weak/40 px-4 py-2 text-xs font-bold text-success hover:bg-success hover:text-white transition-all shadow-xs"
            >
              <span>Chat WhatsApp</span>
              <Icon :icon="ExternalLink" :size="14" />
            </a>

          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-4">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="MapPin" :size="18" />
            </span>
            <h3 class="text-sm font-bold text-fg">Wilayah &amp; Kelompok Tani</h3>
          </div>

          <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="rounded-xl bg-muted/20 p-3 border border-border/60">
              <dt class="text-fg-muted font-medium">Distrik / Kawasan</dt>
              <dd class="font-bold text-fg text-sm mt-1">{{ farmer.region?.name || '-' }}</dd>
            </div>
            <div class="rounded-xl bg-muted/20 p-3 border border-border/60">
              <dt class="text-fg-muted font-medium">Desa / Kampung</dt>
              <dd class="font-bold text-fg text-sm mt-1">{{ farmer.village?.name || '-' }}</dd>
            </div>
            <div class="rounded-xl bg-muted/20 p-3 border border-border/60">
              <dt class="text-fg-muted font-medium">Kelompok Tani</dt>
              <dd class="font-bold text-fg text-sm mt-1">{{ farmer.farmer_group?.name || 'Mandiri' }}</dd>
            </div>
          </dl>
        </div>

        <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-4">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="Sprout" :size="18" />
            </span>
            <h3 class="text-sm font-bold text-fg">Komoditas Budidaya</h3>
          </div>

          <div class="flex flex-wrap gap-2 pt-1">
            <Badge
              v-for="c in farmer.commodities"
              :key="c.id"
              variant="brand"
              class="text-xs px-3 py-1.5"
            >
              {{ c.name }}
            </Badge>
            <span
              v-if="!farmer.commodities || farmer.commodities.length === 0"
              class="text-xs text-fg-muted italic"
            >
              Belum ada komoditas budidaya yang ditandai untuk petani ini.
            </span>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
        <div class="flex items-center justify-between border-b border-border/60 pb-4">
          <div class="flex items-center gap-2.5">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="Package" :size="18" />
            </span>
            <div>
              <h3 class="text-sm font-bold text-fg">Produk Panen Terdaftar</h3>
              <p class="text-xs text-fg-muted">Daftar komoditas panen yang diproduksi dan dijual oleh petani ini.</p>
            </div>
          </div>
          <Badge variant="neutral" class="text-xs font-bold">
            {{ farmer.products?.length || 0 }} Produk
          </Badge>
        </div>

        <div v-if="farmer.products && farmer.products.length > 0" class="overflow-x-auto">
          <table class="w-full text-left text-sm text-fg">
            <thead class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-4 py-3">Nama Produk Panen</th>
                <th scope="col" class="px-4 py-3">Harga Acuan</th>
                <th scope="col" class="px-4 py-3">Status Stok</th>
                <th scope="col" class="px-4 py-3">Publikasi</th>
                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
              <tr
                v-for="prod in farmer.products"
                :key="prod.id"
                class="hover:bg-muted/20 transition-colors"
              >
                <td class="px-4 py-3.5 font-bold text-fg">
                  {{ prod.name }}
                </td>
                <td class="px-4 py-3.5 font-semibold text-brand">
                  {{ formatRupiah(prod.price) }} / {{ prod.unit?.name || 'Kg' }}
                </td>
                <td class="px-4 py-3.5">
                  <Badge :variant="prod.stock_available ? 'success' : 'danger'" class="text-[11px]">
                    {{ prod.stock_available ? 'Tersedia' : 'Habis' }}
                  </Badge>
                </td>
                <td class="px-4 py-3.5">
                  <Badge :variant="prod.is_active ? 'brand' : 'neutral'" class="text-[11px]">
                    {{ prod.is_active ? 'Publik' : 'Draft' }}
                  </Badge>
                </td>
                <td class="px-4 py-3.5 text-right">
                  <Link
                    :href="`/admin/produk/${prod.id}`"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-brand hover:underline"
                  >
                    <span>Lihat Produk</span>
                    <Icon :icon="ExternalLink" :size="12" />
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="py-8 text-center">
          <EmptyState
            title="Belum ada produk terdaftar"
            description="Petani ini belum dihubungkan dengan produk panen apa pun di etalase."
            :icon="Package"
          />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
