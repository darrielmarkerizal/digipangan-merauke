<script setup lang="ts">
import { ref } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import {
    House,
    ShoppingBasket,
    MapPin,
    Users,
    Newspaper,
    Info,
    Sprout,
    Menu,
    X,
} from "@lucide/vue";
import Icon from "@/Components/ui/Icon.vue";
import { Toaster } from "vue-sonner";
import { useFlashToast } from "@/Composables/useFlashToast";

const page = usePage();
useFlashToast();

const isMobileMenuOpen = ref(false);

const navItems = [
    { href: "/", label: "Beranda", icon: House },
    { href: "/produk", label: "Produk", icon: ShoppingBasket },
    { href: "/wilayah", label: "Wilayah", icon: MapPin },
    { href: "/petani", label: "Petani", icon: Users },
    { href: "/berita", label: "Berita", icon: Newspaper },
    { href: "/tentang", label: "Tentang", icon: Info },
];

const isActive = (href: string) =>
    href === "/" ? page.url === "/" : page.url.startsWith(href);
</script>

<template>
    <Toaster position="top-center" richColors />
    <div class="flex min-h-[100dvh] flex-col overflow-x-hidden">
        <header class="fixed inset-x-0 top-0 z-40 px-3 sm:px-4 pt-3">
            <div
                class="mx-auto flex h-14 sm:h-16 max-w-7xl items-center justify-between gap-2 sm:gap-4 rounded-full bg-card/85 pl-3 pr-3 sm:pl-4 sm:pr-4 shadow-[0_10px_40px_-12px_rgba(20,40,31,0.18)] ring-1 ring-fg/5 backdrop-blur-xl transition-all"
            >
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <Link
                        href="/"
                        class="flex items-center gap-2 sm:gap-2.5 min-w-0"
                    >
                        <span
                            class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand to-brand-strong text-on-brand shadow-sm shadow-brand/30"
                        >
                            <Icon :icon="Sprout" :size="18" />
                        </span>
                        <span
                            class="truncate text-base sm:text-lg font-bold tracking-tight text-fg"
                        >
                            DigiPangan<span class="font-medium text-fg-muted">
                                Merauke</span
                            >
                        </span>
                    </Link>
                </div>

                <nav class="hidden items-center gap-0.5 lg:flex">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-full px-3.5 py-1.5 text-sm font-medium transition-colors duration-200"
                        :class="
                            isActive(item.href)
                                ? 'bg-brand-weak text-brand'
                                : 'text-fg-muted hover:bg-muted hover:text-fg'
                        "
                        :aria-current="isActive(item.href) ? 'page' : undefined"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="flex items-center gap-1 lg:hidden">
                    <button
                        type="button"
                        class="flex size-9 items-center justify-center rounded-full text-fg-muted hover:bg-muted hover:text-fg focus:outline-none"
                        :aria-label="
                            isMobileMenuOpen ? 'Tutup Menu' : 'Buka Menu'
                        "
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                    >
                        <Icon :icon="isMobileMenuOpen ? X : Menu" :size="20" />
                    </button>
                </div>
            </div>

            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-if="isMobileMenuOpen"
                    class="mx-auto mt-2 max-w-6xl rounded-3xl border border-border/80 bg-card/95 p-4 shadow-xl backdrop-blur-2xl lg:hidden"
                >
                    <div class="grid grid-cols-2 gap-2">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="flex items-center gap-2.5 rounded-2xl p-3 text-sm font-semibold transition-colors"
                            :class="
                                isActive(item.href)
                                    ? 'bg-brand-weak text-brand'
                                    : 'text-fg-muted hover:bg-muted hover:text-fg'
                            "
                            @click="isMobileMenuOpen = false"
                        >
                            <span
                                class="flex size-8 items-center justify-center rounded-xl"
                                :class="
                                    isActive(item.href)
                                        ? 'bg-brand/10 text-brand'
                                        : 'bg-muted text-fg-muted'
                                "
                            >
                                <Icon :icon="item.icon" :size="16" />
                            </span>
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </Transition>
        </header>

        <main class="flex-1 pt-16 md:pt-20">
            <slot />
        </main>

        <footer class="border-t border-border/80 bg-card/60">
            <div
                class="mx-auto max-w-[90rem] px-3 py-10 sm:px-5 sm:py-14 lg:px-6"
            >
                <div
                    class="grid grid-cols-1 gap-8 sm:gap-10 lg:grid-cols-12 lg:gap-12"
                >
                    <div class="space-y-4 lg:col-span-4">
                        <span class="flex items-center gap-2.5">
                            <span
                                class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand to-brand-strong text-on-brand shadow-sm shadow-brand/20"
                            >
                                <Icon :icon="Sprout" :size="18" />
                            </span>
                            <span
                                class="text-lg font-bold tracking-tight text-fg"
                            >
                                DigiPangan<span
                                    class="font-medium text-fg-muted"
                                >
                                    Merauke</span
                                >
                            </span>
                        </span>
                        <p class="text-sm leading-relaxed text-fg-muted">
                            Etalase komoditas lokal dan pemetaan kawasan
                            transmigrasi Merauke. Membuka potensi pangan masa
                            depan Indonesia melalui teknologi digital.
                        </p>
                        <div
                            class="flex items-center gap-2 pt-1 text-xs font-medium text-fg-muted"
                        >
                            <Icon
                                :icon="MapPin"
                                :size="15"
                                class="text-brand shrink-0"
                            />
                            <span
                                >Kawasan Transmigrasi Merauke, Papua
                                Selatan</span
                            >
                        </div>
                    </div>

                    <div class="space-y-3 lg:col-span-5">
                        <h3
                            class="text-xs font-bold uppercase tracking-wider text-fg/80"
                        >
                            Kolaborasi Strategis
                        </h3>
                        <div
                            class="inline-flex flex-wrap items-center gap-4 sm:gap-6 rounded-2xl border border-border/80 bg-white px-4 py-3 sm:px-6 sm:py-4 shadow-sm max-w-full"
                        >
                            <img
                                src="/images/logos/logo-ugm-horizontal.png"
                                alt="Universitas Gadjah Mada"
                                class="h-8 sm:h-10 w-auto object-contain shrink-0"
                            />
                            <div class="h-8 sm:h-10 w-px bg-slate-200" />
                            <img
                                src="/images/logos/logotext-kementerian-transmigrasi.png"
                                alt="Kementerian Transmigrasi RI"
                                class="h-12 sm:h-16 w-auto object-contain shrink-0"
                            />
                        </div>
                        <p class="text-xs text-fg-muted">
                            Sinergi Universitas Gadjah Mada &amp; Kementerian
                            Transmigrasi RI untuk Kedaulatan Pangan Nusantara.
                        </p>
                    </div>

                    <div class="space-y-3 lg:col-span-3">
                        <h3
                            class="text-xs font-bold uppercase tracking-wider text-fg/80"
                        >
                            Navigasi Cepat
                        </h3>
                        <nav class="grid grid-cols-2 gap-2 text-sm">
                            <Link
                                v-for="item in navItems"
                                :key="item.href"
                                :href="item.href"
                                class="font-medium text-fg-muted transition-colors hover:text-brand"
                            >
                                {{ item.label }}
                            </Link>
                        </nav>
                    </div>
                </div>

                <div
                    class="mt-8 sm:mt-12 flex flex-col items-center justify-between gap-3 sm:gap-4 border-t border-border/70 pt-6 text-center text-xs text-fg-muted sm:flex-row sm:text-left"
                >
                    <p>
                        &copy; 2026 Universitas Gadjah Mada &amp; Kementerian
                        Transmigrasi RI. Hak Cipta Dilindungi.
                    </p>
                    <p class="font-medium">
                        DigiPangan Merauke v1.0 &bull; Membangun Kedaulatan
                        Pangan
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
