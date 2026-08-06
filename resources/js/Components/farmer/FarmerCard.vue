<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { MapPin, ArrowUpRight, User, Tractor, Sprout } from "@lucide/vue";
import { Icon } from "@/Components/ui";
import type { FarmerCard } from "@/types/home";

const props = defineProps<{ farmer: FarmerCard }>();

const commodities = computed(() => props.farmer.commodities ?? []);
const visibleCommodities = computed(() => commodities.value.slice(0, 3));
const extraCommodities = computed(() =>
    Math.max(commodities.value.length - visibleCommodities.value.length, 0),
);
</script>

<template>
    <Link :href="`/petani/${farmer.slug}`" class="group block h-full">
        <article
            class="flex h-full flex-col overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-soft"
        >
            <div class="flex items-start gap-4 p-5">
                <div
                    class="relative size-16 shrink-0 overflow-hidden rounded-2xl bg-brand-weak/40 ring-1 ring-fg/5"
                >
                    <img
                        v-if="farmer.photo"
                        :src="farmer.photo.thumb || farmer.photo.card"
                        :alt="`Foto ${farmer.name}`"
                        loading="lazy"
                        class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                    />
                    <div
                        v-else
                        class="flex size-full items-center justify-center text-brand/40"
                        aria-hidden="true"
                    >
                        <Icon :icon="User" :size="28" />
                    </div>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <h3
                            class="truncate text-base font-bold text-fg transition-colors group-hover:text-brand"
                        >
                            {{ farmer.name }}
                        </h3>
                        <span
                            class="flex size-7 shrink-0 items-center justify-center rounded-full bg-brand-weak text-brand transition-all duration-300 ease-premium group-hover:bg-brand group-hover:text-white sm:opacity-0 group-hover:opacity-100 sm:-translate-x-1 group-hover:translate-x-0"
                            aria-hidden="true"
                        >
                            <Icon :icon="ArrowUpRight" :size="14" />
                        </span>
                    </div>

                    <p
                        v-if="farmer.region"
                        class="mt-1 flex items-center gap-1 text-sm text-fg-muted"
                    >
                        <Icon :icon="MapPin" :size="14" class="shrink-0" />
                        <span class="truncate">Distrik {{ farmer.region.name }}</span>
                    </p>

                    <p
                        v-if="farmer.farmer_group"
                        class="mt-0.5 flex items-center gap-1 text-xs text-fg-muted"
                    >
                        <Icon :icon="Tractor" :size="13" class="shrink-0" />
                        <span class="truncate">{{ farmer.farmer_group.name }}</span>
                    </p>
                </div>
            </div>

            <div
                v-if="visibleCommodities.length > 0"
                class="flex flex-wrap gap-1.5 px-5 pb-4"
            >
                <span
                    v-for="commodity in visibleCommodities"
                    :key="commodity.slug"
                    class="inline-flex items-center gap-1 rounded-full bg-brand/8 px-2.5 py-0.5 text-xs font-semibold text-brand"
                >
                    <Icon :icon="Sprout" :size="11" />
                    {{ commodity.name }}
                </span>
                <span
                    v-if="extraCommodities > 0"
                    class="inline-flex items-center rounded-full bg-muted px-2.5 py-0.5 text-xs font-semibold text-fg-muted"
                >
                    +{{ extraCommodities }}
                </span>
            </div>

            <div
                class="mt-auto flex items-center justify-between border-t border-border/60 px-5 py-3"
            >
                <span class="text-xs font-medium text-fg-muted">
                    <span class="font-bold text-fg">{{ farmer.products_count }}</span>
                    produk dijual
                </span>
                <span
                    class="text-xs font-bold text-brand group-hover:underline"
                >
                    Lihat Profil
                </span>
            </div>
        </article>
    </Link>
</template>
