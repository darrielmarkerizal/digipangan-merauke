<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Master Komoditas"
    subtitle="Manajemen jenis komoditas utama agraris Merauke (cabai, tomat, kopi, beras, bawang) untuk pemetaan pivot daerah."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Komoditas" />
    </template>

    <AdminPageCard
      title="Daftar Komoditas Unggulan Merauke"
      description="Tabel komoditas agraris untuk menghubungkan profil petani dengan potensi distrik transmigrasi."
    />
  </AdminLayout>
</template>
