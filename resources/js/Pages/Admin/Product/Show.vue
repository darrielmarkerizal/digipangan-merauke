<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  ArrowLeft,
  Edit2,
  Trash2,
  MapPin,
  User,
  Calendar,
  Sparkles,
  Star,
  Tag,
  Phone,
  Layers,
  Image as ImageIcon,
} from '@lucide/vue'
import { Icon, Badge, Button, AlertDialog } from '@/Components/ui'
import { formatRupiah, formatTanggal } from '@/lib/format'
import { toast } from 'vue-sonner'

const props = defineProps<{
  product: {
    id: number
    name: string
    slug: string
    description?: string
    price: string | number
    weight_value?: string | number
    stock_available: boolean
    is_featured: boolean
    is_region_featured: boolean
    is_active: boolean
    category?: { id: number; name: string }
    unit?: { id: number; name: string; symbol?: string }
    farmer?: { id: number; name: string; phone?: string; farmer_group?: { name: string } }
    region?: { id: number; name: string }
    photos?: Array<{ id: number; thumb: string; original: string; card?: string }>
    created_at?: string
    updated_at?: string
  }
}>()

const activePhotoIndex = ref(0)

const photoList = computed(() => {
  if (props.product.photos && props.product.photos.length > 0) {
    return props.product.photos.map((p) => p.original || p.card || p.thumb).filter(Boolean)
  }
  return []
})

const currentPhoto = computed(() => (photoList.value.length > 0 ? photoList.value[activePhotoIndex.value] || photoList.value[0] : null))

const formattedPrice = computed(() => formatRupiah(props.product.price))

const executeDelete = () => {
  router.delete(`/admin/produk/${props.product.id}`, {
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
  <AdminLayout
    :title="`Detail Produk: ${product.name}`"
    subtitle="Tampilan rincian komoditas hasil panen, informasi spesifikasi, asal usul petani mitra, serta status publikasi etalase."
  >
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <Link
          href="/admin/produk"
          class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
        >
          <Icon :icon="ArrowLeft" :size="16" />
          <span>Kembali ke Daftar Produk</span>
        </Link>

        <div class="flex items-center gap-2.5">
          <Link :href="`/admin/produk/${product.id}/edit`">
            <Button size="sm" variant="secondary" class="gap-1.5 font-semibold">
              <Icon :icon="Edit2" :size="15" />
              <span>Edit Produk</span>
            </Button>
          </Link>
          <AlertDialog
            title="Hapus Produk Pangan?"
            :description="`Apakah Anda yakin ingin menghapus produk '${product.name}'? Tindakan ini tidak dapat dibatalkan.`"
            confirm-label="Ya, Hapus Produk"
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
                <span>Hapus Produk</span>
              </Button>
            </template>
          </AlertDialog>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 space-y-3">
          <div class="overflow-hidden rounded-2xl border border-border/80 bg-white p-2 shadow-xs">
            <div class="relative aspect-4/3 overflow-hidden rounded-xl bg-muted/30">
              <img
                v-if="currentPhoto"
                :src="currentPhoto"
                :alt="product.name"
                class="size-full object-cover transition-all duration-300"
              />
              <div v-else class="flex size-full flex-col items-center justify-center gap-2 bg-muted/40 text-fg-muted">
                <Icon :icon="ImageIcon" :size="48" class="opacity-40" />
                <span class="text-xs font-semibold text-fg-muted">Tidak ada foto produk</span>
              </div>
              <div class="absolute top-3 left-3 flex flex-wrap gap-1.5 z-10">
                <Badge :variant="product.stock_available ? 'success' : 'danger'" class="shadow-sm">
                  {{ product.stock_available ? 'Stok Tersedia' : 'Stok Habis' }}
                </Badge>
                <Badge :variant="product.is_active ? 'brand' : 'neutral'" class="shadow-sm">
                  {{ product.is_active ? 'Publik' : 'Draft / Nonaktif' }}
                </Badge>
              </div>
            </div>
          </div>

          <div v-if="photoList.length > 1" class="flex items-center gap-2.5 overflow-x-auto pb-1">
            <button
              v-for="(photo, idx) in photoList"
              :key="idx"
              type="button"
              class="relative size-16 shrink-0 overflow-hidden rounded-xl border-2 transition-all cursor-pointer"
              :class="activePhotoIndex === idx ? 'border-brand ring-2 ring-brand/30 scale-95' : 'border-border/60 opacity-70 hover:opacity-100'"
              @click="activePhotoIndex = idx"
            >
              <img :src="photo" :alt="`Foto ${idx + 1}`" class="size-full object-cover" />
            </button>
          </div>
        </div>

        <div class="lg:col-span-7 space-y-6">
          <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
            <div>
              <div class="flex items-center gap-2 text-xs font-semibold text-fg-muted mb-2">
                <span class="inline-flex items-center gap-1 rounded-md bg-muted px-2 py-0.5 font-bold text-fg">
                  <Icon :icon="Tag" :size="12" />
                  {{ product.category?.name || 'Umum' }}
                </span>
                <span>•</span>
                <span class="text-fg-muted">ID: #PRD-00{{ product.id }}</span>
              </div>

              <h1 class="text-2xl font-extrabold text-fg tracking-tight leading-snug">
                {{ product.name }}
              </h1>

              <div class="flex flex-wrap items-center gap-2 mt-3">
                <span v-if="product.is_featured" class="inline-flex items-center gap-1 rounded-lg bg-accent-weak px-2.5 py-1 text-xs font-bold text-accent border border-accent/20">
                  <Icon :icon="Star" :size="13" class="fill-current" />
                  <span>Unggulan Utama</span>
                </span>
                <span v-if="product.is_region_featured" class="inline-flex items-center gap-1 rounded-lg bg-brand-weak px-2.5 py-1 text-xs font-bold text-brand border border-brand/20">
                  <Icon :icon="Sparkles" :size="13" />
                  <span>Unggulan Kawasan</span>
                </span>
              </div>
            </div>

            <div class="rounded-xl border border-brand/20 bg-brand-weak/30 p-4">
              <span class="text-xs font-semibold text-fg-muted uppercase tracking-wider block">Harga Jual Acuan</span>
              <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-brand">{{ formattedPrice }}</span>
                <span class="text-sm font-semibold text-fg-muted">/ {{ product.unit?.name || 'Kg' }} {{ product.unit?.symbol ? `(${product.unit.symbol})` : '' }}</span>
              </div>
            </div>

            <div class="space-y-2 border-t border-border/60 pt-4">
              <h3 class="text-xs font-bold uppercase tracking-wider text-fg-muted">Deskripsi &amp; Mutu Panen</h3>
              <p class="text-sm text-fg leading-relaxed whitespace-pre-line">
                {{ product.description || 'Belum ada deskripsi detail untuk produk ini.' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-border/80 bg-white p-5 shadow-xs space-y-3">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="User" :size="18" />
            </span>
            <h3 class="text-sm font-bold text-fg">Petani Producer Mitra</h3>
          </div>
          <div class="space-y-2 text-xs">
            <div>
              <span class="text-fg-muted block">Nama Petani:</span>
              <span class="font-bold text-fg text-sm">{{ product.farmer?.name || 'Petani Mitra' }}</span>
            </div>
            <div v-if="product.farmer?.phone">
              <span class="text-fg-muted block">Kontak Telepon:</span>
              <div class="flex items-center gap-1 font-semibold text-fg">
                <Icon :icon="Phone" :size="13" class="text-brand" />
                <span>{{ product.farmer.phone }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-border/80 bg-white p-5 shadow-xs space-y-3">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="MapPin" :size="18" />
            </span>
            <h3 class="text-sm font-bold text-fg">Kawasan / Asal Usul</h3>
          </div>
          <div class="space-y-2 text-xs">
            <div>
              <span class="text-fg-muted block">Kawasan Transmigrasi / Distrik:</span>
              <span class="font-bold text-fg text-sm">{{ product.region?.name || 'Kawasan Merauke' }}</span>
            </div>
            <div>
              <span class="text-fg-muted block">Provinsi / Wilayah:</span>
              <span class="font-semibold text-fg">Papua Selatan, Indonesia</span>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-border/80 bg-white p-5 shadow-xs space-y-3">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="Layers" :size="18" />
            </span>
            <h3 class="text-sm font-bold text-fg">Spesifikasi &amp; Riwayat</h3>
          </div>
          <div class="space-y-2 text-xs">
            <div>
              <span class="text-fg-muted block">Takaran / Nilai Berat:</span>
              <span class="font-semibold text-fg">
                {{ product.weight_value ? `${product.weight_value} ${product.unit?.symbol || product.unit?.name || 'Kg'}` : '-' }}
              </span>
            </div>
            <div v-if="product.created_at">
              <span class="text-fg-muted block">Dibuat Pada:</span>
              <div class="flex items-center gap-1 font-medium text-fg">
                <Icon :icon="Calendar" :size="13" class="text-fg-muted" />
                <span>{{ formatTanggal(product.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
