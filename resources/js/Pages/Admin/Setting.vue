<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SettingForm from '@/Components/admin/SettingForm.vue'
</script>

<template>
  <AdminLayout
    title="Pengaturan Situs"
    subtitle="Konfigurasi parameter global aplikasi, teks naratif halaman Tentang, kontak WhatsApp Admin, serta metadata SEO."
  >
    <SettingForm />
  </AdminLayout>
</template>
