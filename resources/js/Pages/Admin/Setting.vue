<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Save } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Pengaturan Situs"
    subtitle="Konfigurasi parameter global aplikasi, teks naratif halaman Tentang, kontak WhatsApp Admin, serta metadata SEO."
  >
    <template #actions>
      <AdminHeaderAction :icon="Save" label="Simpan Perubahan" />
    </template>

    <AdminPageCard
      title="Konfigurasi Key-Value Situs"
      description="Manajemen parameter site_settings agar nama kontak, nomor telepon, dan narasi institusi tidak hardcoded."
    />
  </AdminLayout>
</template>
