<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { Toaster } from "vue-sonner";
import AuthEditorialPanel from "@/Components/auth/AuthEditorialPanel.vue";
import AuthMainPanel from "@/Components/auth/AuthMainPanel.vue";
import { useFlashToast } from "@/Composables/useFlashToast";

useFlashToast();
</script>

<template>
    <Head title="Masuk - DigiPangan Merauke" />
    <Toaster position="top-center" richColors />

    <div class="grid min-h-screen w-full lg:grid-cols-12 bg-bg">
        <AuthEditorialPanel />
        <AuthMainPanel />
    </div>
</template>


