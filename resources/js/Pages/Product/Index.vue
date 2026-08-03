<script setup lang="ts">
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    Search,
    X,
    Filter,
    LayoutGrid,
    List,
    MapPin,
    Sprout,
    ShieldCheck,
    SlidersHorizontal,
    ArrowUpDown,
    RotateCcw,
} from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import ProductCard from "@/Components/product/ProductCard.vue";
import { Icon, Pagination, EmptyState, Badge } from "@/Components/ui";
import { useDebounceFn } from "@vueuse/core";
import type { PaginatedData } from "@/types/pagination";
import type { ProductCard as ProductCardType, TaxonomyRef } from "@/types/home";

defineOptions({ layout: PublicLayout });

interface RegionItem {
    id: number;
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

const currentCategory = computed(() => props.filters.kategori || "");
const currentRegion = computed(() => props.filters.region || "");
const currentSort = computed(() => props.filters.sort || "");
const searchQuery = computed(() => props.filters.q || "");

const hasActiveFilters = computed(() => {
    return !!(
        currentCategory.value ||
        currentRegion.value ||
        currentSort.value ||
        searchQuery.value
    );
});

const applyFilters = (newFilters: Record<string, string | undefined>) => {
    const merged = { ...props.filters, ...newFilters };
    
    // Clean up empty parameters
    Object.keys(merged).forEach((key) => {
        if (!merged[key as keyof typeof merged]) {
            delete merged[key as keyof typeof merged];
        }
    });

    router.get("/produk", merged, {
        preserveState: true,
        replace: true,
    });
};

const handleSearch = useDebounceFn((e: Event) => {
    const target = e.target as HTMLInputElement;
    applyFilters({ q: target.value });
}, 300);

const clearSearch = () => {
    applyFilters({ q: undefined });
};

const selectCategory = (slug: string) => {
    applyFilters({ kategori: slug || undefined });
};

const selectRegion = (slug: string) => {
    applyFilters({ region: slug || undefined });
};

const handleSortChange = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    applyFilters({ sort: target.value || undefined });
};

const resetAllFilters = () => {
    router.get("/produk", {}, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Katalog Produk - DigiPangan Merauke" />

    <main class="min-h-screen bg-bg">
        <!-- Hero Header -->
        <section class="relative overflow-hidden border-b border-border/80 bg-gradient-to-b from-brand/5 via-white to-bg py-12 sm:py-16">
            <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <!-- Badges Row -->
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-brand/20 bg-brand/10 px-3 py-1 text-xs font-bold text-brand">
                            <Icon :icon="Sprout" :size="14" />
                            <span>Pangan Lokal Merauke</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-border/80 bg-white px-3 py-1 text-xs font-semibold text-fg-muted shadow-xs">
                            <Icon :icon="ShieldCheck" :size="14" class="text-green-600" />
                            <span>Terverifikasi Tim Pendamping</span>
                        </span>
                    </div>

                    <h1 class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl lg:text-5xl">
                        Katalog Komoditas Unggulan
                    </h1>
                    <p class="mt-3 text-base text-fg-muted sm:text-lg leading-relaxed">
                        Jelajahi berbagai produk komoditas pertanian dan olahan segar langsung dari lahan transmigrasi Kabupaten Merauke.
                    </p>

                    <!-- Quick Stats Pill -->
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

        <!-- Main Content (Sidebar + Catalog Grid) -->
        <section class="mx-auto max-w-[90rem] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
                
                <!-- Sticky Filter Sidebar -->
                <aside class="w-full lg:w-72 shrink-0 space-y-6 lg:sticky lg:top-24">
                    <div class="rounded-2xl border border-border/80 bg-white p-5 shadow-xs space-y-6">
                        
                        <!-- Sidebar Title -->
                        <div class="flex items-center justify-between border-b border-border/60 pb-3">
                            <div class="flex items-center gap-2 font-bold text-fg">
                                <Icon :icon="SlidersHorizontal" :size="16" class="text-brand" />
                                <span>Filter Komoditas</span>
                            </div>
                            <button
                                v-if="hasActiveFilters"
                                @click="resetAllFilters"
                                class="text-xs font-semibold text-brand hover:underline flex items-center gap-1"
                            >
                                <Icon :icon="RotateCcw" :size="12" />
                                <span>Reset</span>
                            </button>
                        </div>

                        <!-- Search Box -->
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-fg-muted">
                                Pencarian Teks
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-fg-muted">
                                    <Icon :icon="Search" :size="16" />
                                </div>
                                <input
                                    type="text"
                                    :value="searchQuery"
                                    @input="handleSearch"
                                    placeholder="Cari beras, cabai..."
                                    class="w-full rounded-xl border border-border/80 bg-white py-2.5 pl-9 pr-8 text-sm text-fg shadow-xs transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
                                />
                                <button
                                    v-if="searchQuery"
                                    @click="clearSearch"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-fg-muted hover:text-fg"
                                >
                                    <Icon :icon="X" :size="14" />
                                </button>
                            </div>
                        </div>

                        <!-- Kategori Products Filter -->
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-fg-muted">
                                Kategori
                            </label>
                            <div class="flex flex-wrap gap-1.5 lg:flex-col lg:gap-1">
                                <button
                                    @click="selectCategory('')"
                                    :class="[
                                        'w-full text-left rounded-lg px-3 py-2 text-xs font-semibold transition-all flex items-center justify-between',
                                        currentCategory === ''
                                            ? 'bg-brand text-white shadow-xs'
                                            : 'text-fg-muted hover:bg-muted/40 hover:text-fg'
                                    ]"
                                >
                                    <span>Semua Kategori</span>
                                </button>
                                <button
                                    v-for="cat in categories"
                                    :key="cat.slug"
                                    @click="selectCategory(cat.slug)"
                                    :class="[
                                        'w-full text-left rounded-lg px-3 py-2 text-xs font-semibold transition-all flex items-center justify-between',
                                        currentCategory === cat.slug
                                            ? 'bg-brand text-white shadow-xs'
                                            : 'text-fg-muted hover:bg-muted/40 hover:text-fg'
                                    ]"
                                >
                                    <span>{{ cat.name }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Wilayah / Distrik Filter -->
                        <div v-if="regions && regions.length > 0">
                            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-fg-muted">
                                Distrik / Kawasan
                            </label>
                            <div class="flex flex-wrap gap-1.5 lg:flex-col lg:gap-1">
                                <button
                                    @click="selectRegion('')"
                                    :class="[
                                        'w-full text-left rounded-lg px-3 py-2 text-xs font-semibold transition-all flex items-center gap-2',
                                        currentRegion === ''
                                            ? 'bg-brand text-white shadow-xs'
                                            : 'text-fg-muted hover:bg-muted/40 hover:text-fg'
                                    ]"
                                >
                                    <Icon :icon="MapPin" :size="14" />
                                    <span>Semua Wilayah</span>
                                </button>
                                <button
                                    v-for="reg in regions"
                                    :key="reg.slug"
                                    @click="selectRegion(reg.slug)"
                                    :class="[
                                        'w-full text-left rounded-lg px-3 py-2 text-xs font-semibold transition-all flex items-center gap-2',
                                        currentRegion === reg.slug
                                            ? 'bg-brand text-white shadow-xs'
                                            : 'text-fg-muted hover:bg-muted/40 hover:text-fg'
                                    ]"
                                >
                                    <Icon :icon="MapPin" :size="14" />
                                    <span>{{ reg.name }}</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </aside>

                <!-- Catalog Content Area -->
                <div class="flex-1 space-y-6">

                    <!-- Toolbar (Results count, Active Filter Chips, Sort & View Mode) -->
                    <div class="flex flex-col gap-4 rounded-2xl border border-border/80 bg-white p-4 shadow-xs sm:flex-row sm:items-center sm:justify-between">
                        
                        <!-- Count & Active Chips -->
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-fg">
                                Menampilkan
                                <span class="font-bold text-brand">
                                    {{ products.meta?.total ?? products.data.length }}
                                </span>
                                Komoditas
                            </p>

                            <!-- Active Filter Chips -->
                            <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-1.5 pt-1">
                                <span
                                    v-if="searchQuery"
                                    class="inline-flex items-center gap-1 rounded-md bg-muted/60 px-2 py-0.5 text-xs text-fg font-medium"
                                >
                                    Teks: "{{ searchQuery }}"
                                    <button @click="clearSearch" class="hover:text-red-500">
                                        <Icon :icon="X" :size="12" />
                                    </button>
                                </span>
                                <span
                                    v-if="currentCategory"
                                    class="inline-flex items-center gap-1 rounded-md bg-brand/10 text-brand px-2 py-0.5 text-xs font-medium"
                                >
                                    Kat: {{ currentCategory }}
                                    <button @click="selectCategory('')" class="hover:text-red-500">
                                        <Icon :icon="X" :size="12" />
                                    </button>
                                </span>
                                <span
                                    v-if="currentRegion"
                                    class="inline-flex items-center gap-1 rounded-md bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 text-xs font-medium"
                                >
                                    Distrik: {{ currentRegion }}
                                    <button @click="selectRegion('')" class="hover:text-red-500">
                                        <Icon :icon="X" :size="12" />
                                    </button>
                                </span>
                            </div>
                        </div>

                        <!-- Right Control Actions (Sort & View Layout) -->
                        <div class="flex items-center gap-3">
                            <!-- Sort Selector -->
                            <div class="relative">
                                <select
                                    :value="currentSort"
                                    @change="handleSortChange"
                                    class="h-9 rounded-xl border border-border/80 bg-white pl-3 pr-8 text-xs font-semibold text-fg shadow-xs transition-all focus:border-brand focus:ring-brand focus:outline-none"
                                >
                                    <option value="">Urutan: Terbaru</option>
                                    <option value="price">Harga: Terendah</option>
                                    <option value="-price">Harga: Tertinggi</option>
                                    <option value="name">Nama (A - Z)</option>
                                </select>
                            </div>

                            <!-- View Mode Toggle Buttons -->
                            <div class="flex items-center rounded-xl border border-border/80 bg-muted/20 p-0.5">
                                <button
                                    @click="viewMode = 'grid'"
                                    :class="[
                                        'flex size-8 items-center justify-center rounded-lg transition-colors',
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
                                        'flex size-8 items-center justify-center rounded-lg transition-colors',
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

                    <!-- Products Output Grid / List -->
                    <div v-if="products.data.length > 0">
                        <!-- Grid Layout -->
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

                        <!-- List Layout -->
                        <div v-else class="space-y-4">
                            <ProductCard
                                v-for="product in products.data"
                                :key="product.slug"
                                :product="product"
                                variant="horizontal"
                            />
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-2xl border border-border/80 bg-white py-16 text-center shadow-xs">
                        <EmptyState
                            title="Komoditas Tidak Ditemukan"
                            description="Maaf, tidak ada produk komoditas yang sesuai dengan kriteria filter atau pencarian Anda."
                        />
                        <button
                            v-if="hasActiveFilters"
                            @click="resetAllFilters"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-brand/90"
                        >
                            <Icon :icon="RotateCcw" :size="14" />
                            <span>Tampilkan Semua Komoditas</span>
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.meta && products.meta.last_page > 1" class="flex justify-center border-t border-border/60 pt-8">
                        <Pagination :meta="products.meta" />
                    </div>

                </div>
            </div>
        </section>
    </main>
</template>
