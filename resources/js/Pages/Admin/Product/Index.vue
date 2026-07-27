<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus, Search, Filter } from '@lucide/vue'
import { Icon } from '@/Components/ui'
</script>

<template>
  <AdminLayout
    title="Kelola Produk &amp; Komoditas"
    subtitle="Manajemen katalog komoditas hasil panen, penentuan harga, berat/satuan, serta status ketersediaan stok di 3 kawasan transmigrasi Merauke."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Produk" />
    </template>

    <div class="space-y-4">
      <div
        class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
      >
        <div class="relative flex-1 max-w-md">
          <Icon
            :icon="Search"
            :size="18"
            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted"
          />
          <input
            type="search"
            placeholder="Cari produk komoditas atau nama petani..."
            class="w-full rounded-xl border border-border/80 bg-white pl-10 pr-4 py-2 text-sm text-fg placeholder:text-fg-muted/70 focus:border-brand focus:outline-none shadow-xs"
          />
        </div>

        <AdminHeaderAction
          :icon="Filter"
          label="Filter Kategori &amp; Distrik"
          variant="secondary"
        />
      </div>

      <AdminPageCard
        title="Tabel Manajemen Produk Pangan"
        description="Katalog produk terintegrasi langsung dengan etalase pasar di halaman beranda publik."
      />
    </div>
  </AdminLayout>
</template>
