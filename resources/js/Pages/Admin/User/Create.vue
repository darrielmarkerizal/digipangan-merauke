<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import UserForm from "@/Components/admin/User/UserForm.vue";

const props = defineProps<{
    roles: string[];
    regions?: Array<{ id: number; name: string }>;
    villages?: Array<{ id: number; name: string; region_id: number }>;
    farmerGroups?: Array<{ id: number; name: string; region_id: number }>;
}>();
</script>

<template>
    <AdminLayout
        title="Tambah Pengguna"
        subtitle="Buat akun pengguna baru dengan peran dan hak akses yang sesuai."
    >
        <UserForm
            :roles="roles"
            :regions="regions"
            :villages="villages"
            :farmer-groups="farmerGroups"
        />
    </AdminLayout>
</template>
