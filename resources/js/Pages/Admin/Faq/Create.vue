<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FaqForm from "@/Components/admin/Faq/FaqForm.vue";
</script>

<template>
    <AdminLayout
        title="Tambah FAQ"
        subtitle="Buat pertanyaan baru yang akan tampil di halaman Pusat Bantuan."
    >
        <FaqForm />
    </AdminLayout>
</template>
