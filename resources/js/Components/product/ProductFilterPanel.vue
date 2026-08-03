<script setup lang="ts">
import {
    Search,
    X,
    MapPin,
    SlidersHorizontal,
    RotateCcw,
} from "@lucide/vue";
import { Icon } from "@/Components/ui";

interface RegionItem {
    id: number;
    name: string;
    slug: string;
}

interface TaxonomyRef {
    id: number;
    name: string;
    slug: string;
}

defineProps<{
    searchQuery: string;
    currentCategory: string;
    currentRegion: string;
    categories: TaxonomyRef[];
    regions?: RegionItem[];
    hasActiveFilters: boolean;
}>();

const emit = defineEmits<{
    (e: "search", query: string): void;
    (e: "clear-search"): void;
    (e: "select-category", slug: string): void;
    (e: "select-region", slug: string): void;
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
                    class="text-xs font-semibold text-brand hover:underline flex items-center gap-1"
                >
                    <Icon :icon="RotateCcw" :size="12" />
                    <span>Reset</span>
                </button>
            </div>

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
                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-fg-muted hover:text-fg"
                    >
                        <Icon :icon="X" :size="14" />
                    </button>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-fg-muted">
                    Kategori
                </label>
                <div class="flex flex-wrap gap-1.5 lg:flex-col lg:gap-1">
                    <button
                        @click="emit('select-category', '')"
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
                        @click="emit('select-category', cat.slug)"
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

            <div v-if="regions && regions.length > 0">
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-fg-muted">
                    Distrik / Kawasan
                </label>
                <div class="flex flex-wrap gap-1.5 lg:flex-col lg:gap-1">
                    <button
                        @click="emit('select-region', '')"
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
                        @click="emit('select-region', reg.slug)"
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
</template>
