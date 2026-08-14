<script setup lang="ts">
import { computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    ArrowLeft,
    MapPin,
    Tractor,
    Sprout,
    Ruler,
    ShieldCheck,
    Home,
    ShoppingBasket,
    User,
} from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import ProductCard from "@/Components/product/ProductCard.vue";
import { Icon, Breadcrumb, Button, WhatsappIcon } from "@/Components/ui";
import type { ProductCard as ProductCardType } from "@/types/home";

defineOptions({ layout: PublicLayout });

interface FarmerDetail {
    name: string;
    slug: string;
    phone: string | null;
    land_area_ha: string | null;
    photo: { original: string; thumb: string; card: string } | null;
    region: { name: string; slug: string } | null;
    village: { name: string } | null;
    farmer_group: { name: string } | null;
    commodities: { name: string; slug: string }[];
    products: ProductCardType[];
    seo: {
        title: string;
        description: string;
        canonical: string;
        og_image?: string | null;
    };
}

const props = defineProps<{ farmer: FarmerDetail }>();

const products = computed(() => props.farmer.products ?? []);
const commodities = computed(() => props.farmer.commodities ?? []);

const location = computed(() => {
    const parts = [props.farmer.village?.name, props.farmer.region?.name].filter(
        Boolean,
    );
    return parts.length ? parts.join(", ") : "Kabupaten Merauke";
});

const waLink = computed(() => {
    const phone = props.farmer.phone;
    if (!phone) return null;
    let clean = phone.replace(/\D/g, "");
    if (clean.startsWith("0")) clean = "62" + clean.substring(1);
    const message = `Halo Bapak/Ibu ${props.farmer.name}, saya menemukan profil Anda di DigiPangan Merauke dan tertarik dengan hasil panen yang Anda jual. Mohon informasinya. Terima kasih!`;
    return `https://wa.me/${clean}?text=${encodeURIComponent(message)}`;
});
</script>

<template>
    <Head>
        <title>{{ farmer.seo.title }}</title>
        <meta name="description" :content="farmer.seo.description" />
        <link rel="canonical" :href="farmer.seo.canonical" />
        <meta
            v-if="farmer.seo.og_image"
            property="og:image"
            :content="farmer.seo.og_image"
        />
    </Head>

    <main class="min-h-screen bg-bg pb-16">
        <div class="mx-auto max-w-[90rem] px-4 pt-6 pb-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <Link
                    href="/petani"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-fg-muted transition-colors hover:text-brand sm:text-sm"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    Kembali ke Direktori Petani
                </Link>
            </div>

            <Breadcrumb
                :items="[
                    { label: 'Beranda', href: '/' },
                    { label: 'Petani', href: '/petani' },
                    { label: farmer.name },
                ]"
            />
        </div>

        <section class="mx-auto max-w-[90rem] px-4 py-4 sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-3xl border border-border/80 bg-white shadow-soft"
            >
                <div
                    class="relative h-28 bg-gradient-to-br from-brand to-brand-strong sm:h-32"
                >
                    <div
                        class="absolute inset-0 opacity-20"
                        style="
                            background-image: radial-gradient(
                                rgba(255, 255, 255, 0.5) 1.5px,
                                transparent 1.5px
                            );
                            background-size: 20px 20px;
                        "
                    ></div>
                </div>

                <div class="px-5 pb-6 sm:px-8 sm:pb-8">
                    <div
                        class="flex flex-col gap-5 sm:flex-row sm:items-end sm:gap-6"
                    >
                        <div
                            class="relative z-10 -mt-14 size-28 shrink-0 overflow-hidden rounded-3xl border-4 border-white bg-brand-weak/40 shadow-lg sm:-mt-16 sm:size-32"
                        >
                            <img
                                v-if="farmer.photo"
                                :src="farmer.photo.card || farmer.photo.original"
                                :alt="`Foto ${farmer.name}`"
                                class="size-full object-cover"
                            />
                            <div
                                v-else
                                class="flex size-full items-center justify-center text-brand/40"
                                aria-hidden="true"
                            >
                                <Icon :icon="User" :size="48" />
                            </div>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h1
                                    class="text-2xl font-extrabold tracking-tight text-fg sm:text-3xl"
                                >
                                    {{ farmer.name }}
                                </h1>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full border border-green-200 bg-green-50 px-2 py-0.5 text-[11px] font-bold text-green-700"
                                >
                                    <Icon :icon="ShieldCheck" :size="12" />
                                    Terverifikasi
                                </span>
                            </div>
                            <p
                                class="mt-1.5 flex items-center gap-1.5 text-sm text-fg-muted"
                            >
                                <Icon :icon="MapPin" :size="15" class="text-brand" />
                                {{ location }}
                            </p>
                        </div>

                        <div class="sm:pb-1">
                            <a
                                v-if="waLink"
                                :href="waLink"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block"
                            >
                                <Button
                                    variant="whatsapp"
                                    size="lg"
                                    class="w-full gap-2.5 font-bold sm:w-auto"
                                >
                                    <WhatsappIcon :size="20" />
                                    <span>Hubungi Petani</span>
                                </Button>
                            </a>
                        </div>
                    </div>

                    <div
                        class="mt-6 grid grid-cols-1 gap-3 border-t border-border/60 pt-6 sm:grid-cols-3"
                    >
                        <div
                            class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/20 p-3"
                        >
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-green-100 text-brand"
                            >
                                <Icon :icon="Tractor" :size="18" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-medium uppercase tracking-wider text-fg-muted"
                                >
                                    Kelompok Tani
                                </p>
                                <p class="truncate text-sm font-bold text-fg">
                                    {{ farmer.farmer_group?.name || "—" }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/20 p-3"
                        >
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600"
                            >
                                <Icon :icon="Home" :size="18" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-medium uppercase tracking-wider text-fg-muted"
                                >
                                    Desa / Kampung
                                </p>
                                <p class="truncate text-sm font-bold text-fg">
                                    {{ farmer.village?.name || "—" }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/20 p-3"
                        >
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600"
                            >
                                <Icon :icon="Ruler" :size="18" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-medium uppercase tracking-wider text-fg-muted"
                                >
                                    Luas Lahan
                                </p>
                                <p class="truncate text-sm font-bold text-fg">
                                    {{
                                        farmer.land_area_ha
                                            ? `${farmer.land_area_ha} Ha`
                                            : "—"
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="commodities.length > 0" class="mt-5">
                        <p
                            class="mb-2 text-[11px] font-bold uppercase tracking-wider text-fg-muted"
                        >
                            Komoditas yang Dihasilkan
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="commodity in commodities"
                                :key="commodity.slug"
                                class="inline-flex items-center gap-1.5 rounded-full bg-brand/10 px-3 py-1 text-xs font-semibold text-brand"
                            >
                                <Icon :icon="Sprout" :size="12" />
                                {{ commodity.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-[90rem] px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-brand-weak text-brand"
                    >
                        <Icon :icon="ShoppingBasket" :size="20" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-fg sm:text-2xl">
                            Produk yang Dijual
                        </h2>
                        <p class="text-xs text-fg-muted sm:text-sm">
                            Hasil panen dari {{ farmer.name }}
                        </p>
                    </div>
                </div>
                <span
                    class="rounded-md bg-brand-weak px-2.5 py-1 text-sm font-semibold text-brand"
                >
                    {{ products.length }} Produk
                </span>
            </div>

            <div
                v-if="products.length > 0"
                class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <ProductCard
                    v-for="product in products"
                    :key="product.slug"
                    :product="product"
                />
            </div>

            <div
                v-else
                class="rounded-2xl border border-dashed border-border bg-white p-10 text-center"
            >
                <div
                    class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-full bg-muted text-fg-muted"
                >
                    <Icon :icon="Sprout" :size="24" />
                </div>
                <p class="text-sm text-fg-muted">
                    Petani ini belum memiliki produk yang ditampilkan.
                </p>
            </div>
        </section>
    </main>
</template>
