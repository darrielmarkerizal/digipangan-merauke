<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import FarmerLayout from '@/Layouts/FarmerLayout.vue'
import {
  ShoppingBasket,
  CheckCircle2,
  AlertTriangle,
  MapPin,
  Users,
  Plus,
} from '@lucide/vue'
import { Icon, Badge, Button, EmptyState } from '@/Components/ui'

const props = defineProps<{
  farmer?: {
    name?: string
    region?: { name: string }
    village?: { name: string } | null
    farmer_group?: { name: string } | null
    land_area_ha?: number | string | null
  }
  metrics?: {
    total_products?: number
    active_products?: number
    out_of_stock_products?: number
  }
  recent_products?: Array<{
    id: number
    name: string
    price: number | string
    is_active: boolean
    stock_available: boolean
    category?: { name: string } | null
    unit?: { name: string; symbol?: string } | null
  }>
}>()

const statCards = [
  { key: 'total_products', label: 'Total Produk', icon: ShoppingBasket },
  { key: 'active_products', label: 'Produk Aktif', icon: CheckCircle2 },
  { key: 'out_of_stock_products', label: 'Stok Habis', icon: AlertTriangle },
] as const
</script>

<template>
  <FarmerLayout
    title="Dashboard"
    :subtitle="`Selamat datang, ${farmer?.name || 'Petani'}.`"
  >
    <template #actions>
      <Link href="/petani/dashboard/produk/tambah">
        <Button size="sm" class="gap-1.5 font-semibold">
          <Icon :icon="Plus" :size="16" />
          <span>Tambah Produk</span>
        </Button>
      </Link>
    </template>

    <div class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div
          v-for="card in statCards"
          :key="card.key"
          class="rounded-2xl border border-border/80 bg-white p-5 shadow-xs flex items-center gap-4"
        >
          <span class="flex size-11 items-center justify-center rounded-xl bg-brand-weak text-brand shrink-0">
            <Icon :icon="card.icon" :size="20" />
          </span>
          <div>
            <p class="text-2xl font-extrabold text-fg">{{ metrics?.[card.key] ?? 0 }}</p>
            <p class="text-xs font-medium text-fg-muted">{{ card.label }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-4">
        <div class="flex items-center gap-2 border-b border-border/60 pb-3">
          <Icon :icon="MapPin" :size="18" class="text-brand" />
          <h3 class="text-sm font-bold text-fg">Profil Wilayah</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
          <div>
            <p class="text-xs font-medium text-fg-muted">Distrik / Kawasan</p>
            <p class="font-semibold text-fg">{{ farmer?.region?.name || '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-fg-muted">Desa / Kampung</p>
            <p class="font-semibold text-fg">{{ farmer?.village?.name || '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-fg-muted">Kelompok Tani</p>
            <p class="font-semibold text-fg flex items-center gap-1.5">
              <Icon :icon="Users" :size="14" class="text-brand" />
              {{ farmer?.farmer_group?.name || 'Mandiri / Tanpa Kelompok' }}
            </p>
          </div>
        </div>
        <Link href="/petani/dashboard/profil" class="inline-block text-xs font-semibold text-brand hover:text-brand-strong">
          Perbarui profil &rarr;
        </Link>
      </div>

      <div class="rounded-2xl border border-border/80 bg-white shadow-xs">
        <div class="flex items-center justify-between border-b border-border/60 p-5">
          <h3 class="text-sm font-bold text-fg">Produk Terbaru</h3>
          <Link href="/petani/dashboard/produk" class="text-xs font-semibold text-brand hover:text-brand-strong">
            Lihat Semua
          </Link>
        </div>

        <EmptyState
          v-if="!recent_products || recent_products.length === 0"
          title="Belum ada produk"
          description="Tambahkan produk hasil panen pertama Anda."
          :icon="ShoppingBasket"
          class="p-10"
        />

        <div v-else class="divide-y divide-border/60">
          <div
            v-for="product in recent_products"
            :key="product.id"
            class="flex items-center justify-between gap-4 p-4 px-5"
          >
            <div>
              <p class="text-sm font-bold text-fg">{{ product.name }}</p>
              <p class="text-xs text-fg-muted">{{ product.category?.name || 'Umum' }}</p>
            </div>
            <div class="flex items-center gap-2">
              <Badge :variant="product.is_active ? 'brand' : 'neutral'">
                {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
              </Badge>
              <Badge :variant="product.stock_available ? 'brand' : 'danger'">
                {{ product.stock_available ? 'Stok Ada' : 'Stok Habis' }}
              </Badge>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FarmerLayout>
</template>
