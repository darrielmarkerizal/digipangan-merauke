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
    villages?: Array<{ id: number; name: string; region_id: number }>;
    farmerGroups?: Array<{ id: number; name: string; region_id: number }>;
}>();
</script>

<template>
    <AdminLayout
        title="Edit Pengguna"
        subtitle="Perbarui informasi, peran, dan status akun pengguna."
    >
        <UserForm
            :user="user"
            :roles="roles"
            :regions="regions"
            :villages="villages"
            :farmer-groups="farmerGroups"
            :is-edit="true"
        />
    </AdminLayout>
</template>
