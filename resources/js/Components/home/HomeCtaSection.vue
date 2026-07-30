<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ShoppingBasket, ArrowRight } from "@lucide/vue";
import { Icon } from "@/Components/ui";
</script>

<template>
    <section class="border-t border-border/80 bg-white py-24 lg:py-28">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-3xl border border-brand/25 bg-brand-weak/60 p-10 shadow-soft md:p-14"
            >
                <div
                    class="flex flex-col items-center justify-between gap-8 text-center md:flex-row md:text-left"
                >
                    <div class="space-y-3 max-w-xl">
                        <span
                            class="text-xs font-bold uppercase tracking-widest text-brand"
                        >
                            Koneksi Langsung Komunitas
                        </span>
                        <h2 class="text-2xl font-extrabold text-fg sm:text-3xl">
                            Tertarik dengan Komoditas Hasil Panen Merauke?
                        </h2>
                        <p
                            class="text-sm leading-relaxed text-fg-muted sm:text-base"
                        >
                            Mulai jelajahi seluruh katalog pangan lokal atau
                            pelajari metodologi pemberdayaan masyarakat oleh UGM
                            bersama Kementerian Transmigrasi RI.
                        </p>
                    </div>

                    <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                        <Link
                            href="/produk"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-brand px-7 py-3.5 text-sm font-bold text-on-brand shadow-sm transition-all hover:bg-brand-strong"
                        >
                            <Icon :icon="ShoppingBasket" :size="16" />
                            <span>Lihat Etalase</span>
                        </Link>
                        <Link
                            href="/tentang"
                            class="inline-flex items-center justify-center gap-2 rounded-full border border-border/80 bg-white px-7 py-3.5 text-sm font-bold text-fg shadow-sm transition-colors hover:bg-emerald-50/60"
                        >
                            <span>Tentang Program</span>
                            <Icon :icon="ArrowRight" :size="16" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
