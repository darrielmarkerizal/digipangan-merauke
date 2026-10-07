<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ArrowLeft, CheckCircle2, Mail, ShieldCheck } from "@lucide/vue";
import AuthEditorialPanel from "@/Components/auth/AuthEditorialPanel.vue";
import AuthFooter from "@/Components/auth/AuthFooter.vue";
import AuthTopBar from "@/Components/auth/AuthTopBar.vue";
import { Button, Field, Icon, Input } from "@/Components/ui";

defineProps<{ status?: string | null }>();

const form = useForm({ email: "" });

const submit = () => {
    form.post("/lupa-kata-sandi", {
        onSuccess: () => form.reset("email"),
    });
};
</script>

<template>
    <Head title="Lupa Kata Sandi - DigiPangan Merauke" />

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
                    <Icon :icon="Mail" :size="22" />
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-fg sm:text-3xl">
                    Lupa kata sandi?
                </h1>
                <p class="mt-3 text-sm leading-relaxed text-fg-muted">
                    Masukkan alamat email akun Anda. Jika terdaftar, kami akan
                    mengirim tautan untuk membuat kata sandi baru.
                </p>

                <div
                    v-if="status"
                    role="status"
                    aria-live="polite"
                    class="mt-6 flex gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm leading-relaxed text-emerald-900"
                >
                    <Icon
                        :icon="CheckCircle2"
                        :size="19"
                        class="mt-0.5 shrink-0 text-emerald-700"
                    />
                    <span>{{ status }}</span>
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <Field
                        label="Alamat email"
                        :error="form.errors.email"
                        helper="Pastikan Anda dapat membuka kotak masuk email ini."
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

                    <Button
                        type="submit"
                        fullWidth
                        size="lg"
                        :loading="form.processing"
                    >
                        <Icon v-if="!form.processing" :icon="Mail" :size="18" />
                        <span>{{
                            form.processing
                                ? "Mengirim tautan..."
                                : "Kirim tautan reset"
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
                        Demi keamanan, halaman ini tidak memberi tahu apakah
                        alamat email terdaftar. Periksa juga folder spam.
                    </p>
                </div>
            </section>

            <AuthFooter />
        </main>
    </div>
</template>
