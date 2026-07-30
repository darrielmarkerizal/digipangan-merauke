<script setup lang="ts">
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    Sprout,
    LayoutDashboard,
    ShoppingBasket,
    Tags,
    Scale,
    Users,
    UserCheck,
    MapPin,
    Home,
    Newspaper,
    Handshake,
    HelpCircle,
    Shield,
    Settings,
    History,
} from "@lucide/vue";
import { Icon } from "@/Components/ui";

const page = usePage();

const navGroups = [
    {
        title: "Utama",
        items: [
            {
                label: "Dashboard & Dampak",
                href: "/admin/dashboard",
                icon: LayoutDashboard,
            },
        ],
    },
    {
        title: "Katalog & Pangan",
        items: [
            {
                label: "Produk Pangan",
                href: "/admin/produk",
                icon: ShoppingBasket,
            },
            {
                label: "Kategori Produk",
                href: "/admin/kategori",
                icon: Tags,
            },
            {
                label: "Satuan & Berat",
                href: "/admin/satuan",
                icon: Scale,
            },
            {
                label: "Master Komoditas",
                href: "/admin/komoditas",
                icon: Sprout,
            },
        ],
    },
    {
        title: "Komunitas & Kawasan",
        items: [
            {
                label: "Profil Petani",
                href: "/admin/petani",
                icon: Users,
            },
            {
                label: "Kelompok Tani",
                href: "/admin/kelompok-tani",
                icon: UserCheck,
            },
            {
                label: "Kawasan Transmigrasi",
                href: "/admin/wilayah",
                icon: MapPin,
            },
            {
                label: "Master Desa",
                href: "/admin/desa",
                icon: Home,
            },
        ],
    },
    {
        title: "Konten & Edukasi",
        items: [
            {
                label: "Berita",
                href: "/admin/berita",
                icon: Newspaper,
            },
            {
                label: "Pusat Bantuan / FAQ",
                href: "/admin/faq",
                icon: HelpCircle,
            },
        ],
    },
    {
        title: "Sistem & Keamanan",
        items: [
            {
                label: "User & Hak Akses",
                href: "/admin/user",
                icon: Shield,
            },
            {
                label: "Pengaturan Situs",
                href: "/admin/pengaturan",
                icon: Settings,
            },
            {
                label: "Log Aktivitas (Audit)",
                href: "/admin/audit-log",
                icon: History,
            },
        ],
    },
];

const currentPath = computed(() => page.url.split("?")[0]);

const isActive = (href: string) => {
    if (href === "/admin/dashboard") {
        return (
            currentPath.value === "/admin/dashboard" ||
            currentPath.value === "/admin"
        );
    }
    return currentPath.value.startsWith(href);
};
</script>

<template>
    <aside class="flex h-full w-64 flex-col border-r border-border/80 bg-white">
        <div
            class="flex h-14 shrink-0 items-center justify-between border-b border-border/80 px-5"
        >
            <Link href="/admin/dashboard" class="flex items-center gap-3">
                <span
                    class="flex size-9 items-center justify-center rounded-xl bg-brand text-white shadow-xs"
                >
                    <Icon :icon="Sprout" :size="20" />
                </span>
                <div class="flex flex-col">
                    <span
                        class="text-base font-bold leading-tight tracking-tight text-fg"
                    >
                        DigiPangan<span class="font-normal text-fg-muted">
                            Merauke</span
                        >
                    </span>
                    <span
                        class="text-[11px] font-semibold uppercase tracking-wider text-brand"
                    >
                        Portal Admin
                    </span>
                </div>
            </Link>
        </div>

        <div
            class="flex-1 overflow-y-auto px-4 py-6 space-y-7 custom-scrollbar"
        >
            <div
                v-for="group in navGroups"
                :key="group.title"
                class="space-y-1.5"
            >
                <p
                    class="px-3 text-[11px] font-mono font-bold uppercase tracking-widest text-fg-muted/60"
                >
                    {{ group.title }}
                </p>
                <nav class="space-y-0.5">
                    <Link
                        v-for="item in group.items"
                        :key="item.href"
                        :href="item.href"
                        class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-150"
                        :class="
                            isActive(item.href)
                                ? 'bg-brand-weak text-brand font-semibold shadow-xs'
                                : 'text-fg-muted hover:bg-muted/60 hover:text-fg'
                        "
                    >
                        <span class="flex items-center gap-3">
                            <Icon
                                :icon="item.icon"
                                :size="18"
                                class="shrink-0 transition-colors"
                                :class="
                                    isActive(item.href)
                                        ? 'text-brand'
                                        : 'text-fg-muted/70 group-hover:text-fg'
                                "
                            />
                            <span class="truncate">{{ item.label }}</span>
                        </span>
                        <span
                            v-if="isActive(item.href)"
                            class="size-1.5 rounded-full bg-brand"
                        />
                    </Link>
                </nav>
            </div>
        </div>
    </aside>
</template>
