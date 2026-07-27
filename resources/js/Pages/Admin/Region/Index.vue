<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola 3 Kawasan Transmigrasi"
    subtitle="Manajemen profil distrik transmigrasi Muting, Ulilin, dan Elikobel: deskripsi agraris, luas panen, dan galeri foto."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Kawasan" />
    </template>

    <AdminPageCard
      title="Direktori Distrik Transmigrasi"
      description="Manajemen statistik dan konten promosi daerah kawasan pangan Merauke (Muting, Ulilin, Elikobel)."
    />
  </AdminLayout>
</template>
