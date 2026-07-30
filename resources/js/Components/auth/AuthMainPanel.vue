<script setup lang="ts">
import AuthTopBar from "@/Components/auth/AuthTopBar.vue";
import AuthFormSection from "@/Components/auth/AuthFormSection.vue";
import AuthFooter from "@/Components/auth/AuthFooter.vue";
</script>

<template>
    <main
        class="flex min-h-screen flex-col justify-between bg-white px-6 py-8 sm:px-12 sm:py-10 lg:col-span-6 lg:bg-bg/40 lg:px-16 xl:px-24"
    >
        <AuthTopBar />
        <AuthFormSection />
        <AuthFooter />
    </main>
</template>
