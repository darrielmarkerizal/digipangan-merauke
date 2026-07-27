<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Filter } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Log Aktivitas Admin (Audit Log)"
    subtitle="Riwayat perubahan data dan jejak rekam aktivitas admin untuk menjaga transparansi, keandalan, dan keamanan sistem."
  >
    <template #actions>
      <AdminHeaderAction
        :icon="Filter"
        label="Filter Waktu"
        variant="secondary"
      />
    </template>

    <AdminPageCard
      title="Jejak Audit Sistem"
      description="Mencatat setiap pembuatan, pengubahan, dan penghapusan data secara otomatis dari modul owen-it/laravel-auditing."
    />
  </AdminLayout>
</template>
