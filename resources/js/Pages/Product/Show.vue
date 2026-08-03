<script setup lang="ts">
import { computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, MapPin, Store, Scale, Tag, CircleCheck, CircleX, MessageCircle } from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { Icon, Button, Breadcrumb } from "@/Components/ui";
import { useRupiah } from "@/Composables/useRupiah";

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    product: any; // Using any for brevity, typically would match PublicProductDetailResource type
}>();

const { formatRupiah } = useRupiah();
const harga = computed(() => formatRupiah(Number(props.product.price)));

const waLink = computed(() => {
    const phone = props.product.farmer?.phone;
    if (!phone) return null;
    
    // clean phone number (remove leading 0 and add country code if needed)
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
        <title>{{ product.seo.title }}</title>
        <meta name="description" :content="product.seo.description" />
        <meta property="og:image" :content="product.seo.og_image" />
    </Head>

    <main class="min-h-screen bg-bg">
        <!-- Breadcrumb & Back -->
        <div class="mx-auto max-w-[90rem] px-3 pt-8 pb-4 sm:px-5 lg:px-6">
            <Link
                href="/produk"
                class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted hover:text-brand mb-6 transition-colors"
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

        <!-- Product Details -->
        <section class="mx-auto max-w-[90rem] px-3 pb-16 sm:px-5 lg:px-6">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-12 xl:gap-16">
                <!-- Left: Image Gallery (Single image for now) -->
                <div class="lg:col-span-5 xl:col-span-6">
                    <div class="aspect-square sm:aspect-[4/3] lg:aspect-square overflow-hidden rounded-card bg-muted/60 shadow-sm border border-border/80 relative">
                        <img
                            v-if="product.photos && product.photos.length > 0"
                            :src="product.photos[0].original"
                            :alt="product.name"
                            class="size-full object-cover"
                        />
                        <div v-else class="flex size-full items-center justify-center text-brand/40">
                            <Icon :icon="Tag" :size="64" />
                        </div>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="lg:col-span-7 xl:col-span-6">
                    <div class="mb-6">
                        <h1 class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl">
                            {{ product.name }}
                        </h1>
                        <div class="mt-4 flex items-center gap-4">
                            <span class="text-3xl font-bold text-brand tabular-nums">
                                {{ harga }}
                            </span>
                            <span
                                v-if="product.stock_available"
                                class="inline-flex items-center gap-1.5 rounded-full bg-success-weak px-3 py-1 text-sm font-semibold text-success"
                            >
                                <Icon :icon="CircleCheck" :size="16" /> Stok Tersedia
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 rounded-full bg-danger-weak px-3 py-1 text-sm font-semibold text-danger"
                            >
                                <Icon :icon="CircleX" :size="16" /> Stok Habis
                            </span>
                        </div>
                    </div>

                    <!-- Meta Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-6 border-y border-border/60">
                        <div v-if="product.region" class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-weak text-brand">
                                <Icon :icon="MapPin" :size="20" />
                            </div>
                            <div>
                                <p class="text-sm text-fg-muted">Lokasi Asal</p>
                                <p class="font-medium text-fg">{{ product.region.name }}</p>
                            </div>
                        </div>

                        <div v-if="product.farmer" class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <Icon :icon="Store" :size="20" />
                            </div>
                            <div>
                                <p class="text-sm text-fg-muted">Petani / Kelompok</p>
                                <p class="font-medium text-fg">{{ product.farmer.name }}</p>
                            </div>
                        </div>

                        <div v-if="product.weight_value && product.unit" class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <Icon :icon="Scale" :size="20" />
                            </div>
                            <div>
                                <p class="text-sm text-fg-muted">Berat / Satuan</p>
                                <p class="font-medium text-fg">{{ product.weight_value }} {{ product.unit.name }}</p>
                            </div>
                        </div>

                        <div v-if="product.category" class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                <Icon :icon="Tag" :size="20" />
                            </div>
                            <div>
                                <p class="text-sm text-fg-muted">Kategori</p>
                                <p class="font-medium text-fg">{{ product.category.name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="py-6">
                        <h3 class="text-lg font-bold text-fg mb-3">Deskripsi Produk</h3>
                        <div class="prose prose-sm sm:prose-base prose-brand text-fg-muted" v-html="product.description || 'Belum ada deskripsi untuk produk ini.'">
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="mt-4 pt-6 border-t border-border/60">
                        <a v-if="waLink" :href="waLink" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-block">
                            <Button size="xl" class="w-full sm:w-auto gap-2 bg-[#25D366] hover:bg-[#128C7E] text-white">
                                <Icon :icon="MessageCircle" :size="20" />
                                Hubungi Penjual via WhatsApp
                            </Button>
                        </a>
                        <div v-else class="rounded-card bg-muted p-4 text-center">
                            <p class="text-sm text-fg-muted">Kontak penjual tidak tersedia saat ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>
