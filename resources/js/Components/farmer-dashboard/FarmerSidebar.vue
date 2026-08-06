<script setup lang="ts">
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { Sprout, LayoutDashboard, User, ShoppingBasket } from "@lucide/vue";
import { Icon } from "@/Components/ui";

const page = usePage();

const navItems = [
    {
        label: "Dashboard",
        href: "/petani/dashboard",
        icon: LayoutDashboard,
    },
    {
        label: "Profil Saya",
        href: "/petani/dashboard/profil",
        icon: User,
    },
    {
        label: "Produk Saya",
        href: "/petani/dashboard/produk",
        icon: ShoppingBasket,
    },
];

const currentPath = computed(() => page.url.split("?")[0]);

const isActive = (href: string) => {
    if (href === "/petani/dashboard") {
        return currentPath.value === "/petani/dashboard";
    }
    return currentPath.value.startsWith(href);
};
</script>

<template>
    <aside class="flex h-full w-64 flex-col border-r border-border/80 bg-white">
        <div
            class="flex h-14 shrink-0 items-center justify-between border-b border-border/80 px-5"
        >
            <Link href="/petani/dashboard" class="flex items-center gap-3">
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
                        Portal Petani
                    </span>
                </div>
            </Link>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 custom-scrollbar">
            <nav class="space-y-0.5">
                <Link
                    v-for="item in navItems"
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
    </aside>
</template>
