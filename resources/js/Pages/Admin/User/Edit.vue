<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import UserForm from "@/Components/admin/User/UserForm.vue";

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        is_active: boolean;
        region_id?: number | null;
        roles: string[];
    };
    roles: string[];
    regions?: Array<{ id: number; name: string }>;
}>();
</script>

<template>
    <AdminLayout
        title="Edit Pengguna"
        subtitle="Perbarui informasi, peran, dan status akun pengguna."
    >
        <UserForm :user="user" :roles="roles" :regions="regions" :is-edit="true" />
    </AdminLayout>
</template>
