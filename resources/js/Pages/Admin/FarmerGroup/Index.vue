<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Kelompok Tani (Gapoktan)"
    subtitle="Manajemen kelembagaan kelompok tani atau koperasi tani per desa dan distrik transmigrasi di Merauke."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Gapoktan" />
    </template>

    <AdminPageCard
      title="Daftar Kelembagaan Kelompok Tani"
      description="Wadah kolaborasi formal antarpetani untuk memperkuat daya tawar pasar dan manajemen pascapanen."
    />
  </AdminLayout>
</template>
