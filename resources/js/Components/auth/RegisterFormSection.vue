<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import RegisterForm from "@/Components/auth/RegisterForm.vue";

defineProps<{
    regions?: any[];
    villages?: any[];
    farmerGroups?: any[];
}>();
</script>

<template>
    <div class="mx-auto my-auto w-full max-w-sm py-8 sm:max-w-lg">
        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-fg sm:text-3xl">
                Daftar sebagai Petani
            </h1>
            <p class="mt-2 text-sm leading-relaxed text-fg-muted">
                Lengkapi profil Anda untuk mulai memasarkan hasil panen di
                DigiPangan Merauke.
            </p>
        </div>

        <RegisterForm
            :regions="regions"
            :villages="villages"
            :farmer-groups="farmerGroups"
        />

        <p class="mt-6 text-center text-sm text-fg-muted">
            Sudah punya akun?
            <Link href="/login" class="font-medium text-brand hover:text-brand-strong">
                Masuk di sini
            </Link>
        </p>
    </div>
</template>
