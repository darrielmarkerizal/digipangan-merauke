<script setup lang="ts">
import { ref, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import {
    X,
    LayoutGrid,
    List,
    MapPin,
    Sprout,
    ShieldCheck,
    RotateCcw,
} from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import ProductCard from "@/Components/product/ProductCard.vue";
import ProductFilterPanel from "@/Components/product/ProductFilterPanel.vue";
import { Icon, Pagination, EmptyState, Badge, Button } from "@/Components/ui";
import type { PaginatedData } from "@/types/pagination";
import type { ProductCard as ProductCardType, TaxonomyRef } from "@/types/home";

defineOptions({ layout: PublicLayout });

interface RegionItem {
    id?: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    products: PaginatedData<ProductCardType>;
    categories: TaxonomyRef[];
    regions?: RegionItem[];
    filters: {
        kategori?: string;
        region?: string;
        sort?: string;
        q?: string;
    };
}>();

const viewMode = ref<"grid" | "list">("grid");

const selectedCategories = computed<string[]>(() => {
    return props.filters.kategori ? props.filters.kategori.split(",").filter(Boolean) : [];
});

const selectedRegions = computed<string[]>(() => {
    return props.filters.region ? props.filters.region.split(",").filter(Boolean) : [];
});

const currentSort = computed(() => props.filters.sort || "-created_at");
const searchQuery = computed(() => props.filters.q || "");

const hasActiveFilters = computed(() => {
    return !!(
        selectedCategories.value.length > 0 ||
        selectedRegions.value.length > 0 ||
        (props.filters.sort && props.filters.sort !== "-created_at") ||
        searchQuery.value
    );
});

const applyFilters = (newFilters: Record<string, string | undefined>) => {
    const merged = { ...props.filters, ...newFilters };
    
    const cleanedFilters: Record<string, string> = {};
    Object.keys(merged).forEach((key) => {
        const val = merged[key as keyof typeof merged];
        if (val) {
            cleanedFilters[key] = val;
        }
    });

    router.get("/produk", cleanedFilters, {
        preserveState: true,
        replace: true,
    });
};

const handleSearch = (query: string) => {
    applyFilters({ q: query });
};

const clearSearch = () => {
    applyFilters({ q: undefined });
};

const toggleCategory = (slug: string) => {
    const current = [...selectedCategories.value];
    const idx = current.indexOf(slug);
    if (idx > -1) {
        current.splice(idx, 1);
    } else {
        current.push(slug);
    }
    applyFilters({ kategori: current.join(",") || undefined });
};

const clearCategories = () => {
    applyFilters({ kategori: undefined });
};

const toggleRegion = (slug: string) => {
    const current = [...selectedRegions.value];
    const idx = current.indexOf(slug);
    if (idx > -1) {
        current.splice(idx, 1);
    } else {
        current.push(slug);
    }
    applyFilters({ region: current.join(",") || undefined });
};

const clearRegions = () => {
    applyFilters({ region: undefined });
};

const handleSortChange = (e: Event) => {
    const val = (e.target as HTMLSelectElement).value;
    applyFilters({ sort: val === "-created_at" ? undefined : val });
};

const resetAllFilters = () => {
    router.get("/produk", {}, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Katalog Produk - DigiPangan Merauke" />

    <main class="min-h-screen bg-bg">
        <section class="border-b border-border/80 bg-white py-12 sm:py-16">
            <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <Badge variant="brand" :icon="Sprout">
                            Pangan Lokal Merauke
                        </Badge>
                        <Badge variant="neutral" :icon="ShieldCheck">
                            Terverifikasi Tim Pendamping
                        </Badge>
                    </div>

                    <h1 class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl lg:text-5xl">
                        Katalog Komoditas Unggulan
                    </h1>
                    <p class="mt-3 text-base text-fg-muted sm:text-lg leading-relaxed">
                        Jelajahi berbagai produk komoditas pertanian dan olahan segar langsung dari lahan transmigrasi Kabupaten Merauke.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-6 pt-4 border-t border-border/60 text-xs font-medium text-fg-muted">
                        <div class="flex items-center gap-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-white shadow-xs border border-border/60 text-brand">
                                <Icon :icon="Sprout" :size="14" />
                            </div>
                            <span>Hasil Panen Berkualitas</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-white shadow-xs border border-border/60 text-brand">
                                <Icon :icon="MapPin" :size="14" />
                            </div>
                            <span>Distrik Transmigrasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-[90rem] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
                
                <ProductFilterPanel
                    :search-query="searchQuery"
                    :selected-categories="selectedCategories"
                    :selected-regions="selectedRegions"
                    :categories="categories"
                    :regions="regions"
                    :has-active-filters="hasActiveFilters"
                    @search="handleSearch"
                    @clear-search="clearSearch"
                    @toggle-category="toggleCategory"
                    @clear-categories="clearCategories"
                    @toggle-region="toggleRegion"
                    @clear-regions="clearRegions"
                    @reset-all="resetAllFilters"
                />

                <div class="flex-1 space-y-6">

                    <div class="flex flex-col gap-4 rounded-2xl border border-border/80 bg-white p-4 shadow-xs sm:flex-row sm:items-center sm:justify-between">
                        
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-fg">
                                Menampilkan
                                <span class="font-bold text-brand">
                                    {{ products.meta?.total ?? products.data.length }}
                                </span>
                                Komoditas
                            </p>

                            <!-- Active Filter Chips Bar -->
                            <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-1.5 pt-1">
                                <span
                                    v-if="searchQuery"
                                    class="inline-flex items-center gap-1 rounded-md bg-muted/60 px-2 py-0.5 text-xs text-fg font-medium"
                                >
                                    Teks: "{{ searchQuery }}"
                                    <button @click="clearSearch" class="hover:text-red-500 cursor-pointer">
                                        <Icon :icon="X" :size="12" />
                                    </button>
                                </span>

                                <span
                                    v-for="catSlug in selectedCategories"
                                    :key="catSlug"
                                    class="inline-flex items-center gap-1 rounded-md bg-brand/10 text-brand px-2 py-0.5 text-xs font-medium"
                                >
                                    Kat: {{ categories.find(c => c.slug === catSlug)?.name || catSlug }}
                                    <button @click="toggleCategory(catSlug)" class="hover:text-red-500 cursor-pointer">
                                        <Icon :icon="X" :size="12" />
                                    </button>
                                </span>

                                <span
                                    v-for="regSlug in selectedRegions"
                                    :key="regSlug"
                                    class="inline-flex items-center gap-1 rounded-md bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 text-xs font-medium"
                                >
                                    Distrik: {{ regions?.find(r => r.slug === regSlug)?.name || regSlug }}
                                    <button @click="toggleRegion(regSlug)" class="hover:text-red-500 cursor-pointer">
                                        <Icon :icon="X" :size="12" />
                                    </button>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <select
                                    :value="currentSort"
                                    @change="handleSortChange"
                                    class="h-9 cursor-pointer rounded-xl border border-border/80 bg-white px-3 text-xs font-semibold text-fg shadow-xs transition-all focus:border-brand focus:outline-none"
                                >
                                    <option value="-created_at">Urutan: Terbaru</option>
                                    <option value="price">Harga: Terendah</option>
                                    <option value="-price">Harga: Tertinggi</option>
                                    <option value="name">Nama (A - Z)</option>
                                </select>
                            </div>

                            <div class="flex items-center rounded-xl border border-border/80 bg-muted/20 p-0.5">
                                <button
                                    @click="viewMode = 'grid'"
                                    :class="[
                                        'flex size-8 items-center justify-center rounded-lg transition-colors cursor-pointer',
                                        viewMode === 'grid'
                                            ? 'bg-white text-brand shadow-xs'
                                            : 'text-fg-muted hover:text-fg'
                                    ]"
                                    title="Tampilan Grid"
                                >
                                    <Icon :icon="LayoutGrid" :size="16" />
                                </button>
                                <button
                                    @click="viewMode = 'list'"
                                    :class="[
                                        'flex size-8 items-center justify-center rounded-lg transition-colors cursor-pointer',
                                        viewMode === 'list'
                                            ? 'bg-white text-brand shadow-xs'
                                            : 'text-fg-muted hover:text-fg'
                                    ]"
                                    title="Tampilan List"
                                >
                                    <Icon :icon="List" :size="16" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="products.data.length > 0">
                        <div
                            v-if="viewMode === 'grid'"
                            class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3"
                        >
                            <ProductCard
                                v-for="product in products.data"
                                :key="product.slug"
                                :product="product"
                            />
                        </div>

                        <div v-else class="space-y-4">
                            <ProductCard
                                v-for="product in products.data"
                                :key="product.slug"
                                :product="product"
                                variant="horizontal"
                            />
                        </div>
                    </div>

                    <div v-else class="rounded-2xl border border-border/80 bg-white py-16 text-center shadow-xs">
                        <EmptyState
                            title="Komoditas Tidak Ditemukan"
                            description="Maaf, tidak ada produk komoditas yang sesuai dengan kriteria filter atau pencarian Anda."
                        />
                        <Button
                            v-if="hasActiveFilters"
                            @click="resetAllFilters"
                            class="mt-4 gap-2 text-xs font-bold"
                        >
                            <Icon :icon="RotateCcw" :size="14" />
                            <span>Tampilkan Semua Komoditas</span>
                        </Button>
                    </div>

                    <div v-if="products.meta && products.meta.last_page > 1" class="flex justify-center pt-6">
                        <Pagination :meta="products.meta" :links="products.links" />
                    </div>

                </div>
            </div>
        </section>
    </main>
</template>
