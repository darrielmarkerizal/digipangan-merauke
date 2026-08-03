<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ShoppingBasket, ArrowRight } from "@lucide/vue";
import { Icon } from "@/Components/ui";
</script>

<template>
    <section
        class="mx-auto flex w-full max-w-[90rem] flex-col justify-center border-b border-border/80 px-3 py-20 sm:px-5 sm:py-24 lg:px-6"
    >
        <div
            class="relative overflow-hidden rounded-3xl border border-brand/25 bg-brand-weak/60 p-6 sm:p-10 md:p-14 shadow-soft"
        >
            <div
                class="flex flex-col items-center justify-between gap-6 sm:gap-8 text-center md:flex-row md:text-left"
            >
                    <div class="space-y-2 sm:space-y-3 max-w-xl">
                        <span
                            class="text-xs font-bold uppercase tracking-widest text-brand"
                        >
                            Koneksi Langsung Komunitas
                        </span>
                        <h2 class="text-xl font-extrabold text-fg sm:text-2xl md:text-3xl">
                            Tertarik dengan Komoditas Hasil Panen Merauke?
                        </h2>
                        <p
                            class="text-xs leading-relaxed text-fg-muted sm:text-sm md:text-base"
                        >
                            Mulai jelajahi seluruh katalog pangan lokal atau
                            pelajari metodologi pemberdayaan masyarakat oleh UGM
                            bersama Kementerian Transmigrasi RI.
                        </p>
                    </div>

                    <div class="flex shrink-0 flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <Link
                            href="/produk"
                            class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full bg-brand px-6 py-3.5 text-sm font-bold text-on-brand shadow-sm transition-all hover:bg-brand-strong"
                        >
                            <Icon :icon="ShoppingBasket" :size="16" />
                            <span>Lihat Etalase</span>
                        </Link>
                        <Link
                            href="/tentang"
                            class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full border border-border/80 bg-white px-6 py-3.5 text-sm font-bold text-fg shadow-sm transition-colors hover:bg-emerald-50/60"
                        >
                            <span>Tentang Program</span>
                            <Icon :icon="ArrowRight" :size="16" />
                        </Link>
                    </div>
                </div>
        </div>
    </section>
</template>
