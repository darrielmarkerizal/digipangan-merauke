<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    ArrowLeft,
    MapPin,
    Store,
    Scale,
    Tag,
    CircleCheck,
    CircleX,
    Share2,
    Minus,
    Plus,
    Maximize2,
    ChevronLeft,
    ChevronRight,
    X,
    ShieldCheck,
    Sprout,
    User,
} from "@lucide/vue";
import { toast } from "vue-sonner";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import ProductCard from "@/Components/product/ProductCard.vue";
import { Icon, Button, Breadcrumb, WhatsappIcon } from "@/Components/ui";
import { useRupiah } from "@/Composables/useRupiah";
import type { ProductCard as ProductCardType } from "@/types/home";

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    product: any;
    relatedProducts?: ProductCardType[];
}>();

const activePhotoIndex = ref(0);
const isLightboxOpen = ref(false);
const isDescriptionModalOpen = ref(false);
const quantity = ref(1);

const openDescriptionModal = () => {
    isDescriptionModalOpen.value = true;
};

const closeDescriptionModal = () => {
    isDescriptionModalOpen.value = false;
};

const { formatRupiah } = useRupiah();
const unitName = computed(
    () => props.product.unit?.symbol || props.product.unit?.name || "satuan",
);
const unitPrice = computed(() => Number(props.product.price) || 0);

const harga = computed(() => formatRupiah(unitPrice.value));
const totalEstimatedPrice = computed(() =>
    formatRupiah(unitPrice.value * quantity.value),
);

const photos = computed(() => props.product.photos || []);
const activePhoto = computed(
    () =>
        photos.value[activePhotoIndex.value]?.original ||
        photos.value[0]?.original ||
        null,
);

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

const increaseQuantity = () => {
    quantity.value++;
};

const waLink = computed(() => {
    const phone = props.product.farmer?.phone;
    if (!phone) return null;

    let cleanPhone = phone.replace(/\D/g, "");
    if (cleanPhone.startsWith("0")) {
        cleanPhone = "62" + cleanPhone.substring(1);
    }

    const qtyText = `${quantity.value} ${unitName.value}`;
    const totalText = totalEstimatedPrice.value;
    const message = `Halo Bapak/Ibu ${props.product.farmer.name}, saya melihat produk *${props.product.name}* di DigiPangan Merauke dan tertarik memesan sebanyak *${qtyText}* (Estimasi Total: ${totalText}). Mohon info ketersediaan stok & proses pengirimannya. Terima kasih!`;
    return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;
});

const copyLink = () => {
    if (navigator.clipboard && window.location.href) {
        navigator.clipboard.writeText(window.location.href);
        toast.success("Tautan produk berhasil disalin!");
    }
};

const openLightbox = (index: number) => {
    activePhotoIndex.value = index;
    isLightboxOpen.value = true;
};

const closeLightbox = () => {
    isLightboxOpen.value = false;
};

const nextPhoto = () => {
    if (photos.value.length > 0) {
        activePhotoIndex.value =
            (activePhotoIndex.value + 1) % photos.value.length;
    }
};

const prevPhoto = () => {
    if (photos.value.length > 0) {
        activePhotoIndex.value =
            (activePhotoIndex.value - 1 + photos.value.length) %
            photos.value.length;
    }
};

const trackContact = () => {
    try {
        fetch(`/api/v1/products/${props.product.slug}/contact`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    (
                        document.querySelector(
                            'meta[name="csrf-token"]',
                        ) as HTMLMetaElement
                    )?.content || "",
                Accept: "application/json",
            },
        }).catch(() => {});
    } catch (e) {}
};

const trackView = () => {
    try {
        fetch(`/api/v1/products/${props.product.slug}/view`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    (
                        document.querySelector(
                            'meta[name="csrf-token"]',
                        ) as HTMLMetaElement
                    )?.content || "",
                Accept: "application/json",
            },
        }).catch(() => {});
    } catch (e) {}
};

onMounted(() => {
    trackView();
});
</script>

<template>
    <Head>
        <title>{{ product.seo?.title || product.name }}</title>
        <meta name="description" :content="product.seo?.description || ''" />
        <meta property="og:image" :content="product.seo?.og_image || ''" />
    </Head>

    <main class="min-h-screen bg-bg pb-24 sm:pb-16">
        <div class="mx-auto max-w-[90rem] px-3 pt-6 pb-2 sm:px-5 lg:px-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <Link
                    href="/produk"
                    class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-fg-muted hover:text-brand transition-colors"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    Kembali ke Katalog
                </Link>

                <button
                    @click="copyLink"
                    class="inline-flex items-center gap-1.5 rounded-full border border-border/80 bg-white px-3 py-1 text-xs font-semibold text-fg-muted shadow-2xs hover:text-brand hover:border-brand/40 transition-all cursor-pointer"
                    title="Bagikan Produk"
                >
                    <Icon :icon="Share2" :size="14" />
                    <span>Bagikan</span>
                </button>
            </div>

            <Breadcrumb
                :items="[
                    { label: 'Beranda', href: '/' },
                    { label: 'Produk', href: '/produk' },
                    ...(product.category
                        ? [
                              {
                                  label: product.category.name,
                                  href: `/produk?kategori=${product.category.slug}`,
                              },
                          ]
                        : []),
                    { label: product.name },
                ]"
            />
        </div>

        <section class="mx-auto max-w-[90rem] px-3 py-6 sm:px-5 lg:px-6">
            <div
                class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10 xl:gap-14"
            >
                <div class="lg:col-span-6 space-y-3">
                    <div
                        @click="
                            photos.length > 0 && openLightbox(activePhotoIndex)
                        "
                        :class="[
                            'aspect-square sm:aspect-[4/3] lg:aspect-square overflow-hidden rounded-2xl bg-white shadow-xs border border-border/80 relative group',
                            photos.length > 0 ? 'cursor-zoom-in' : '',
                        ]"
                    >
                        <img
                            v-if="activePhoto"
                            :src="activePhoto"
                            :alt="product.name"
                            class="size-full object-cover transition-all duration-300 group-hover:scale-103"
                        />
                        <div
                            v-else
                            class="flex size-full items-center justify-center text-brand/30"
                        >
                            <Icon :icon="Tag" :size="64" />
                        </div>

                        <span
                            v-if="!product.stock_available"
                            class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-red-600/90 px-3 py-1 text-xs font-bold text-white shadow-md backdrop-blur"
                        >
                            <Icon :icon="CircleX" :size="14" /> Stok Habis
                        </span>

                        <button
                            v-if="photos.length > 0"
                            class="absolute right-4 bottom-4 size-9 rounded-full bg-black/50 text-white backdrop-blur flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Perbesar Foto"
                        >
                            <Icon :icon="Maximize2" :size="16" />
                        </button>
                    </div>

                    <div
                        v-if="photos.length > 1"
                        class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none"
                    >
                        <button
                            v-for="(img, idx) in photos"
                            :key="idx"
                            @click="activePhotoIndex = Number(idx)"
                            :class="[
                                'relative size-16 sm:size-20 shrink-0 overflow-hidden rounded-xl border-2 transition-all cursor-pointer bg-white',
                                activePhotoIndex === Number(idx)
                                    ? 'border-brand ring-2 ring-brand/20 shadow-xs'
                                    : 'border-border/70 opacity-70 hover:opacity-100',
                            ]"
                        >
                            <img
                                :src="img.thumb || img.card || img.original"
                                :alt="`${product.name} ${Number(idx) + 1}`"
                                class="size-full object-cover"
                            />
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-6 flex flex-col">
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                v-if="product.category"
                                class="inline-flex items-center gap-1 rounded-full bg-brand/10 px-2.5 py-0.5 text-xs font-bold text-brand"
                            >
                                <Icon :icon="Tag" :size="12" />
                                {{ product.category.name }}
                            </span>
                            <span
                                v-if="product.region"
                                class="inline-flex items-center gap-1 rounded-full bg-muted px-2.5 py-0.5 text-xs font-semibold text-fg-muted"
                            >
                                <Icon :icon="MapPin" :size="12" />
                                {{ product.region.name }}
                            </span>
                        </div>

                        <h1
                            class="text-2xl font-extrabold tracking-tight text-fg sm:text-3xl lg:text-4xl"
                        >
                            {{ product.name }}
                        </h1>

                        <div class="mt-4 flex flex-wrap items-center gap-4">
                            <div class="flex items-baseline gap-1">
                                <span
                                    class="text-3xl font-extrabold text-brand tabular-nums"
                                >
                                    {{ harga }}
                                </span>
                                <span
                                    class="text-xs font-semibold text-fg-muted"
                                >
                                    / {{ unitName }}
                                </span>
                            </div>

                            <span
                                v-if="product.stock_available"
                                class="inline-flex items-center gap-1.5 rounded-full bg-success-weak px-3 py-1 text-xs font-bold text-success"
                            >
                                <Icon :icon="CircleCheck" :size="14" /> Stok
                                Tersedia
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 rounded-full bg-danger-weak px-3 py-1 text-xs font-bold text-danger"
                            >
                                <Icon :icon="CircleX" :size="14" /> Stok Habis
                            </span>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-3 py-4 my-2 border-y border-border/60"
                    >
                        <div
                            v-if="product.region"
                            class="flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs"
                        >
                            <div
                                class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-brand/10 text-brand"
                            >
                                <Icon :icon="MapPin" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-medium text-fg-muted uppercase tracking-wider"
                                >
                                    Lokasi Asal
                                </p>
                                <p
                                    class="text-xs sm:text-sm font-bold text-fg truncate"
                                >
                                    {{ product.region.name }}
                                </p>
                            </div>
                        </div>

                        <component
                            :is="product.farmer?.slug ? Link : 'div'"
                            v-if="product.farmer"
                            :href="
                                product.farmer?.slug
                                    ? `/petani/${product.farmer.slug}`
                                    : undefined
                            "
                            :class="[
                                'flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs transition-colors',
                                product.farmer?.slug
                                    ? 'hover:border-brand/40 hover:bg-brand/5 cursor-pointer'
                                    : '',
                            ]"
                        >
                            <div
                                class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600"
                            >
                                <Icon :icon="Store" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-medium text-fg-muted uppercase tracking-wider"
                                >
                                    Petani / Kelompok
                                </p>
                                <p
                                    class="text-xs sm:text-sm font-bold text-fg truncate"
                                >
                                    {{ product.farmer.name }}
                                </p>
                            </div>
                        </component>

                        <div
                            v-if="product.weight_value && product.unit"
                            class="flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs"
                        >
                            <div
                                class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600"
                            >
                                <Icon :icon="Scale" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-medium text-fg-muted uppercase tracking-wider"
                                >
                                    Berat / Satuan
                                </p>
                                <p
                                    class="text-xs sm:text-sm font-bold text-fg truncate"
                                >
                                    {{ product.weight_value }}
                                    {{
                                        product.unit.symbol || product.unit.name
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="product.category"
                            class="flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs"
                        >
                            <div
                                class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600"
                            >
                                <Icon :icon="Tag" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-medium text-fg-muted uppercase tracking-wider"
                                >
                                    Kategori
                                </p>
                                <p
                                    class="text-xs sm:text-sm font-bold text-fg truncate"
                                >
                                    {{ product.category.name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="product.stock_available" class="py-4 space-y-3">
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-fg-muted"
                        >
                            Jumlah Pembelian & Estimasi
                        </label>

                        <div
                            class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-border/80 bg-white p-3 shadow-2xs"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex items-center rounded-lg border border-border/80 bg-muted/30 p-1"
                                >
                                    <button
                                        @click="decreaseQuantity"
                                        :disabled="quantity <= 1"
                                        class="flex size-8 items-center justify-center rounded-md bg-white text-fg shadow-2xs hover:bg-muted disabled:opacity-40 transition-colors cursor-pointer"
                                        title="Kurangi"
                                    >
                                        <Icon :icon="Minus" :size="14" />
                                    </button>
                                    <input
                                        type="number"
                                        v-model.number="quantity"
                                        min="1"
                                        class="w-12 text-center text-sm font-bold text-fg focus:outline-none bg-transparent"
                                    />
                                    <button
                                        @click="increaseQuantity"
                                        class="flex size-8 items-center justify-center rounded-md bg-white text-fg shadow-2xs hover:bg-muted transition-colors cursor-pointer"
                                        title="Tambah"
                                    >
                                        <Icon :icon="Plus" :size="14" />
                                    </button>
                                </div>
                                <span
                                    class="text-xs font-semibold text-fg-muted"
                                >
                                    {{ unitName }}
                                </span>
                            </div>

                            <div class="text-right">
                                <p
                                    class="text-[11px] font-medium text-fg-muted"
                                >
                                    Estimasi Total
                                </p>
                                <p
                                    class="text-lg font-bold text-brand tabular-nums"
                                >
                                    {{ totalEstimatedPrice }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="py-4 flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h3
                                class="text-xs font-bold text-fg uppercase tracking-wider"
                            >
                                Deskripsi Produk
                            </h3>
                            <button
                                v-if="product.description"
                                @click="openDescriptionModal"
                                class="text-xs font-bold text-brand hover:underline flex items-center gap-1 cursor-pointer"
                            >
                                <span>Lihat Selengkapnya</span>
                                <Icon :icon="ChevronRight" :size="14" />
                            </button>
                        </div>

                        <div
                            class="relative group cursor-pointer rounded-xl border border-transparent p-1 transition-all hover:border-brand/20 hover:bg-muted/20"
                            @click="openDescriptionModal"
                        >
                            <div
                                class="prose prose-sm max-w-none text-fg-muted leading-relaxed line-clamp-3 prose-headings:text-fg prose-a:text-brand prose-strong:text-fg"
                                v-html="
                                    product.description ||
                                    '<p class=\'italic text-fg-muted\'>Belum ada deskripsi untuk produk ini.</p>'
                                "
                            ></div>

                            <div
                                v-if="product.description"
                                class="mt-1.5 flex items-center gap-1 text-xs font-bold text-brand group-hover:underline"
                            >
                                <span>Baca deskripsi selengkapnya &rarr;</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-6 pt-4 border-t border-border/60 hidden sm:block"
                    >
                        <a
                            v-if="waLink"
                            :href="waLink"
                            target="_blank"
                            rel="noopener noreferrer"
                            @click="trackContact"
                            class="inline-block w-full sm:w-auto"
                        >
                            <Button
                                variant="whatsapp"
                                size="lg"
                                class="w-full sm:w-auto px-6 py-3 text-sm sm:text-base font-bold shadow-md transition-all flex items-center justify-center gap-2.5 cursor-pointer"
                            >
                                <WhatsappIcon :size="20" />
                                <span>Hubungi Penjual via WhatsApp</span>
                            </Button>
                        </a>
                        <div
                            v-else
                            class="rounded-xl bg-muted/60 p-4 text-center"
                        >
                            <p class="text-xs font-semibold text-fg-muted">
                                Kontak penjual tidak tersedia saat ini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            v-if="product.farmer"
            class="mx-auto max-w-[90rem] px-3 py-4 sm:px-5 lg:px-6"
        >
            <div
                class="rounded-2xl border border-border/80 bg-white p-5 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand font-bold text-lg border border-brand/20"
                    >
                        <Icon :icon="Sprout" :size="24" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <component
                                :is="product.farmer?.slug ? Link : 'h3'"
                                :href="
                                    product.farmer?.slug
                                        ? `/petani/${product.farmer.slug}`
                                        : undefined
                                "
                                :class="[
                                    'text-base font-bold text-fg',
                                    product.farmer?.slug
                                        ? 'hover:text-brand hover:underline transition-colors cursor-pointer'
                                        : '',
                                ]"
                            >
                                {{ product.farmer.name }}
                            </component>
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-green-50 border border-green-200 px-2 py-0.5 text-[11px] font-bold text-green-700"
                            >
                                <Icon :icon="ShieldCheck" :size="12" />
                                Terverifikasi
                            </span>
                        </div>
                        <p class="text-xs text-fg-muted mt-0.5">
                            Petani Lokal Transmigrasi —
                            {{ product.region?.name || "Kabupaten Merauke" }}
                        </p>
                    </div>
                </div>

                <Link
                    v-if="product.farmer?.slug"
                    :href="`/petani/${product.farmer.slug}`"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-border/80 bg-muted/30 px-4 py-2 text-xs font-bold text-fg hover:bg-brand hover:text-white transition-all cursor-pointer"
                >
                    <Icon :icon="User" :size="14" />
                    <span>Lihat Profil Petani</span>
                </Link>
                <Link
                    v-else-if="product.region"
                    :href="`/produk?region=${product.region.slug}`"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-border/80 bg-muted/30 px-4 py-2 text-xs font-bold text-fg hover:bg-brand hover:text-white transition-all cursor-pointer"
                >
                    <span>Lihat Semua Komoditas Distrik Ini</span>
                </Link>
            </div>
        </section>

        <section
            v-if="relatedProducts && relatedProducts.length > 0"
            class="mx-auto max-w-[90rem] px-3 py-8 sm:px-5 lg:px-6 border-t border-border/60 mt-8"
        >
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-fg">
                        Komoditas Sejenis
                    </h2>
                    <p class="text-xs sm:text-sm text-fg-muted mt-1">
                        Pilihan hasil pertanian lainnya dari kawasan unggulan
                        yang sama.
                    </p>
                </div>
                <Link
                    href="/produk"
                    class="text-xs sm:text-sm font-bold text-brand hover:underline"
                >
                    Lihat Semua Katalog &rarr;
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard
                    v-for="relProd in relatedProducts"
                    :key="relProd.slug"
                    :product="relProd"
                />
            </div>
        </section>

        <div
            v-if="waLink"
            class="fixed inset-x-0 bottom-0 z-40 bg-white/95 backdrop-blur-md p-3 border-t border-border/80 sm:hidden shadow-lg"
        >
            <div class="flex items-center justify-between gap-3 mb-2 px-1">
                <div class="text-xs text-fg-muted font-medium">
                    Jml:
                    <span class="font-bold text-fg"
                        >{{ quantity }} {{ unitName }}</span
                    >
                </div>
                <div class="text-xs font-bold text-brand">
                    Est: {{ totalEstimatedPrice }}
                </div>
            </div>
            <a
                :href="waLink"
                target="_blank"
                rel="noopener noreferrer"
                @click="trackContact"
                class="block w-full"
            >
                <Button
                    variant="whatsapp"
                    size="md"
                    class="w-full text-sm font-bold flex items-center justify-center gap-2 py-3 cursor-pointer"
                >
                    <WhatsappIcon :size="18" />
                    <span>Hubungi Penjual via WhatsApp</span>
                </Button>
            </a>
        </div>

        <div
            v-if="isLightboxOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
            @click.self="closeLightbox"
        >
            <button
                @click="closeLightbox"
                class="absolute right-4 top-4 z-10 size-10 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center transition-colors cursor-pointer"
                title="Tutup"
            >
                <Icon :icon="X" :size="20" />
            </button>

            <button
                v-if="photos.length > 1"
                @click="prevPhoto"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-10 size-11 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center transition-colors cursor-pointer"
                title="Sebelumnya"
            >
                <Icon :icon="ChevronLeft" :size="24" />
            </button>

            <div class="max-w-4xl max-h-[85vh] overflow-hidden rounded-2xl">
                <img
                    :src="activePhoto"
                    :alt="product.name"
                    class="max-h-[80vh] w-auto object-contain mx-auto rounded-xl"
                />
            </div>

            <button
                v-if="photos.length > 1"
                @click="nextPhoto"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 size-11 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center transition-colors cursor-pointer"
                title="Berikutnya"
            >
                <Icon :icon="ChevronRight" :size="24" />
            </button>
        </div>

        <div
            v-if="isDescriptionModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="closeDescriptionModal"
        >
            <div
                class="w-full max-w-2xl max-h-[85vh] overflow-hidden rounded-2xl bg-white shadow-2xl flex flex-col animate-in fade-in zoom-in-95 duration-200"
            >
                <div
                    class="flex items-center justify-between border-b border-border/80 px-6 py-4 bg-muted/20"
                >
                    <div>
                        <h3 class="text-base font-extrabold text-fg">
                            Deskripsi Lengkap Produk
                        </h3>
                        <p class="text-xs text-fg-muted font-medium">
                            {{ product.name }}
                        </p>
                    </div>
                    <button
                        @click="closeDescriptionModal"
                        class="size-8 rounded-full bg-white text-fg-muted hover:text-fg border border-border/80 flex items-center justify-center shadow-2xs transition-colors cursor-pointer"
                        title="Tutup"
                    >
                        <Icon :icon="X" :size="16" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    <div
                        class="prose prose-sm sm:prose-base max-w-none text-fg-muted leading-relaxed prose-headings:text-fg prose-a:text-brand prose-strong:text-fg prose-ul:list-disc prose-ol:list-decimal"
                        v-html="
                            product.description ||
                            '<p class=\'italic text-fg-muted\'>Belum ada deskripsi untuk produk ini.</p>'
                        "
                    ></div>
                </div>

                <div
                    class="border-t border-border/80 px-6 py-3 bg-muted/20 text-right"
                >
                    <Button
                        size="sm"
                        variant="secondary"
                        @click="closeDescriptionModal"
                    >
                        Tutup
                    </Button>
                </div>
            </div>
        </div>
    </main>
</template>
