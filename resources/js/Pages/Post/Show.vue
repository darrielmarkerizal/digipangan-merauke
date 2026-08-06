<script setup lang="ts">
import { computed, onMounted, onUnmounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    ArrowLeft,
    CalendarDays,
    UserRound,
    Share2,
    Newspaper,
} from "@lucide/vue";
import { toast } from "vue-sonner";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import PostCard from "@/Components/post/PostCard.vue";
import { Icon, Breadcrumb } from "@/Components/ui";
import { formatTanggal } from "@/lib/format";
import type { PostCard as PostCardType } from "@/types/home";

defineOptions({ layout: PublicLayout });

interface PostDetail {
    title: string;
    slug: string;
    published_at: string | null;
    body: string;
    cover: { original: string; card: string } | null;
    category: { name: string; slug: string } | null;
    author: { name: string } | null;
    seo: {
        title: string;
        description: string;
        canonical: string;
        og_image?: string | null;
        structured_data?: Record<string, unknown>;
    };
}

const props = defineProps<{
    post: PostDetail;
    relatedPosts?: PostCardType[];
}>();

const structuredData = computed(() =>
    props.post.seo.structured_data
        ? JSON.stringify(props.post.seo.structured_data)
        : null,
);

const copyLink = () => {
    if (navigator.clipboard && window.location.href) {
        navigator.clipboard.writeText(window.location.href);
        toast.success("Tautan berita berhasil disalin!");
    }
};

// Inertia's <Head> strips <script> tags, so inject the schema.org Article
// JSON-LD (NFR-10) directly into <head> on mount.
const LD_ID = "post-structured-data";

onMounted(() => {
    if (!structuredData.value) return;
    let el = document.getElementById(LD_ID) as HTMLScriptElement | null;
    if (!el) {
        el = document.createElement("script");
        el.id = LD_ID;
        el.type = "application/ld+json";
        document.head.appendChild(el);
    }
    el.textContent = structuredData.value;
});

onUnmounted(() => {
    document.getElementById(LD_ID)?.remove();
});
</script>

<template>
    <Head>
        <title>{{ post.seo.title }}</title>
        <meta name="description" :content="post.seo.description" />
        <link rel="canonical" :href="post.seo.canonical" />
        <meta property="og:title" :content="post.seo.title" />
        <meta property="og:description" :content="post.seo.description" />
        <meta
            v-if="post.seo.og_image"
            property="og:image"
            :content="post.seo.og_image"
        />
    </Head>

    <main class="min-h-screen bg-bg pb-16">
        <div class="mx-auto max-w-3xl px-4 pt-6 pb-2 sm:px-6">
            <div class="mb-4 flex items-center justify-between gap-4">
                <Link
                    href="/berita"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-fg-muted transition-colors hover:text-brand sm:text-sm"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    Kembali ke Berita
                </Link>

                <button
                    @click="copyLink"
                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-border/80 bg-white px-3 py-1 text-xs font-semibold text-fg-muted shadow-2xs transition-all hover:border-brand/40 hover:text-brand"
                    title="Bagikan Berita"
                >
                    <Icon :icon="Share2" :size="14" />
                    <span>Bagikan</span>
                </button>
            </div>

            <Breadcrumb
                :items="[
                    { label: 'Beranda', href: '/' },
                    { label: 'Berita', href: '/berita' },
                    { label: post.title },
                ]"
            />
        </div>

        <article class="mx-auto max-w-3xl px-4 py-6 sm:px-6">
            <header class="mb-8">
                <Link
                    v-if="post.category"
                    :href="`/berita?kategori=${post.category.slug}`"
                    class="inline-flex items-center rounded-full bg-brand-weak px-3 py-1 text-xs font-bold text-brand transition-colors hover:bg-brand hover:text-white"
                >
                    {{ post.category.name }}
                </Link>

                <h1
                    class="mt-4 text-3xl font-extrabold leading-tight tracking-tight text-fg sm:text-4xl"
                >
                    {{ post.title }}
                </h1>

                <div
                    class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-border/60 pb-6 text-sm text-fg-muted"
                >
                    <span class="inline-flex items-center gap-1.5">
                        <Icon :icon="CalendarDays" :size="15" class="text-brand" />
                        {{ formatTanggal(post.published_at) }}
                    </span>
                    <span
                        v-if="post.author"
                        class="inline-flex items-center gap-1.5"
                    >
                        <Icon :icon="UserRound" :size="15" class="text-brand" />
                        {{ post.author.name }}
                    </span>
                </div>
            </header>

            <div
                v-if="post.cover"
                class="mb-8 overflow-hidden rounded-2xl border border-border/80 bg-muted/30 shadow-xs"
            >
                <img
                    :src="post.cover.original"
                    :alt="post.title"
                    class="w-full object-cover"
                />
            </div>

            <div
                class="prose prose-base max-w-none leading-relaxed text-fg prose-headings:text-fg prose-a:text-brand prose-strong:text-fg prose-img:rounded-xl prose-ul:list-disc prose-ol:list-decimal"
                v-html="
                    post.body ||
                    '<p class=\'italic text-fg-muted\'>Belum ada isi untuk berita ini.</p>'
                "
            ></div>
        </article>

        <section
            v-if="relatedPosts && relatedPosts.length > 0"
            class="mx-auto mt-8 max-w-[90rem] border-t border-border/60 px-4 pt-10 sm:px-6 lg:px-8"
        >
            <div class="mb-6 flex items-center gap-3">
                <div
                    class="flex size-10 items-center justify-center rounded-lg bg-brand-weak text-brand"
                >
                    <Icon :icon="Newspaper" :size="20" />
                </div>
                <h2 class="text-xl font-bold text-fg sm:text-2xl">
                    Berita Lainnya
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <PostCard
                    v-for="related in relatedPosts"
                    :key="related.slug"
                    :post="related"
                />
            </div>
        </section>
    </main>
</template>
