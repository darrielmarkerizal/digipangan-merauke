<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Mitra Bisnis &amp; Institusi"
    subtitle="Manajemen logo dan nama mitra strategis pada halaman Tentang (UGM, Kementerian Transmigrasi RI, Pemerintah Daerah)."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Mitra" />
    </template>

    <AdminPageCard
      title="Direktori Mitra Kolaborasi"
      description="Mitra kelembagaan yang mendukung keberlanjutan program pendampingan ekonomi komunitas."
    />
  </AdminLayout>
</template>
