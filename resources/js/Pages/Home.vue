<script setup lang="ts">
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import HomeHeroSection from "@/Components/home/HomeHeroSection.vue";
import HomeFeaturedSection from "@/Components/home/HomeFeaturedSection.vue";
import HomeRegionsSection from "@/Components/home/HomeRegionsSection.vue";
import HomeLatestSection from "@/Components/home/HomeLatestSection.vue";
import HomeCtaSection from "@/Components/home/HomeCtaSection.vue";
import type { HomeProps } from "@/types/home";

defineOptions({ layout: PublicLayout });

const props = defineProps<HomeProps>();

const heroProduct = computed(
    () => props.featuredProducts[0] ?? props.latestProducts[0] ?? null,
);
const heroImage = computed(() => "/images/hero-image.webp");
const heroIsFeatured = computed(() => props.featuredProducts.length > 0);

const featuredList = computed(() =>
    props.featuredProducts.filter((p) => p.slug !== heroProduct.value?.slug),
);
const useSpotlight = computed(() => featuredList.value.length >= 3);
const spotlight = computed(() =>
    useSpotlight.value ? featuredList.value[0] : null,
);
const restFeatured = computed(() =>
    useSpotlight.value ? featuredList.value.slice(1, 4) : [],
);

const shownSlugs = computed(
    () =>
        new Set(
            [
                heroProduct.value?.slug,
                ...props.featuredProducts.map((p) => p.slug),
            ].filter(Boolean),
        ),
);
const latestList = computed(() =>
    props.latestProducts.filter((p) => !shownSlugs.value.has(p.slug)),
);
const showLatest = computed(
    () => latestList.value.length > 0 || props.latestProducts.length === 0,
);

const grain =
    "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E\")";

const categoriesPreview = computed(() => props.categories ?? []);
</script>

<template>
    <Head
        title="DigiPangan Merauke - Marketplace Komunitas Kawasan Transmigrasi"
    />

    <HomeHeroSection
        :hero-product="heroProduct"
        :hero-image="heroImage"
        :hero-is-featured="heroIsFeatured"
        :grain="grain"
    />

    <HomeFeaturedSection
        :featured-list="featuredList"
        :use-spotlight="useSpotlight"
        :spotlight="spotlight"
        :rest-featured="restFeatured"
        :categories-preview="categoriesPreview"
    />

    <HomeRegionsSection :regions="regions" :grain="grain" />

    <HomeLatestSection :show-latest="showLatest" :latest-list="latestList" />

    <HomeCtaSection />
</template>
