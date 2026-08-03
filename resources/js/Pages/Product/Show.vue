<script setup lang="ts">
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, MapPin, Store, Scale, Tag, CircleCheck, CircleX } from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { Icon, Button, Breadcrumb, WhatsappIcon } from "@/Components/ui";
import { useRupiah } from "@/Composables/useRupiah";

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    product: any;
}>();

const activePhotoIndex = ref(0);

const { formatRupiah } = useRupiah();
const harga = computed(() => formatRupiah(Number(props.product.price)));

const photos = computed(() => props.product.photos || []);
const activePhoto = computed(() => photos.value[activePhotoIndex.value]?.original || photos.value[0]?.original || null);

const waLink = computed(() => {
    const phone = props.product.farmer?.phone;
    if (!phone) return null;
    
    let cleanPhone = phone.replace(/\D/g, "");
    if (cleanPhone.startsWith("0")) {
        cleanPhone = "62" + cleanPhone.substring(1);
    }
    
    const message = `Halo Bapak/Ibu ${props.product.farmer.name}, saya melihat produk *${props.product.name}* di DigiPangan Merauke dan tertarik untuk berdiskusi lebih lanjut.`;
    return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;
});
</script>

<template>
    <Head>
        <title>{{ product.seo?.title || product.name }}</title>
        <meta name="description" :content="product.seo?.description || ''" />
        <meta property="og:image" :content="product.seo?.og_image || ''" />
    </Head>

    <main class="min-h-screen bg-bg pb-20 sm:pb-12">
        <!-- Breadcrumb & Back navigation -->
        <div class="mx-auto max-w-[90rem] px-3 pt-6 pb-2 sm:px-5 lg:px-6">
            <Link
                href="/produk"
                class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-fg-muted hover:text-brand mb-4 transition-colors"
            >
                <Icon :icon="ArrowLeft" :size="16" />
                Kembali ke Katalog
            </Link>

            <Breadcrumb
                :items="[
                    { label: 'Beranda', href: '/' },
                    { label: 'Produk', href: '/produk' },
                    ...(product.category ? [{ label: product.category.name, href: `/produk?kategori=${product.category.slug}` }] : []),
                    { label: product.name }
                ]"
            />
        </div>

        <!-- Product Details Main Section -->
        <section class="mx-auto max-w-[90rem] px-3 py-6 sm:px-5 lg:px-6">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10 xl:gap-14">
                
                <!-- Left: Multi Image Gallery -->
                <div class="lg:col-span-6 space-y-3">
                    <div class="aspect-square sm:aspect-[4/3] lg:aspect-square overflow-hidden rounded-2xl bg-white shadow-xs border border-border/80 relative group">
                        <img
                            v-if="activePhoto"
                            :src="activePhoto"
                            :alt="product.name"
                            class="size-full object-cover transition-all duration-300"
                        />
                        <div v-else class="flex size-full items-center justify-center text-brand/30">
                            <Icon :icon="Tag" :size="64" />
                        </div>

                        <span
                            v-if="!product.stock_available"
                            class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-red-600/90 px-3 py-1 text-xs font-bold text-white shadow-md backdrop-blur"
                        >
                            <Icon :icon="CircleX" :size="14" /> Stok Habis
                        </span>
                    </div>

                    <!-- Multi Image Thumbnail Strip -->
                    <div v-if="photos.length > 1" class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
                        <button
                            v-for="(img, idx) in photos"
                            :key="idx"
                            @click="activePhotoIndex = idx"
                            :class="[
                                'relative size-16 sm:size-20 shrink-0 overflow-hidden rounded-xl border-2 transition-all cursor-pointer bg-white',
                                activePhotoIndex === idx
                                    ? 'border-brand ring-2 ring-brand/20 shadow-xs'
                                    : 'border-border/70 opacity-70 hover:opacity-100'
                            ]"
                        >
                            <img :src="img.thumb || img.card || img.original" :alt="`${product.name} ${idx + 1}`" class="size-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Right: Product Info & Meta Details -->
                <div class="lg:col-span-6 flex flex-col">
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span v-if="product.category" class="inline-flex items-center gap-1 rounded-full bg-brand/10 px-2.5 py-0.5 text-xs font-bold text-brand">
                                <Icon :icon="Tag" :size="12" />
                                {{ product.category.name }}
                            </span>
                            <span v-if="product.region" class="inline-flex items-center gap-1 rounded-full bg-muted px-2.5 py-0.5 text-xs font-semibold text-fg-muted">
                                <Icon :icon="MapPin" :size="12" />
                                {{ product.region.name }}
                            </span>
                        </div>

                        <h1 class="text-2xl font-extrabold tracking-tight text-fg sm:text-3xl lg:text-4xl">
                            {{ product.name }}
                        </h1>

                        <div class="mt-4 flex flex-wrap items-center gap-4">
                            <span class="text-3xl font-extrabold text-brand tabular-nums">
                                {{ harga }}
                            </span>
                            <span
                                v-if="product.stock_available"
                                class="inline-flex items-center gap-1.5 rounded-full bg-success-weak px-3 py-1 text-xs font-bold text-success"
                            >
                                <Icon :icon="CircleCheck" :size="14" /> Stok Tersedia
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 rounded-full bg-danger-weak px-3 py-1 text-xs font-bold text-danger"
                            >
                                <Icon :icon="CircleX" :size="14" /> Stok Habis
                            </span>
                        </div>
                    </div>

                    <!-- Sleek Compact Meta Grid -->
                    <div class="grid grid-cols-2 gap-3 py-4 my-2 border-y border-border/60">
                        <div v-if="product.region" class="flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-brand/10 text-brand">
                                <Icon :icon="MapPin" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-medium text-fg-muted uppercase tracking-wider">Lokasi Asal</p>
                                <p class="text-xs sm:text-sm font-bold text-fg truncate">{{ product.region.name }}</p>
                            </div>
                        </div>

                        <div v-if="product.farmer" class="flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                                <Icon :icon="Store" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-medium text-fg-muted uppercase tracking-wider">Petani / Kelompok</p>
                                <p class="text-xs sm:text-sm font-bold text-fg truncate">{{ product.farmer.name }}</p>
                            </div>
                        </div>

                        <div v-if="product.weight_value && product.unit" class="flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                <Icon :icon="Scale" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-medium text-fg-muted uppercase tracking-wider">Berat / Satuan</p>
                                <p class="text-xs sm:text-sm font-bold text-fg truncate">{{ product.weight_value }} {{ product.unit.symbol || product.unit.name }}</p>
                            </div>
                        </div>

                        <div v-if="product.category" class="flex items-center gap-2.5 rounded-xl bg-white p-2.5 border border-border/60 shadow-2xs">
                            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                                <Icon :icon="Tag" :size="14" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-medium text-fg-muted uppercase tracking-wider">Kategori</p>
                                <p class="text-xs sm:text-sm font-bold text-fg truncate">{{ product.category.name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description HTML Rendered from RichTextEditor -->
                    <div class="py-4 flex-1">
                        <h3 class="text-sm font-bold text-fg uppercase tracking-wider mb-2">Deskripsi Produk</h3>
                        <div
                            class="prose prose-sm max-w-none text-fg-muted leading-relaxed prose-headings:text-fg prose-a:text-brand prose-strong:text-fg prose-ul:list-disc prose-ol:list-decimal"
                            v-html="product.description || '<p class=\'italic text-fg-muted\'>Belum ada deskripsi untuk produk ini.</p>'"
                        >
                        </div>
                    </div>

                    <!-- Desktop & Tablet Action CTA Button -->
                    <div class="mt-6 pt-4 border-t border-border/60 hidden sm:block">
                        <a
                            v-if="waLink"
                            :href="waLink"
                            target="_blank"
                            rel="noopener noreferrer"
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
                        <div v-else class="rounded-xl bg-muted/60 p-4 text-center">
                            <p class="text-xs font-semibold text-fg-muted">Kontak penjual tidak tersedia saat ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mobile Floating Bottom CTA Bar -->
        <div v-if="waLink" class="fixed inset-x-0 bottom-0 z-40 bg-white/95 backdrop-blur-md p-3 border-t border-border/80 sm:hidden shadow-lg">
            <a
                :href="waLink"
                target="_blank"
                rel="noopener noreferrer"
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
    </main>
</template>
