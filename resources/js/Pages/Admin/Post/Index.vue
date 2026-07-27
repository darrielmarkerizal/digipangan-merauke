<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPageCard from '@/Components/admin/AdminPageCard.vue'
import AdminHeaderAction from '@/Components/admin/AdminHeaderAction.vue'
import { Plus } from '@lucide/vue'
</script>

<template>
  <AdminLayout
    title="Kelola Berita &amp; Pelatihan"
    subtitle="Manajemen publikasi artikel kegiatan panen, pelatihan literasi digital, informasi harga pasar, dan edukasi masyarakat."
  >
    <template #actions>
      <AdminHeaderAction :icon="Plus" label="Tulis Artikel" />
    </template>

    <AdminPageCard
      title="Publikasi Artikel &amp; Informasi Pasar"
      description="Manajemen artikel program pendampingan ekonomi komunitas berbasis WYSIWYG Editor."
    />
  </AdminLayout>
</template>
