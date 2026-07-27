<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Satuan &amp; Berat"
    subtitle="Manajemen standar satuan takaran pangan lokal seperti kg, ikat, karung, gram, atau ekor agar tidak hardcoded."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tambah Satuan" />
    </template>

    <AdminPageCard
      title="Master Satuan Komoditas"
      description="Daftar satuan pangan resmi yang akan dipilih pada saat input produk oleh Admin ataupun Petani."
    />
  </AdminLayout>
</template>
