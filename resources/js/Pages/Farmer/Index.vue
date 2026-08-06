<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import {
    Users,
    Search,
    X,
    MapPin,
    ShieldCheck,
    RotateCcw,
} from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import FarmerCard from "@/Components/farmer/FarmerCard.vue";
import { Icon, Pagination, EmptyState, Badge, Button } from "@/Components/ui";
import type { PaginatedData } from "@/types/pagination";
import type { FarmerCard as FarmerCardType } from "@/types/home";

defineOptions({ layout: PublicLayout });

interface RegionItem {
    id?: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    farmers: PaginatedData<FarmerCardType>;
    regions: RegionItem[];
    filters: {
        wilayah?: string | null;
        q?: string | null;
    };
}>();

const searchInput = ref(props.filters.q ?? "");

watch(
    () => props.filters.q,
    (val) => {
        searchInput.value = val ?? "";
    },
);

const selectedRegion = computed(() => props.filters.wilayah ?? null);

const hasActiveFilters = computed(
    () => !!selectedRegion.value || !!props.filters.q,
);

const applyFilters = (newFilters: Record<string, string | undefined | null>) => {
    const merged = { ...props.filters, ...newFilters };
    const cleaned: Record<string, string> = {};
    Object.keys(merged).forEach((key) => {
        const val = merged[key as keyof typeof merged];
        if (val) cleaned[key] = String(val);
    });
    router.get("/petani", cleaned, { preserveState: true, replace: true });
};

const selectRegion = (slug: string | null) => {
    applyFilters({ wilayah: slug ?? undefined });
};

const submitSearch = () => {
    applyFilters({ q: searchInput.value.trim() || undefined });
};

const clearSearch = () => {
    searchInput.value = "";
    applyFilters({ q: undefined });
};

const resetAll = () => {
    searchInput.value = "";
    router.get("/petani", {}, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Direktori Petani - DigiPangan Merauke" />

    <main class="min-h-screen bg-bg">
        <section class="border-b border-border/80 bg-white py-12 sm:py-16">
            <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <Badge variant="brand" :icon="Users">
                            Pelaku Pangan Lokal
                        </Badge>
                        <Badge variant="neutral" :icon="ShieldCheck">
                            Terverifikasi Tim Pendamping
                        </Badge>
                    </div>

                    <h1
                        class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl lg:text-5xl"
                    >
                        Direktori Petani Transmigrasi
                    </h1>
                    <p
                        class="mt-3 text-base leading-relaxed text-fg-muted sm:text-lg"
                    >
                        Kenali para petani di balik komoditas unggulan Merauke.
                        Telusuri profil, kelompok tani, dan hasil panen yang
                        mereka jual langsung dari lahan transmigrasi.
                    </p>
                </div>
            </div>
        </section>

        <section
            class="mx-auto max-w-[90rem] px-4 py-8 sm:px-6 sm:py-12 lg:px-8"
        >
            <div
                class="mb-8 flex flex-col gap-4 rounded-2xl border border-border/80 bg-white p-4 shadow-xs sm:p-5"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <form
                        class="relative flex-1"
                        @submit.prevent="submitSearch"
                    >
                        <span
                            class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-fg-muted"
                        >
                            <Icon :icon="Search" :size="16" />
                        </span>
                        <input
                            v-model="searchInput"
                            type="search"
                            placeholder="Cari nama petani..."
                            class="h-10 w-full rounded-xl border border-border/80 bg-white pl-9 pr-9 text-sm text-fg shadow-xs transition-all focus:border-brand focus:outline-none"
                        />
                        <button
                            v-if="searchInput"
                            type="button"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-fg-muted hover:text-danger"
                        >
                            <Icon :icon="X" :size="16" />
                        </button>
                    </form>

                    <p class="text-sm font-semibold text-fg sm:pl-2">
                        <span class="font-bold text-brand">
                            {{ farmers.meta?.total ?? farmers.data.length }}
                        </span>
                        Petani
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="selectRegion(null)"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                            !selectedRegion
                                ? 'bg-brand text-white shadow-xs'
                                : 'bg-muted/60 text-fg-muted hover:bg-muted hover:text-fg',
                        ]"
                    >
                        Semua Distrik
                    </button>
                    <button
                        v-for="region in regions"
                        :key="region.slug"
                        type="button"
                        @click="selectRegion(region.slug)"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                            selectedRegion === region.slug
                                ? 'bg-brand text-white shadow-xs'
                                : 'bg-muted/60 text-fg-muted hover:bg-muted hover:text-fg',
                        ]"
                    >
                        <Icon :icon="MapPin" :size="12" />
                        {{ region.name }}
                    </button>
                </div>
            </div>

            <div v-if="farmers.data.length > 0">
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <FarmerCard
                        v-for="farmer in farmers.data"
                        :key="farmer.slug"
                        :farmer="farmer"
                    />
                </div>

                <div
                    v-if="farmers.meta && farmers.meta.last_page > 1"
                    class="flex justify-center pt-8"
                >
                    <Pagination :meta="farmers.meta" :links="farmers.links" />
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl border border-border/80 bg-white py-16 text-center shadow-xs"
            >
                <EmptyState
                    title="Petani Tidak Ditemukan"
                    description="Belum ada petani yang sesuai dengan filter atau pencarian Anda saat ini."
                />
                <Button
                    v-if="hasActiveFilters"
                    @click="resetAll"
                    class="mt-4 gap-2 text-xs font-bold"
                >
                    <Icon :icon="RotateCcw" :size="14" />
                    <span>Tampilkan Semua Petani</span>
                </Button>
            </div>
        </section>
    </main>
</template>
