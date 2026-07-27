<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Master Desa"
    subtitle="Manajemen daftar desa atau kampung di bawah 3 distrik transmigrasi (contoh: 11 Desa di Distrik Ulilin)."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Desa" />
    </template>

    <AdminPageCard
      title="Direktori Permukiman Desa"
      description="Tabel permukiman desa transmigrasi untuk klasifikasi lokasi lahan petani dan kelompok tani."
    />
  </AdminLayout>
</template>
