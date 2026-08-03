<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import {
    Users,
    ShieldCheck,
    BookOpen,
    Handshake,
    Cpu,
    ArrowRight,
} from "@lucide/vue";
import { Icon } from "@/Components/ui";

withDefaults(defineProps<{ grain?: string }>(), { grain: "" });

const pillars = [
    {
        num: "01",
        title: "Aksi Partisipasi",
        icon: Users,
        desc: "Masyarakat dilibatkan aktif sejak awal perencanaan hingga evaluasi untuk membangun kepemilikan program yang kuat.",
    },
    {
        num: "02",
        title: "Pendampingan Intensif",
        icon: ShieldCheck,
        desc: "Pembinaan berkala fokus pada koperasi tani, kelembagaan ekonomi, dan digitalisasi pascapanen.",
    },
    {
        num: "03",
        title: "Learning by Doing",
        icon: BookOpen,
        desc: "Praktik langsung pemasaran digital, pengemasan standar, dan manajemen keuangan usaha tani.",
    },
    {
        num: "04",
        title: "Kolaborasi Multi Pihak",
        icon: Handshake,
        desc: "Sinergi akademisi UGM, Kementerian Transmigrasi, pemerintah daerah, dan kelompok masyarakat.",
    },
    {
        num: "05",
        title: "Dukungan Teknologi",
        icon: Cpu,
        desc: "Pemanfaatan marketplace komunitas DigiPangan untuk menghubungkan komoditas lokal langsung ke pembeli.",
    },
];
</script>

<template>
    <section
        class="relative isolate flex flex-col justify-center overflow-hidden border-b border-emerald-900/40 bg-[#14281f] py-14 sm:py-20 lg:py-28"
    >
        <div
            v-if="grain"
            class="pointer-events-none absolute inset-0 z-0 opacity-[0.18] mix-blend-overlay"
            :style="{ backgroundImage: grain }"
            aria-hidden="true"
        />
        <div class="relative z-10 mx-auto max-w-[90rem] px-3 sm:px-5 lg:px-6">
            <div
                class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-end lg:gap-12"
            >
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-3">
                        <span class="h-px w-8 bg-emerald-400/60" />
                        <span
                            class="text-xs font-bold uppercase tracking-widest text-emerald-300"
                        >
                            Pendekatan Terintegrasi &amp; Berkelanjutan
                        </span>
                    </div>

                    <h2
                        class="mt-4 font-serif text-[1.75rem] font-semibold leading-[1.12] tracking-tight text-white sm:text-4xl lg:text-[2.75rem] lg:leading-[1.08]"
                    >
                        <span class="text-emerald-300">5 Pilar</span> Metodologi
                        Program Pendampingan
                    </h2>
                </div>

                <div class="lg:col-span-5">
                    <p
                        class="text-sm leading-relaxed text-emerald-100/70 sm:text-base"
                    >
                        Metodologi komprehensif yang dirancang oleh Tim UGM
                        bersama Kementerian Transmigrasi RI untuk membangun
                        ekosistem ekonomi lokal yang produktif dan berdaya
                        saing.
                    </p>

                    <Link
                        href="/tentang"
                        class="mt-5 inline-flex items-center gap-2.5 rounded-full border border-emerald-400/30 bg-emerald-500/10 px-5 py-3 text-sm font-bold text-emerald-200 transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-400/60 hover:bg-emerald-500/20 hover:text-white active:scale-[0.98]"
                    >
                        <span>Baca Dokumen Laporan Lengkap</span>
                        <Icon :icon="ArrowRight" :size="16" />
                    </Link>
                </div>
            </div>

            <ol
                class="mt-12 grid grid-cols-1 gap-x-8 gap-y-10 sm:mt-16 sm:grid-cols-2 lg:grid-cols-6 xl:grid-cols-5"
            >
                <li
                    v-for="(pilar, i) in pillars"
                    :key="pilar.num"
                    class="group relative border-t border-white/12 pt-6 sm:pt-7 xl:col-span-1"
                    :class="i < 3 ? 'lg:col-span-2' : 'lg:col-span-3'"
                >
                    <span
                        class="pointer-events-none absolute -top-px left-0 h-px w-0 bg-emerald-400 transition-all duration-500 ease-premium group-hover:w-full"
                        aria-hidden="true"
                    />

                    <div class="flex items-start justify-between gap-3">
                        <span
                            class="font-serif text-4xl font-semibold leading-none text-emerald-300/35 transition-colors duration-300 group-hover:text-emerald-300/80 sm:text-5xl"
                        >
                            {{ pilar.num }}
                        </span>
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-300 ring-1 ring-inset ring-emerald-400/20 transition-colors duration-300 group-hover:bg-emerald-500/20 group-hover:text-emerald-200"
                        >
                            <Icon :icon="pilar.icon" :size="19" />
                        </span>
                    </div>

                    <h3
                        class="mt-5 text-base font-bold leading-snug text-white"
                    >
                        {{ pilar.title }}
                    </h3>
                    <p
                        class="mt-2 text-[13px] leading-relaxed text-emerald-100/65 sm:text-sm"
                    >
                        {{ pilar.desc }}
                    </p>
                </li>
            </ol>
        </div>
    </section>
</template>
