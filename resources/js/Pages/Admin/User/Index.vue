<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola User &amp; Hak Akses (RBAC)"
    subtitle="Manajemen akun pengguna dan penugasan role/permission menggunakan spatie/laravel-permission (Admin Sentral &amp; Petani)."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah User" />
    </template>

    <AdminPageCard
      title="Manajemen Pengguna &amp; Role Akses"
      description="Tabel kontrol peran Super Admin, Admin, dan Mitra Petani untuk menjaga keamanan sistem."
    />
  </AdminLayout>
</template>
