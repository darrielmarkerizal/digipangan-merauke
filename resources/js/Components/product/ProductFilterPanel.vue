<script setup lang="ts">
import {
    Search,
    X,
    MapPin,
    SlidersHorizontal,
    RotateCcw,
    Check,
} from "@lucide/vue";
import { Icon } from "@/Components/ui";
import type { TaxonomyRef } from "@/types/home";

interface RegionItem {
    id?: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    searchQuery: string;
    selectedCategories: string[];
    selectedRegions: string[];
    categories: TaxonomyRef[];
    regions?: RegionItem[];
    hasActiveFilters: boolean;
}>();

const emit = defineEmits<{
    (e: "search", query: string): void;
    (e: "clear-search"): void;
    (e: "toggle-category", slug: string): void;
    (e: "clear-categories"): void;
    (e: "toggle-region", slug: string): void;
    (e: "clear-regions"): void;
    (e: "reset-all"): void;
}>();
</script>

<template>
    <aside class="w-full lg:w-72 shrink-0 space-y-6 lg:sticky lg:top-24 z-10">
        <div class="rounded-2xl border border-border/80 bg-white p-5 shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-border/60 pb-3">
                <div class="flex items-center gap-2 font-bold text-fg">
                    <Icon :icon="SlidersHorizontal" :size="16" class="text-brand" />
                    <span>Filter Komoditas</span>
                </div>
                <button
                    v-if="hasActiveFilters"
                    @click="emit('reset-all')"
                    class="text-xs font-semibold text-brand hover:underline flex items-center gap-1 cursor-pointer"
                >
                    <Icon :icon="RotateCcw" :size="12" />
                    <span>Reset</span>
                </button>
            </div>

            <!-- Search Input -->
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
                        @input="emit('search', ($event.target as HTMLInputElement).value)"
                        placeholder="Cari beras, cabai..."
                        class="w-full rounded-xl border border-border/80 bg-white py-2.5 pl-9 pr-8 text-sm text-fg shadow-xs transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
                    />
                    <button
                        v-if="searchQuery"
                        @click="emit('clear-search')"
                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-fg-muted hover:text-fg cursor-pointer"
                    >
                        <Icon :icon="X" :size="14" />
                    </button>
                </div>
            </div>

            <!-- Multi Category Filter (Checkboxes) -->
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-fg-muted">
                        Kategori
                    </label>
                    <button
                        v-if="selectedCategories.length > 0"
                        @click="emit('clear-categories')"
                        class="text-[11px] text-fg-muted hover:text-brand cursor-pointer"
                    >
                        Hapus ({{ selectedCategories.length }})
                    </button>
                </div>
                <div class="space-y-1">
                    <button
                        type="button"
                        @click="emit('clear-categories')"
                        :class="[
                            'w-full text-left rounded-lg px-3 py-2 text-xs font-semibold transition-all flex items-center justify-between cursor-pointer',
                            selectedCategories.length === 0
                                ? 'bg-brand text-white shadow-xs'
                                : 'text-fg-muted hover:bg-muted/40 hover:text-fg'
                        ]"
                    >
                        <span>Semua Kategori</span>
                        <Icon v-if="selectedCategories.length === 0" :icon="Check" :size="14" />
                    </button>

                    <div
                        v-for="cat in categories"
                        :key="cat.slug"
                        @click="emit('toggle-category', cat.slug)"
                        :class="[
                            'w-full text-left rounded-lg px-3 py-2 text-xs font-medium transition-all flex items-center justify-between cursor-pointer select-none border',
                            selectedCategories.includes(cat.slug)
                                ? 'bg-brand/10 border-brand/40 text-brand font-semibold'
                                : 'bg-white border-transparent text-fg-muted hover:bg-muted/40 hover:text-fg'
                        ]"
                    >
                        <div class="flex items-center gap-2.5">
                            <div
                                :class="[
                                    'size-4 rounded flex items-center justify-center border transition-colors',
                                    selectedCategories.includes(cat.slug)
                                        ? 'bg-brand border-brand text-white'
                                        : 'border-border bg-white'
                                ]"
                            >
                                <Icon v-if="selectedCategories.includes(cat.slug)" :icon="Check" :size="12" />
                            </div>
                            <span>{{ cat.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Multi Region Filter (Checkboxes) -->
            <div v-if="regions && regions.length > 0">
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-fg-muted">
                        Distrik / Kawasan
                    </label>
                    <button
                        v-if="selectedRegions.length > 0"
                        @click="emit('clear-regions')"
                        class="text-[11px] text-fg-muted hover:text-brand cursor-pointer"
                    >
                        Hapus ({{ selectedRegions.length }})
                    </button>
                </div>
                <div class="space-y-1">
                    <button
                        type="button"
                        @click="emit('clear-regions')"
                        :class="[
                            'w-full text-left rounded-lg px-3 py-2 text-xs font-semibold transition-all flex items-center justify-between cursor-pointer',
                            selectedRegions.length === 0
                                ? 'bg-brand text-white shadow-xs'
                                : 'text-fg-muted hover:bg-muted/40 hover:text-fg'
                        ]"
                    >
                        <div class="flex items-center gap-2">
                            <Icon :icon="MapPin" :size="14" />
                            <span>Semua Wilayah</span>
                        </div>
                        <Icon v-if="selectedRegions.length === 0" :icon="Check" :size="14" />
                    </button>

                    <div
                        v-for="reg in regions"
                        :key="reg.slug"
                        @click="emit('toggle-region', reg.slug)"
                        :class="[
                            'w-full text-left rounded-lg px-3 py-2 text-xs font-medium transition-all flex items-center justify-between cursor-pointer select-none border',
                            selectedRegions.includes(reg.slug)
                                ? 'bg-green-50 border-green-300 text-green-800 font-semibold'
                                : 'bg-white border-transparent text-fg-muted hover:bg-muted/40 hover:text-fg'
                        ]"
                    >
                        <div class="flex items-center gap-2.5">
                            <div
                                :class="[
                                    'size-4 rounded flex items-center justify-center border transition-colors',
                                    selectedRegions.includes(reg.slug)
                                        ? 'bg-green-600 border-green-600 text-white'
                                        : 'border-border bg-white'
                                ]"
                            >
                                <Icon v-if="selectedRegions.includes(reg.slug)" :icon="Check" :size="12" />
                            </div>
                            <span>{{ reg.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </aside>
</template>
