<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ArrowRight } from "@lucide/vue";
import { Icon, EmptyState } from "@/Components/ui";
import ProductCard from "@/Components/product/ProductCard.vue";
import type { ProductCard as ProductCardType, TaxonomyRef } from "@/types/home";

defineProps<{
    featuredList: ProductCardType[];
    categoriesPreview: TaxonomyRef[];
}>();
const getBentoClasses = (index: number, total: number) => {
    if (total === 1) return 'sm:col-span-2 lg:col-span-4';
    if (total === 2) return 'sm:col-span-1 lg:col-span-2';
    if (total === 3) {
        if (index === 0) return 'sm:col-span-2 lg:col-span-2 lg:row-span-2';
        return 'sm:col-span-2 lg:col-span-2 lg:row-span-1';
    }
    // 4 or more
    if (index === 0) return 'sm:col-span-2 lg:col-span-2 lg:row-span-2';
    if (index === 1) return 'sm:col-span-2 lg:col-span-2 lg:row-span-1';
    return 'sm:col-span-1 lg:col-span-1 lg:row-span-1';
};

const getBentoVariant = (index: number, total: number) => {
    if (total === 1 || total === 2) return 'default';
    if (total === 3) {
        if (index === 0) return 'spotlight';
        return 'horizontal';
    }
    // 4 or more
    if (index === 0) return 'spotlight';
    if (index === 1) return 'horizontal';
    return 'default';
};
</script>

<template>
    <section
        class="mx-auto flex min-h-[100dvh] w-full max-w-[90rem] flex-col justify-center px-3 py-20 sm:px-5 sm:py-24 lg:px-6"
    >
        <div
            class="flex flex-col justify-between gap-4 sm:gap-6 md:flex-row md:items-end"
        >
            <div class="space-y-2 sm:space-y-3">
                <span
                    class="text-xs font-bold uppercase tracking-widest text-brand"
                >
                    Kualitas Terverifikasi Tim Pendamping
                </span>
                <h2
                    class="text-2xl font-extrabold tracking-tight text-fg sm:text-3xl lg:text-4xl"
                >
                    Komoditas Unggulan Kawasan
                </h2>
                <p class="text-sm text-fg-muted sm:text-base">
                    Pilihan hasil panen terbaik langsung dari lahan transmigran
                    di Kabupaten Merauke.
                </p>
            </div>

            <Link
                href="/produk"
                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full border border-border/80 bg-white px-6 py-3 text-sm font-bold text-fg shadow-sm transition-all hover:border-brand/40 hover:bg-card"
            >
                <span>Lihat Semua Produk</span>
                <Icon :icon="ArrowRight" :size="16" />
            </Link>
        </div>

        <div
            v-if="categoriesPreview.length > 0"
            class="-mx-4 flex flex-nowrap overflow-x-auto px-4 pb-2 pt-6 sm:mx-0 sm:flex-wrap sm:px-0 sm:pt-8 gap-2 sm:gap-2.5 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
        >
            <Link
                v-for="cat in categoriesPreview"
                :key="cat.slug"
                :href="`/produk?kategori=${encodeURIComponent(cat.slug)}`"
                class="shrink-0 rounded-full border border-border/80 bg-white px-4 py-1.5 sm:px-5 sm:py-2 text-xs font-bold text-fg shadow-xs transition-colors hover:border-brand hover:text-brand"
            >
                {{ cat.name }}
            </Link>
        </div>

        <div
            v-if="featuredList.length > 0"
            class="mt-8 sm:mt-12 grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:grid-rows-2"
        >
            <div
                v-for="(prod, index) in featuredList.slice(0, 4)"
                :key="prod.slug"
                :class="['h-full w-full', getBentoClasses(index, Math.min(featuredList.length, 4))]"
            >
                <ProductCard
                    :product="prod"
                    :variant="getBentoVariant(index, Math.min(featuredList.length, 4)) as any"
                />
            </div>
        </div>

        <div v-else class="mt-8 sm:mt-12">
            <EmptyState
                title="Belum Ada Produk Unggulan"
                description="Saat ini belum ada produk unggulan kawasan yang ditambahkan ke etalase."
            />
        </div>
    </section>
</template>
