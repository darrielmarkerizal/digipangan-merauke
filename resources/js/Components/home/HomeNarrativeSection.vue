<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import {
    ArrowRight,
    Landmark,
    GraduationCap,
    Smartphone,
    Package,
} from "@lucide/vue";
import { Icon } from "@/Components/ui";

const solutions = [
    {
        icon: Landmark,
        title: "Penguatan Koperasi Tani",
        desc: "Memperkuat kelembagaan dan tata kelola koperasi sebagai pusat ekonomi masyarakat di kawasan transmigrasi Merauke.",
    },
    {
        icon: GraduationCap,
        title: "Peningkatan Kapasitas SDM",
        desc: "Meningkatkan kemampuan kewirausahaan, manajemen usaha tani, dan literasi keuangan masyarakat secara terukur.",
    },
    {
        icon: Smartphone,
        title: "Digitalisasi Pemasaran",
        desc: "Memanfaatkan platform marketplace komunitas untuk pemasaran langsung, informasi harga pasar, dan promosi produk desa.",
    },
    {
        icon: Package,
        title: "Pascapanen & Nilai Tambah",
        desc: "Meningkatkan teknologi pascapanen, pengemasan standar, penyimpanan, dan diversifikasi produk agar bernilai jual tinggi.",
    },
];
</script>

<template>
    <section
        class="mx-auto flex w-full max-w-[90rem] items-center px-3 py-20 sm:px-5 sm:py-24 lg:px-6"
    >
        <div
            class="grid w-full grid-cols-1 items-start gap-8 sm:gap-10 lg:grid-cols-12 lg:gap-12"
        >
            <div class="space-y-6 lg:col-span-5 lg:sticky lg:top-24">
                <h2
                    class="text-3xl font-extrabold leading-tight tracking-tight text-fg sm:text-4xl"
                >
                    Menutup Kesenjangan Potensi Alam Merauke
                    <span class="text-brand">dengan Digitalisasi</span>
                </h2>

                <p class="text-sm leading-relaxed text-fg-muted sm:text-base">
                    Distrik Ulilin, Muting, dan Elikobel memiliki kekayaan
                    komoditas berlimpah mulai dari padi, jagung, rambutan,
                    hingga hortikultura. Namun tantangan pascapanen dan rantai
                    pasok panjang sering menekan margin petani.
                </p>
                <p class="text-sm leading-relaxed text-fg-muted sm:text-base">
                    <strong class="font-semibold text-brand"
                        >DigiPangan Merauke</strong
                    >
                    hadir sebagai solusi nyata perpanjangan tangan program
                    Pengabdian kepada Masyarakat UGM bersama Kementerian
                    Transmigrasi RI untuk membangun kedaulatan pangan nasional
                    dari Papua Selatan.
                </p>

                <div>
                    <Link
                        href="/tentang"
                        class="inline-flex items-center gap-2.5 rounded-full bg-brand px-6 py-3 text-sm font-bold text-on-brand shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-strong hover:shadow-md active:scale-[0.98]"
                    >
                        <span>Pelajari Metodologi Lengkap Tim UGM</span>
                        <Icon :icon="ArrowRight" :size="16" />
                    </Link>
                </div>
            </div>

            <div class="space-y-3 sm:space-y-4 lg:col-span-7">
                <div
                    v-for="s in solutions"
                    :key="s.title"
                    class="flex items-center gap-4 rounded-2xl border border-border/70 bg-white p-4 shadow-xs sm:gap-5 sm:p-6"
                >
                    <span
                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-brand-weak text-brand sm:size-16"
                    >
                        <Icon :icon="s.icon" :size="26" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-lg font-bold text-fg sm:text-xl">
                            {{ s.title }}
                        </h3>
                        <p class="mt-1 text-sm leading-relaxed text-fg-muted">
                            {{ s.desc }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
