<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Search, X, RotateCcw, Megaphone } from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import PostCard from "@/Components/post/PostCard.vue";
import { Icon, Pagination, EmptyState, Badge, Button } from "@/Components/ui";
import type { PaginatedData } from "@/types/pagination";
import type { PostCard as PostCardType } from "@/types/home";

defineOptions({ layout: PublicLayout });

interface CategoryItem {
    name: string;
    slug: string;
    posts_count: number;
}

const props = defineProps<{
    posts: PaginatedData<PostCardType>;
    categories: CategoryItem[];
    filters: {
        kategori?: string | null;
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

const selectedCategory = computed(() => props.filters.kategori ?? null);

const hasActiveFilters = computed(
    () => !!selectedCategory.value || !!props.filters.q,
);

const applyFilters = (newFilters: Record<string, string | undefined | null>) => {
    const merged = { ...props.filters, ...newFilters };
    const cleaned: Record<string, string> = {};
    Object.keys(merged).forEach((key) => {
        const val = merged[key as keyof typeof merged];
        if (val) cleaned[key] = String(val);
    });
    router.get("/berita", cleaned, { preserveState: true, replace: true });
};

const selectCategory = (slug: string | null) => {
    applyFilters({ kategori: slug ?? undefined });
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
    router.get("/berita", {}, { preserveState: true, replace: true });
};

const featured = computed(() =>
    !hasActiveFilters.value && props.posts.data.length > 0
        ? props.posts.data[0]
        : null,
);
const restPosts = computed(() =>
    featured.value ? props.posts.data.slice(1) : props.posts.data,
);
</script>

<template>
    <Head title="Berita & Informasi - DigiPangan Merauke" />

    <main class="min-h-screen bg-bg">
        <section class="border-b border-border/80 bg-white py-12 sm:py-16">
            <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <Badge variant="brand" :icon="Megaphone">
                            Kabar Kawasan Transmigrasi
                        </Badge>
                    </div>

                    <h1
                        class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl lg:text-5xl"
                    >
                        Berita &amp; Informasi
                    </h1>
                    <p
                        class="mt-3 text-base leading-relaxed text-fg-muted sm:text-lg"
                    >
                        Ikuti kabar panen, kegiatan kelompok tani, pelatihan, dan
                        informasi harga pasar terkini dari kawasan transmigrasi
                        Merauke.
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
                            placeholder="Cari berita..."
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
                            {{ posts.meta?.total ?? posts.data.length }}
                        </span>
                        Artikel
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="selectCategory(null)"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                            !selectedCategory
                                ? 'bg-brand text-white shadow-xs'
                                : 'bg-muted/60 text-fg-muted hover:bg-muted hover:text-fg',
                        ]"
                    >
                        Semua Kategori
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.slug"
                        type="button"
                        @click="selectCategory(category.slug)"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors',
                            selectedCategory === category.slug
                                ? 'bg-brand text-white shadow-xs'
                                : 'bg-muted/60 text-fg-muted hover:bg-muted hover:text-fg',
                        ]"
                    >
                        {{ category.name }}
                        <span class="opacity-70">({{ category.posts_count }})</span>
                    </button>
                </div>
            </div>

            <div v-if="posts.data.length > 0" class="space-y-6">
                <PostCard
                    v-if="featured"
                    :post="featured"
                    variant="feature"
                />

                <div
                    v-if="restPosts.length > 0"
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <PostCard
                        v-for="post in restPosts"
                        :key="post.slug"
                        :post="post"
                    />
                </div>

                <div
                    v-if="posts.meta && posts.meta.last_page > 1"
                    class="flex justify-center pt-4"
                >
                    <Pagination :meta="posts.meta" :links="posts.links" />
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl border border-border/80 bg-white py-16 text-center shadow-xs"
            >
                <EmptyState
                    title="Berita Tidak Ditemukan"
                    description="Belum ada artikel yang sesuai dengan kategori atau pencarian Anda saat ini."
                />
                <Button
                    v-if="hasActiveFilters"
                    @click="resetAll"
                    class="mt-4 gap-2 text-xs font-bold"
                >
                    <Icon :icon="RotateCcw" :size="14" />
                    <span>Tampilkan Semua Berita</span>
                </Button>
            </div>
        </section>
    </main>
</template>
