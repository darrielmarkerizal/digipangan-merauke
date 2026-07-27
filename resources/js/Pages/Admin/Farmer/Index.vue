<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus, Search } from '@lucide/vue'
import { Icon } from '@/Components/ui'
</script>

<template>
  <AdminLayout
    title="Kelola Profil Petani &amp; Mitra"
    subtitle="Manajemen data petani lokal, koneksi nomor WhatsApp aktif, foto profil asli, dan penetapan komoditas budidaya."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Petani" />
    </template>

    <div class="space-y-4">
      <div class="relative max-w-md">
        <Icon
          :icon="Search"
          :size="18"
          class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted"
        />
        <input
          type="search"
          placeholder="Cari nama petani atau kelompok tani..."
          class="w-full rounded-xl border border-border/80 bg-white pl-10 pr-4 py-2 text-sm text-fg placeholder:text-fg-muted/70 focus:border-brand focus:outline-none shadow-xs"
        />
      </div>

      <AdminPageCard
        title="Direktori Petani Merauke"
        description="Daftar petani dari kawasan Muting, Ulilin, dan Elikobel beserta kelola kontak langsung WhatsApp."
      />
    </div>
  </AdminLayout>
</template>
