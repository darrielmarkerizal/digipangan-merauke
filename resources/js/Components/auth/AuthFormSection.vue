<script setup lang="ts">
import LoginForm from '@/Components/auth/LoginForm.vue'
</script>

<template>
  <div class="mx-auto my-auto w-full max-w-sm py-8 sm:max-w-md">
    <div class="mb-8">
      <span
        class="inline-flex items-center gap-1.5 rounded-full bg-brand-weak px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand"
      >
        Akses Portal Mitra
      </span>
      <h1 class="mt-3 text-2xl font-bold tracking-tight text-fg sm:text-3xl">
        Masuk ke Akun
      </h1>
      <p class="mt-2 text-sm leading-relaxed text-fg-muted">
        Kelola etalase komoditas, pantau pesanan, serta perbarui informasi pertanian Anda.
      </p>
    </div>

    <LoginForm />
  </div>
</template>
