<script setup lang="ts">
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FaqForm from "@/Components/admin/Faq/FaqForm.vue";

const props = defineProps<{
    faq: {
        id: number;
        question: string;
        answer: string;
        sort_order: number;
        is_active: boolean;
    };
}>();
</script>

<template>
    <AdminLayout
        title="Edit FAQ"
        subtitle="Perbarui pertanyaan & jawaban yang sudah ada."
    >
        <FaqForm :faq="faq" :is-edit="true" />
    </AdminLayout>
</template>
