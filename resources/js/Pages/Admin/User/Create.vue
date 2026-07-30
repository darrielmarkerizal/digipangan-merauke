<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import UserForm from "@/Components/admin/User/UserForm.vue";

const props = defineProps<{
    roles: string[];
}>();
</script>

<template>
    <AdminLayout
        title="Tambah Pengguna"
        subtitle="Buat akun admin baru dengan peran dan hak akses yang sesuai."
    >
        <UserForm :roles="roles" />
    </AdminLayout>
</template>
