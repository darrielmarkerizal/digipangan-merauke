<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ArrowRight } from "@lucide/vue";
import { Icon } from "@/Components/ui";
import ProductCard from "@/Components/product/ProductCard.vue";
import type { ProductCard as ProductCardType } from "@/types/home";

defineProps<{
    featuredList: ProductCardType[];
    useSpotlight: boolean;
    spotlight: ProductCardType | null;
    restFeatured: ProductCardType[];
    categoriesPreview: string[];
}>();
</script>

<template>
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div
            class="flex flex-col justify-between gap-6 md:flex-row md:items-end"
        >
            <div class="space-y-3">
                <span
                    class="text-xs font-bold uppercase tracking-widest text-brand"
                >
                    Kualitas Terverifikasi Tim Pendamping
                </span>
                <h2
                    class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl"
                >
                    Komoditas Unggulan Kawasan
                </h2>
                <p class="text-base text-fg-muted">
                    Pilihan hasil panen terbaik langsung dari lahan transmigran
                    di Kabupaten Merauke.
                </p>
            </div>

            <Link
                href="/produk"
                class="inline-flex items-center gap-2 rounded-full border border-border/80 bg-white px-6 py-3 text-sm font-bold text-fg shadow-sm transition-all hover:border-brand/40 hover:bg-card"
            >
                <span>Lihat Semua Produk</span>
                <Icon :icon="ArrowRight" :size="16" />
            </Link>
        </div>

        <div class="mt-8 flex flex-wrap gap-2.5">
            <Link
                v-for="cat in categoriesPreview"
                :key="cat"
                :href="`/produk?kategori=${encodeURIComponent(cat)}`"
                class="rounded-full border border-border/80 bg-white px-5 py-2 text-xs font-bold text-fg shadow-xs transition-colors hover:border-brand hover:text-brand"
            >
                {{ cat }}
            </Link>
        </div>

        <div
            v-if="useSpotlight && spotlight"
            class="mt-12 grid grid-cols-1 gap-8 lg:grid-cols-12"
        >
            <div class="lg:col-span-6">
                <ProductCard :product="spotlight" :featured="true" />
            </div>
            <div
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:col-span-6 lg:grid-cols-2"
            >
                <ProductCard
                    v-for="prod in restFeatured"
                    :key="prod.slug"
                    :product="prod"
                />
            </div>
        </div>

        <div
            v-else
            class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <ProductCard
                v-for="prod in featuredList"
                :key="prod.slug"
                :product="prod"
            />
        </div>
    </section>
</template>
