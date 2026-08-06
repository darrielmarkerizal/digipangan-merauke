<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { ArrowRight, Sprout } from "@lucide/vue";
import { Icon } from "@/Components/ui";
import { useRupiah } from "@/Composables/useRupiah";
import type { RegionCard } from "@/types/home";

const props = defineProps<{ region: RegionCard }>();
const { formatAngka } = useRupiah();

const stats = computed(() => [
    { label: "Produk", value: props.region.products_count },
    { label: "Kelompok", value: props.region.farmer_groups_count },
    { label: "Desa", value: props.region.villages_count },
]);
</script>

<template>
    <Link :href="`/wilayah/${region.slug}`" class="group block w-full">
        <article
            class="flex flex-col sm:flex-row w-full overflow-hidden rounded-2xl sm:rounded-3xl border border-border/80 bg-white shadow-xs transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-soft"
        >

            <div
                class="relative aspect-[2/1] sm:aspect-[4/3] sm:w-[240px] md:w-[280px] lg:w-[240px] xl:w-[300px] shrink-0 overflow-hidden bg-muted/30"
            >
                <img
                    v-if="region.cover"
                    :src="region.cover.card"
                    :alt="`Wilayah ${region.name}`"
                    loading="lazy"
                    class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                />
                <div
                    v-else
                    class="flex size-full items-center justify-center bg-brand-weak/30 text-brand/40"
                    aria-hidden="true"
                >
                    <Icon :icon="Sprout" :size="48" />
                </div>
            </div>


            <div class="flex flex-1 flex-col justify-between p-5 sm:p-6 lg:p-8">
                <div>
                    <div class="flex items-start justify-between gap-4">
                        <h3
                            class="text-xl sm:text-2xl font-bold text-fg transition-colors group-hover:text-brand"
                        >
                            {{ region.name }}
                        </h3>
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-weak text-brand transition-all duration-300 ease-premium group-hover:bg-brand group-hover:text-white sm:opacity-0 group-hover:opacity-100 sm:-translate-x-2 group-hover:translate-x-0"
                            aria-hidden="true"
                        >
                            <Icon :icon="ArrowRight" :size="16" />
                        </span>
                    </div>
                    <p class="text-sm text-fg-muted mt-2 line-clamp-2 max-w-lg">
                        Eksplorasi potensi pertanian, kelompok tani aktif, dan komoditas unggulan di distrik transmigrasi {{ region.name }}.
                    </p>
                </div>

                <div class="mt-6 sm:mt-8 pt-6 sm:pt-0 border-t sm:border-0 border-border/60">
                    <dl class="flex flex-wrap gap-6 sm:gap-8 lg:gap-12">
                        <div v-for="s in stats" :key="s.label" class="min-w-0">
                            <dt
                                class="text-[10px] sm:text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                            >
                                {{ s.label }}
                            </dt>
                            <dd
                                class="text-xl sm:text-2xl font-black tabular-nums text-fg"
                            >
                                {{ formatAngka(s.value) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </article>
    </Link>
</template>
