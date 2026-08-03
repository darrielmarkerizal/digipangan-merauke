<script setup lang="ts">
import { computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Search } from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import ProductCard from "@/Components/product/ProductCard.vue";
import { Icon, Pagination, EmptyState } from "@/Components/ui";
import { useDebounceFn } from "@vueuse/core";
import type { PaginatedData } from "@/types/pagination";
import type { ProductCard as ProductCardType, TaxonomyRef } from "@/types/home";

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    products: PaginatedData<ProductCardType>;
    categories: TaxonomyRef[];
    filters: {
        kategori?: string;
        q?: string;
    };
}>();

const currentCategory = computed(() => props.filters.kategori || "");
const searchQuery = computed(() => props.filters.q || "");

const handleSearch = useDebounceFn((e: Event) => {
    const target = e.target as HTMLInputElement;
    router.get(
        "/produk",
        { ...props.filters, q: target.value },
        { preserveState: true, replace: true }
    );
}, 300);
</script>

<template>
    <Head title="Katalog Produk - DigiPangan Merauke" />

    <main class="min-h-screen bg-bg">
        <!-- Header -->
        <section class="bg-card py-12 sm:py-16 border-b border-border">
            <div class="mx-auto max-w-[90rem] px-3 sm:px-5 lg:px-6">
                <div class="max-w-3xl">
                    <h1 class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl lg:text-5xl">
                        Katalog Produk Unggulan
                    </h1>
                    <p class="mt-4 text-lg text-fg-muted">
                        Temukan berbagai hasil panen terbaik dan produk olahan dari lahan transmigran Kabupaten Merauke.
                    </p>
                </div>
            </div>
        </section>

        <!-- Filters & Catalog -->
        <section class="mx-auto max-w-[90rem] px-3 py-8 sm:px-5 sm:py-12 lg:px-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <!-- Search & Categories -->
                <div class="w-full lg:w-1/4 shrink-0 space-y-6">
                    <!-- Search -->
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <Icon :icon="Search" class="text-fg-muted" :size="18" />
                        </div>
                        <input
                            type="text"
                            :value="searchQuery"
                            @input="handleSearch"
                            placeholder="Cari produk..."
                            class="block w-full rounded-full border-border/80 bg-white py-2.5 pl-10 pr-4 text-sm text-fg shadow-sm focus:border-brand focus:ring-brand"
                        />
                    </div>

                    <!-- Categories -->
                    <div>
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-fg-muted">
                            Kategori Produk
                        </h3>
                        <div class="flex flex-wrap gap-2 lg:flex-col lg:gap-1">
                            <Link
                                href="/produk"
                                :class="[
                                    'rounded-full lg:rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                                    currentCategory === ''
                                        ? 'bg-brand text-white'
                                        : 'bg-white text-fg hover:bg-muted/50 border border-border/80 lg:border-transparent'
                                ]"
                            >
                                Semua Produk
                            </Link>
                            <Link
                                v-for="cat in categories"
                                :key="cat.slug"
                                :href="`/produk?kategori=${encodeURIComponent(cat.slug)}`"
                                :class="[
                                    'rounded-full lg:rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                                    currentCategory === cat.slug
                                        ? 'bg-brand text-white'
                                        : 'bg-white text-fg hover:bg-muted/50 border border-border/80 lg:border-transparent'
                                ]"
                            >
                                {{ cat.name }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1">
                    <div v-if="products.data.length > 0" class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.slug"
                            :product="product"
                        />
                    </div>
                    
                    <div v-else class="py-12">
                        <EmptyState
                            title="Produk Tidak Ditemukan"
                            description="Maaf, kami tidak dapat menemukan produk yang sesuai dengan pencarian atau filter Anda."
                        />
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.meta.last_page > 1" class="mt-8 flex justify-center border-t border-border/50 pt-8">
                        <Pagination :meta="products.meta" />
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>
