<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Pusat Bantuan &amp; FAQ"
    subtitle="Manajemen daftar pertanyaan umum dan jawaban seputar pengoperasian platform oleh pembeli maupun petani."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah FAQ" />
    </template>

    <AdminPageCard
      title="Pusat Bantuan / Pertanyaan Umum"
      description="Daftar tanya jawab untuk mempermudah literasi digital pengguna dan kontak admin."
    />
  </AdminLayout>
</template>
