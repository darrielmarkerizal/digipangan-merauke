<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { Toaster } from "vue-sonner";
import AuthEditorialPanel from "@/Components/auth/AuthEditorialPanel.vue";
import RegisterMainPanel from "@/Components/auth/RegisterMainPanel.vue";
import { useFlashToast } from "@/Composables/useFlashToast";

defineProps<{
    regions?: any[];
    villages?: any[];
    farmerGroups?: any[];
}>();

useFlashToast();
</script>

<template>
    <Head title="Daftar Petani - DigiPangan Merauke" />
    <Toaster position="top-center" richColors />

    <div class="grid min-h-screen w-full lg:grid-cols-12 bg-bg">
        <AuthEditorialPanel />
        <RegisterMainPanel
            :regions="regions"
            :villages="villages"
            :farmer-groups="farmerGroups"
        />
    </div>
</template>
