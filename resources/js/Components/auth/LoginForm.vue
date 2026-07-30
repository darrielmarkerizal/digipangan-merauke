<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { LogIn, Eye, EyeOff } from "@lucide/vue";
import { Button, Field, Input, Icon } from "@/Components/ui";
import { toast } from "vue-sonner";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post("/login", {
        onSuccess: () => {
            toast.success("Berhasil masuk", {
                description:
                    "Selamat datang di Portal Admin DigiPangan Merauke.",
            });
        },
        onError: () => {
            toast.error("Gagal masuk", {
                description: "Periksa kembali email dan kata sandi Anda.",
            });
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-5">
        <Field label="Alamat Email" :error="form.errors.email" required>
            <Input
                v-model="form.email"
                type="email"
                placeholder="nama@mitra-digipangan.id"
                autocomplete="username"
                :disabled="form.processing"
            />
        </Field>

        <Field label="Kata Sandi" :error="form.errors.password" required>
            <div class="relative">
                <Input
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="••••••••••••"
                    autocomplete="current-password"
                    :disabled="form.processing"
                    class="pr-11"
                />
                <button
                    type="button"
                    aria-label="Tampilkan kata sandi"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-fg-muted transition-colors hover:text-fg"
                    @click="showPassword = !showPassword"
                >
                    <Icon :icon="showPassword ? EyeOff : Eye" :size="18" />
                </button>
            </div>
        </Field>

        <div class="flex items-center justify-between">
            <label
                class="group flex cursor-pointer select-none items-center gap-2.5"
            >
                <input
                    type="checkbox"
                    v-model="form.remember"
                    class="size-4 rounded border-border text-brand shadow-sm transition-colors focus:border-brand focus:ring-brand accent-brand cursor-pointer"
                />
                <span
                    class="text-sm text-fg-muted transition-colors group-hover:text-fg"
                >
                    Simpan sesi di perangkat ini
                </span>
            </label>

            <a
                href="#"
                class="text-sm font-medium text-brand transition-colors hover:text-brand-strong"
            >
                Lupa kata sandi?
            </a>
        </div>

        <Button
            type="submit"
            fullWidth
            :loading="form.processing"
            class="mt-3 shadow-sm"
        >
            <Icon v-if="!form.processing" :icon="LogIn" :size="18" />
            <span>{{
                form.processing
                    ? "Memverifikasi kredensial..."
                    : "Masuk ke Portal"
            }}</span>
        </Button>
    </form>
</template>
