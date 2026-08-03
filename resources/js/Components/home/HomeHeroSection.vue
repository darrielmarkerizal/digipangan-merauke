<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { Sprout, Lock, MapPin, Truck, ShieldCheck, Users } from "@lucide/vue";
import { Icon } from "@/Components/ui";
import type { ProductCard } from "@/types/home";

defineProps<{
    heroProduct: ProductCard | null;
    heroImage: string | null;
    heroIsFeatured: boolean;
    grain: string;
}>();

const features = [
    {
        icon: Sprout,
        title: "Dari Petani",
        desc: "Produk langsung dari petani dan kelompok tani.",
    },
    {
        icon: Truck,
        title: "Rantai Pasok Pendek",
        desc: "Lebih efisien, transparan, dan menekan biaya.",
    },
    {
        icon: ShieldCheck,
        title: "Aman & Terpercaya",
        desc: "Transaksi aman, kualitas terjamin, saling menguntungkan.",
    },
    {
        icon: Users,
        title: "Untuk Komunitas",
        desc: "Keuntungan kembali ke masyarakat, ekonomi lokal makin kuat.",
    },
];
</script>

<template>
    <section
        class="relative isolate -mt-16 flex min-h-[100dvh] flex-col justify-between pt-16 md:-mt-20 md:pt-20"
    >
        <div
            class="pointer-events-none absolute inset-0 -z-10 opacity-[0.05] mix-blend-multiply"
            :style="{ backgroundImage: grain }"
            aria-hidden="true"
        />

        <div
            class="pointer-events-none absolute inset-y-0 right-0 hidden w-[48%] lg:block"
            aria-hidden="true"
        >
            <img
                :src="heroImage || '/images/hero-image.webp'"
                :alt="
                    heroProduct
                        ? `${heroProduct.name} dari Merauke`
                        : 'Komoditas lokal Merauke'
                "
                class="size-full object-cover"
            />
            <div
                class="absolute inset-0 bg-gradient-to-r from-bg via-bg/20 to-transparent"
            />
            <div
                class="absolute inset-x-0 bottom-0 h-44 bg-gradient-to-t from-bg via-bg/60 to-transparent"
            />
        </div>

        <div class="flex w-full flex-1 items-center py-4 sm:py-6">
            <div
                class="mx-auto grid w-full max-w-[90rem] items-center gap-6 px-3 pb-4 pt-2 sm:gap-8 sm:px-5 sm:pt-4 lg:grid-cols-12 lg:gap-8 lg:px-6 lg:pb-4 lg:pt-4"
            >
                <div class="space-y-5 sm:space-y-6 lg:col-span-7 lg:pr-8">
                    <h1
                        class="font-serif text-[2rem] font-semibold leading-[1.12] tracking-tight text-fg sm:text-[2.75rem] sm:leading-[1.1] lg:text-[3.1rem] lg:leading-[1.08]"
                    >
                        Menguatkan Kelembagaan &amp; Digitalisasi Rantai Pasok
                        <span class="text-brand">Komoditas Lokal Merauke</span>
                    </h1>

                    <p
                        class="max-w-xl text-sm leading-relaxed text-fg-muted sm:text-base"
                    >
                        DigiPangan Merauke adalah marketplace komunitas untuk
                        petani dan koperasi di kawasan transmigrasi
                        <strong class="font-semibold text-fg"
                            >Muting, Ulilin, dan Elikobel</strong
                        >. Bersama membangun ekonomi lokal yang produktif dan
                        berkelanjutan.
                    </p>

                    <div
                        class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:gap-4"
                    >
                        <Link
                            href="/produk"
                            class="inline-flex items-center justify-center gap-2.5 rounded-full bg-brand px-7 py-3.5 text-sm font-bold text-on-brand shadow-soft transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-strong hover:shadow-md active:scale-[0.98] sm:text-base"
                        >
                            <Icon :icon="Lock" :size="17" />
                            <span>Jelajahi Produk</span>
                        </Link>

                        <Link
                            href="/wilayah"
                            class="inline-flex items-center justify-center gap-2.5 rounded-full border border-border/80 bg-white px-7 py-3.5 text-sm font-bold text-fg shadow-sm transition-all duration-200 hover:border-brand/40 hover:bg-card active:scale-[0.98] sm:text-base"
                        >
                            <Icon
                                :icon="MapPin"
                                :size="17"
                                class="text-fg-muted"
                            />
                            <span>Lihat Wilayah</span>
                        </Link>
                    </div>

                    <div class="pt-2">
                        <div class="mb-3 flex items-center gap-4">
                            <p
                                class="shrink-0 text-[11px] font-bold uppercase tracking-wider text-fg-muted/80 sm:text-xs"
                            >
                                Mitra Strategis Pendamping
                            </p>
                            <span class="h-px flex-1 bg-border/80" />
                        </div>
                        <div
                            class="flex max-w-full flex-col items-start gap-5 lg:flex-row lg:items-center lg:gap-6"
                        >
                            <img
                                src="/images/logos/logo-ugm-horizontal.png"
                                alt="Universitas Gadjah Mada"
                                class="h-12 w-auto max-w-full object-contain object-left sm:h-16 lg:h-11"
                            />
                            <div
                                class="hidden h-10 w-px shrink-0 bg-border lg:block"
                            />
                            <img
                                src="/images/logos/logotext-kementerian-transmigrasi.png"
                                alt="Kementerian Transmigrasi RI"
                                class="h-14 w-auto max-w-full object-contain object-left sm:h-16"
                            />
                        </div>
                    </div>
                </div>

                <div
                    class="relative order-first lg:order-last lg:col-span-5 lg:min-h-[26rem] lg:self-stretch lg:mx-0 lg:mt-0"
                >
                    <div
                        class="relative w-full h-[18rem] sm:h-[22rem] overflow-hidden rounded-3xl shadow-soft lg:hidden lg:rounded-none lg:shadow-none"
                    >
                        <img
                            :src="heroImage || '/images/hero-image.webp'"
                            :alt="
                                heroProduct
                                    ? `${heroProduct.name} dari Merauke`
                                    : 'Komoditas lokal Merauke'
                            "
                            class="size-full object-cover object-[center_40%]"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div
            class="relative z-10 mx-auto w-full max-w-[90rem] px-3 pb-6 sm:px-5 sm:pb-8 lg:px-6 lg:pb-10"
        >
            <div
                class="grid grid-cols-1 gap-6 rounded-[2rem] border border-emerald-400/20 bg-[#14281f] p-6 sm:grid-cols-2 sm:p-8 lg:grid-cols-4 lg:gap-6 lg:p-8 shadow-[0_25px_60px_-15px_rgba(20,40,31,0.35)]"
            >
                <div
                    v-for="(f, i) in features"
                    :key="i"
                    class="group flex items-start gap-4 transition-all duration-300 hover:translate-x-0.5"
                >
                    <span
                        class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shadow-inner group-hover:bg-emerald-500/30 group-hover:text-emerald-200 transition-colors"
                    >
                        <Icon :icon="f.icon" :size="26" />
                    </span>
                    <div class="min-w-0">
                        <p
                            class="text-base sm:text-lg font-bold text-white group-hover:text-emerald-300 transition-colors"
                        >
                            {{ f.title }}
                        </p>
                        <p
                            class="mt-1.5 text-xs sm:text-sm leading-relaxed text-emerald-100/80"
                        >
                            {{ f.desc }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
