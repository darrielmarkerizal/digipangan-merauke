<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import ProductCard from "@/Components/product/ProductCard.vue";
import FarmerCard from "@/Components/farmer/FarmerCard.vue";
import { Icon, Breadcrumb } from "@/Components/ui";
import {
    MapPin,
    ArrowLeft,
    Users,
    Sprout,
    Building,
    ChevronRight,
    Tractor,
    Image as ImageIcon,
} from "@lucide/vue";
import { useRupiah } from "@/Composables/useRupiah";
import type {
    ProductCard as ProductCardType,
    FarmerCard as FarmerCardType,
} from "@/types/home";

interface RegionDetail {
    name: string;
    slug: string;
    description: string;
    agricultural_potential: string;
    area_km2: string;
    population: number;
    villages_count: number;
    farmer_groups_count: number;
    products_count: number;
    cover: { original: string; card: string } | null;
    gallery: { thumb: string; card: string }[];
    featured_products: ProductCardType[];
    farmers: FarmerCardType[];
    seo: {
        title: string;
        description: string;
        canonical: string;
        og_image?: string;
    };
}

const props = defineProps<{
    region: RegionDetail;
}>();

const { formatAngka } = useRupiah();

defineOptions({ layout: PublicLayout });
</script>

<template>
    <Head>
        <title>{{ region.seo.title }}</title>
        <meta name="description" :content="region.seo.description" />
        <link rel="canonical" :href="region.seo.canonical" />
        <meta
            property="og:image"
            :content="region.seo.og_image"
            v-if="region.seo.og_image"
        />
    </Head>

    <main class="min-h-screen bg-bg pb-24 sm:pb-16">

        <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8 pt-6 pb-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <Link
                    href="/wilayah"
                    class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-fg-muted hover:text-brand transition-colors"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    Kembali ke Daftar Wilayah
                </Link>
            </div>

            <Breadcrumb
                :items="[
                    { label: 'Beranda', href: '/' },
                    { label: 'Wilayah', href: '/wilayah' },
                    { label: region.name },
                ]"
            />
        </div>


        <div class="relative bg-brand-weak/20">
            <div class="absolute inset-0 z-0">
                <img
                    v-if="region.cover"
                    :src="region.cover.original"
                    :alt="`Pemandangan ${region.name}`"
                    class="w-full h-full object-cover opacity-20"
                />
                <div
                    v-else
                    class="w-full h-full bg-brand-weak/30"
                    style="background-image: radial-gradient(rgba(10, 110, 60, 0.15) 1.5px, transparent 1.5px); background-size: 24px 24px;"
                ></div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-bg via-bg/80 to-transparent"
                ></div>
            </div>

            <div
                class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8 relative z-10 py-16 lg:pt-20 lg:pb-32"
            >
                <div class="max-w-3xl">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-weak text-brand text-sm font-semibold mb-6"
                    >
                        <Icon :icon="MapPin" :size="16" />
                        Profil Kawasan Transmigrasi
                    </div>
                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-brand-strong mb-6"
                    >
                        Distrik {{ region.name }}
                    </h1>
                    <p
                        class="text-lg text-fg-muted leading-relaxed whitespace-pre-wrap"
                    >
                        {{
                            region.description || "Belum ada deskripsi wilayah."
                        }}
                    </p>
                </div>
            </div>
        </div>


        <div
            class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8 pt-0 pb-12 -mt-20 relative z-20"
        >
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">

                <div
                    class="bg-white/80 backdrop-blur-md border border-border/80 rounded-2xl p-6 shadow-soft transition-transform hover:-translate-y-1"
                >
                    <div
                        class="size-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-4"
                    >
                        <Icon :icon="MapPin" :size="24" />
                    </div>
                    <dt class="text-sm font-medium text-fg-muted mb-1">
                        Luas Wilayah
                    </dt>
                    <dd class="text-3xl lg:text-4xl font-black text-fg tracking-tight mt-1">
                        {{ region.area_km2 }}
                        <span class="text-base font-semibold text-fg-muted"
                            >km²</span
                        >
                    </dd>
                </div>


                <div
                    class="bg-white/80 backdrop-blur-md border border-border/80 rounded-2xl p-6 shadow-soft transition-transform hover:-translate-y-1"
                >
                    <div
                        class="size-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4"
                    >
                        <Icon :icon="Users" :size="24" />
                    </div>
                    <dt class="text-sm font-medium text-fg-muted mb-1">
                        Populasi
                    </dt>
                    <dd class="text-3xl lg:text-4xl font-black text-fg tracking-tight mt-1">
                        {{ formatAngka(region.population) }}
                        <span class="text-base font-semibold text-fg-muted"
                            >jiwa</span
                        >
                    </dd>
                </div>


                <div
                    class="bg-white/80 backdrop-blur-md border border-border/80 rounded-2xl p-6 shadow-soft transition-transform hover:-translate-y-1"
                >
                    <div
                        class="size-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-4"
                    >
                        <Icon :icon="Building" :size="24" />
                    </div>
                    <dt class="text-sm font-medium text-fg-muted mb-1">
                        Jumlah Desa/Kampung
                    </dt>
                    <dd class="text-3xl lg:text-4xl font-black text-fg tracking-tight mt-1">
                        {{ formatAngka(region.villages_count) }}
                    </dd>
                </div>


                <div
                    class="bg-white/80 backdrop-blur-md border border-border/80 rounded-2xl p-6 shadow-soft transition-transform hover:-translate-y-1"
                >
                    <div
                        class="size-12 rounded-xl bg-green-100 text-brand flex items-center justify-center mb-4"
                    >
                        <Icon :icon="Tractor" :size="24" />
                    </div>
                    <dt class="text-sm font-medium text-fg-muted mb-1">
                        Kelompok Tani
                    </dt>
                    <dd class="text-3xl lg:text-4xl font-black text-fg tracking-tight mt-1">
                        {{ formatAngka(region.farmer_groups_count) }}
                    </dd>
                </div>
            </div>
        </div>

        <div
            class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8 py-8 lg:py-16 grid grid-cols-1 lg:grid-cols-12 gap-12"
        >

            <div class="lg:col-span-8 space-y-16">

                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="size-10 rounded-lg bg-brand-weak text-brand flex items-center justify-center"
                        >
                            <Icon :icon="Sprout" :size="20" />
                        </div>
                        <h2 class="text-2xl font-bold text-fg">
                            Potensi Pertanian
                        </h2>
                    </div>
                    <div
                        class="prose prose-brand max-w-none text-fg-muted bg-white rounded-3xl p-6 sm:p-10 shadow-soft border-0 ring-1 ring-fg/5 whitespace-pre-wrap"
                    >
                        {{
                            region.agricultural_potential ||
                            "Belum ada data potensi pertanian."
                        }}
                    </div>
                </section>


                <section>
                    <div class="flex items-center justify-between gap-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="size-10 rounded-lg bg-brand-weak text-brand flex items-center justify-center"
                            >
                                <Icon :icon="Users" :size="20" />
                            </div>
                            <h2 class="text-2xl font-bold text-fg">
                                Petani di Wilayah Ini
                            </h2>
                        </div>
                        <span
                            v-if="region.farmers.length > 0"
                            class="text-sm font-semibold bg-brand-weak text-brand px-2.5 py-1 rounded-md"
                        >
                            {{ region.farmers.length }} Petani
                        </span>
                    </div>

                    <div
                        v-if="region.farmers.length > 0"
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                    >
                        <FarmerCard
                            v-for="farmer in region.farmers"
                            :key="farmer.slug"
                            :farmer="farmer"
                        />
                    </div>

                    <div
                        v-else
                        class="bg-muted/20 border border-dashed border-border rounded-xl p-8 text-center"
                    >
                        <div
                            class="inline-flex size-12 rounded-full bg-white shadow-xs items-center justify-center text-fg-muted mb-3"
                        >
                            <Icon :icon="Users" :size="24" />
                        </div>
                        <p class="text-sm text-fg-muted">
                            Belum ada petani terdaftar untuk wilayah ini.
                        </p>
                    </div>
                </section>

                <section v-if="region.gallery.length > 0">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="size-10 rounded-lg bg-brand-weak text-brand flex items-center justify-center"
                        >
                            <Icon :icon="ImageIcon" :size="20" />
                        </div>
                        <h2 class="text-2xl font-bold text-fg">
                            Galeri Kawasan
                        </h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div
                            v-for="(image, index) in region.gallery"
                            :key="index"
                            class="group relative aspect-square rounded-xl overflow-hidden bg-muted/30 cursor-pointer"
                        >
                            <img
                                :src="image.thumb"
                                :alt="`Galeri ${region.name} ${index + 1}`"
                                loading="lazy"
                                class="size-full object-cover transition-transform duration-500 group-hover:scale-110"
                            />
                            <div
                                class="absolute inset-0 bg-black/0 transition-colors duration-300 group-hover:bg-black/10"
                            ></div>
                        </div>
                    </div>
                </section>
            </div>


            <div class="lg:col-span-4">
                <div class="sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-fg">
                            Produk Unggulan
                        </h2>
                        <span
                            class="text-sm font-semibold bg-brand-weak text-brand px-2.5 py-1 rounded-md"
                        >
                            {{ region.featured_products.length }} Produk
                        </span>
                    </div>

                    <div
                        v-if="region.featured_products.length > 0"
                        class="flex flex-col gap-4"
                    >
                        <ProductCard
                            v-for="product in region.featured_products"
                            :key="product.slug"
                            :product="product"
                        />
                    </div>

                    <div
                        v-else
                        class="bg-muted/20 border border-dashed border-border rounded-xl p-8 text-center"
                    >
                        <div
                            class="inline-flex size-12 rounded-full bg-white shadow-xs items-center justify-center text-fg-muted mb-3"
                        >
                            <Icon :icon="Sprout" :size="24" />
                        </div>
                        <p class="text-sm text-fg-muted">
                            Belum ada produk unggulan yang disorot untuk wilayah
                            ini.
                        </p>
                    </div>

                    <div class="mt-8">
                        <Link
                            :href="`/produk?region=${region.slug}`"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-white border border-border/80 px-4 py-3 text-sm font-semibold text-fg shadow-xs transition-colors hover:bg-muted/30 hover:text-brand"
                        >
                            Lihat Semua Produk Wilayah Ini
                            <Icon :icon="ChevronRight" :size="16" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
