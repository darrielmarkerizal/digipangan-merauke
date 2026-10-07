<script setup lang="ts">
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ArrowLeft, Eye, EyeOff, KeyRound, ShieldCheck } from "@lucide/vue";
import AuthEditorialPanel from "@/Components/auth/AuthEditorialPanel.vue";
import AuthFooter from "@/Components/auth/AuthFooter.vue";
import AuthTopBar from "@/Components/auth/AuthTopBar.vue";
import { Button, Field, Icon, Input } from "@/Components/ui";

const props = defineProps<{ token: string; email: string }>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const showPassword = ref(false);
const showConfirmation = ref(false);

const submit = () => {
    form.post("/reset-password", {
        onSuccess: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Buat Kata Sandi Baru - DigiPangan Merauke" />

    <div class="grid min-h-screen w-full bg-bg lg:grid-cols-12">
        <AuthEditorialPanel />

        <main
            class="flex min-h-screen flex-col justify-between bg-white px-6 py-8 sm:px-12 sm:py-10 lg:col-span-6 lg:bg-bg/40 lg:px-16 xl:px-24"
        >
            <AuthTopBar />

            <section class="mx-auto my-auto w-full max-w-md py-10">
                <Link
                    href="/login"
                    class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-brand"
                >
                    <Icon :icon="ArrowLeft" :size="17" />
                    Kembali ke halaman masuk
                </Link>

                <div
                    class="mb-6 flex size-12 items-center justify-center rounded-2xl bg-brand-weak text-brand"
                    aria-hidden="true"
                >
                    <Icon :icon="KeyRound" :size="22" />
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-fg sm:text-3xl">
                    Buat kata sandi baru
                </h1>
                <p class="mt-3 text-sm leading-relaxed text-fg-muted">
                    Gunakan kata sandi minimal 8 karakter, lalu konfirmasikan
                    kembali agar tidak salah ketik.
                </p>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <Field
                        label="Alamat email"
                        :error="form.errors.email"
                        required
                    >
                        <Input
                            v-model="form.email"
                            type="email"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            inputmode="email"
                            maxlength="150"
                            :disabled="form.processing"
                        />
                    </Field>

                    <Field
                        label="Kata sandi baru"
                        :error="form.errors.password"
                        helper="Minimal 8 karakter."
                        required
                    >
                        <div class="relative">
                            <Input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="255"
                                class="pr-11"
                                :disabled="form.processing"
                            />
                            <button
                                type="button"
                                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-fg-muted transition-colors hover:text-fg"
                                @click="showPassword = !showPassword"
                            >
                                <Icon
                                    :icon="showPassword ? EyeOff : Eye"
                                    :size="18"
                                />
                            </button>
                        </div>
                    </Field>

                    <Field
                        label="Konfirmasi kata sandi"
                        :error="form.errors.password_confirmation"
                        required
                    >
                        <div class="relative">
                            <Input
                                v-model="form.password_confirmation"
                                :type="showConfirmation ? 'text' : 'password'"
                                placeholder="Ulangi kata sandi baru"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="255"
                                class="pr-11"
                                :disabled="form.processing"
                            />
                            <button
                                type="button"
                                :aria-label="showConfirmation ? 'Sembunyikan konfirmasi kata sandi' : 'Tampilkan konfirmasi kata sandi'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-fg-muted transition-colors hover:text-fg"
                                @click="showConfirmation = !showConfirmation"
                            >
                                <Icon
                                    :icon="showConfirmation ? EyeOff : Eye"
                                    :size="18"
                                />
                            </button>
                        </div>
                    </Field>

                    <Button
                        type="submit"
                        fullWidth
                        size="lg"
                        :loading="form.processing"
                    >
                        <Icon v-if="!form.processing" :icon="KeyRound" :size="18" />
                        <span>{{
                            form.processing
                                ? "Menyimpan kata sandi..."
                                : "Simpan kata sandi baru"
                        }}</span>
                    </Button>
                </form>

                <div
                    class="mt-6 flex items-start gap-2.5 rounded-xl bg-muted/60 p-4 text-xs leading-relaxed text-fg-muted"
                >
                    <Icon
                        :icon="ShieldCheck"
                        :size="17"
                        class="mt-0.5 shrink-0 text-brand"
                    />
                    <p>
                        Tautan hanya berlaku satu kali dan akan kedaluwarsa.
                        Jika tautan tidak dapat digunakan, minta tautan baru
                        dari halaman lupa kata sandi.
                    </p>
                </div>
            </section>

            <AuthFooter />
        </main>
    </div>
</template>
