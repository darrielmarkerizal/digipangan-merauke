<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Kategori Produk"
    subtitle="Manajemen klasifikasi pangan: Sayuran Segar, Buah-buahan, Hasil Perkebunan, Peternakan, dan Olahan Pangan."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Kategori" />
    </template>

    <AdminPageCard
      title="Klasifikasi Kategori Komoditas"
      description="Halaman manajemen 5 kategori utama yang terhubung langsung dengan filter etalase di halaman publik."
    />
  </AdminLayout>
</template>
