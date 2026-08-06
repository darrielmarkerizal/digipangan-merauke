<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import LoginForm from "@/Components/auth/LoginForm.vue";
</script>

<template>
    <div class="mx-auto my-auto w-full max-w-sm py-8 sm:max-w-md">
        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-fg sm:text-3xl">
                Masuk ke Akun
            </h1>
            <p class="mt-2 text-sm leading-relaxed text-fg-muted">
                Kelola etalase komoditas, pantau pesanan, serta perbarui
                informasi pertanian Anda.
            </p>
        </div>

        <LoginForm />

        <p class="mt-6 text-center text-sm text-fg-muted">
            Belum punya akun petani?
            <Link href="/daftar" class="font-medium text-brand hover:text-brand-strong">
                Daftar di sini
            </Link>
        </p>
    </div>
</template>
