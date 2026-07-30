<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { Sprout, MapPin } from "@lucide/vue";
import { Icon } from "@/Components/ui";
</script>

<template>
    <aside
        class="relative isolate hidden flex-col justify-between overflow-hidden bg-slate-900 p-10 text-white lg:flex lg:col-span-6 xl:p-14"
    >
        <img
            src="/images/logos/login-image.webp"
            alt="DigiPangan Merauke"
            class="absolute inset-0 -z-20 size-full object-cover"
        />
        <div
            class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-black/50 via-transparent to-black/40"
            aria-hidden="true"
        />

        <div class="flex items-center">
            <Link href="/" class="flex items-center gap-3">
                <span
                    class="flex size-10 items-center justify-center rounded-xl bg-brand text-white shadow-sm"
                >
                    <Icon :icon="Sprout" :size="20" />
                </span>
                <span
                    class="text-xl font-bold tracking-tight text-white drop-shadow-md"
                >
                    DigiPangan<span class="font-medium text-emerald-200">
                        Merauke</span
                    >
                </span>
            </Link>
        </div>

        <div
            class="self-end rounded-2xl bg-white p-6 shadow-xl transition-all hover:shadow-2xl max-w-max"
        >
            <span
                class="text-xs font-bold uppercase tracking-wider text-emerald-700"
            >
                Kolaborasi Strategis
            </span>

            <div class="my-4 flex items-center gap-6">
                <img
                    src="/images/logos/logo-ugm-horizontal.png"
                    alt="Universitas Gadjah Mada"
                    class="h-9 xl:h-10 w-auto object-contain shrink-0"
                />
                <div class="h-10 w-px bg-slate-200" />
                <img
                    src="/images/logos/logotext-kementerian-transmigrasi.png"
                    alt="Kementerian Transmigrasi RI"
                    class="h-14 xl:h-16 w-auto object-contain shrink-0"
                />
            </div>

            <div
                class="flex items-center gap-1.5 text-xs font-medium text-slate-600"
            >
                <Icon :icon="MapPin" :size="15" class="text-emerald-700" />
                <span>Kawasan Transmigrasi Merauke, Papua Selatan</span>
            </div>
        </div>
    </aside>
</template>
