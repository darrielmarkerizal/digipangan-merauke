<script setup lang="ts">
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import RegionCard from "@/Components/region/RegionCard.vue";
import { Icon } from "@/Components/ui";
import {
    MapPin,
    Map,
    Tractor,
    ShoppingBasket,
    ArrowUpRight,
} from "@lucide/vue";
import type { RegionCard as RegionCardType } from "@/types/home";

const props = defineProps<{
    regions: RegionCardType[];
}>();

defineOptions({ layout: PublicLayout });

const totalVillages = computed(() =>
    props.regions.reduce((acc, curr) => acc + curr.villages_count, 0),
);
const totalFarmerGroups = computed(() =>
    props.regions.reduce((acc, curr) => acc + curr.farmer_groups_count, 0),
);
const totalProducts = computed(() =>
    props.regions.reduce((acc, curr) => acc + curr.products_count, 0),
);
</script>

<template>
    <Head title="Wilayah Transmigrasi" />

    <main class="min-h-screen bg-bg relative">

        <div
            class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none"
            style="
                background-image: radial-gradient(
                    #000 1.5px,
                    transparent 1.5px
                );
                background-size: 32px 32px;
            "
        ></div>

        <div
            class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8 py-12 lg:py-24 relative z-10"
        >
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">

                <div class="lg:w-[400px] xl:w-[450px] shrink-0">
                    <div class="lg:sticky lg:top-32">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-weak text-brand text-sm font-semibold mb-6"
                        >
                            <Icon :icon="MapPin" :size="16" />
                            Direktori Kawasan
                        </div>

                        <h1
                            class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-fg leading-[1.1] mb-6"
                        >
                            Jelajahi<br />
                            <span class="text-brand">Transmigrasi</span><br />
                            Merauke
                        </h1>

                        <p class="text-lg text-fg-muted leading-relaxed mb-10">
                            Merauke merupakan pilar strategis ketahanan pangan
                            nasional. Temukan potensi pertanian, kelompok tani,
                            dan komoditas unggulan yang tersebar di berbagai
                            distrik transmigrasi.
                        </p>


                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="bg-white rounded-3xl p-5 shadow-soft border border-fg/5"
                            >
                                <div
                                    class="size-10 rounded-xl bg-brand-weak text-brand flex items-center justify-center mb-4"
                                >
                                    <Icon :icon="Map" :size="20" />
                                </div>
                                <div class="text-3xl font-black text-fg mb-1">
                                    {{ regions.length }}
                                </div>
                                <div
                                    class="text-sm font-semibold text-fg-muted"
                                >
                                    Distrik Aktif
                                </div>
                            </div>

                            <div
                                class="bg-white rounded-3xl p-5 shadow-soft border border-fg/5"
                            >
                                <div
                                    class="size-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-4"
                                >
                                    <Icon :icon="ShoppingBasket" :size="20" />
                                </div>
                                <div class="text-3xl font-black text-fg mb-1">
                                    {{ totalProducts }}
                                </div>
                                <div
                                    class="text-sm font-semibold text-fg-muted"
                                >
                                    Produk Tani
                                </div>
                            </div>

                            <div
                                class="bg-white rounded-3xl p-5 shadow-soft border border-fg/5"
                            >
                                <div
                                    class="size-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4"
                                >
                                    <Icon :icon="Tractor" :size="20" />
                                </div>
                                <div class="text-3xl font-black text-fg mb-1">
                                    {{ totalFarmerGroups }}
                                </div>
                                <div
                                    class="text-sm font-semibold text-fg-muted"
                                >
                                    Kelompok Tani
                                </div>
                            </div>

                            <div
                                class="bg-brand rounded-3xl p-5 shadow-soft shadow-brand/20 text-on-brand flex flex-col justify-between"
                            >
                                <div>
                                    <div class="text-3xl font-black mb-1">
                                        {{ totalVillages }}
                                    </div>
                                    <div
                                        class="text-sm font-medium text-on-brand/80"
                                    >
                                        Desa/Kampung
                                    </div>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <div
                                        class="size-8 rounded-full bg-white/20 flex items-center justify-center"
                                    >
                                        <Icon :icon="ArrowUpRight" :size="16" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="flex-1">
                    <div
                        v-if="regions.length > 0"
                        class="flex flex-col gap-6"
                    >
                        <RegionCard
                            v-for="region in regions"
                            :key="region.slug"
                            :region="region"
                        />
                    </div>

                    <div
                        v-else
                        class="h-full min-h-[400px] flex flex-col items-center justify-center text-center p-8 bg-white rounded-3xl border border-dashed border-border shadow-xs"
                    >
                        <div
                            class="size-16 rounded-full bg-muted flex items-center justify-center text-fg-muted mb-4"
                        >
                            <Icon :icon="MapPin" :size="32" />
                        </div>
                        <h3 class="text-lg font-bold text-fg mb-2">
                            Belum ada wilayah
                        </h3>
                        <p class="text-fg-muted max-w-sm">
                            Saat ini belum ada data kawasan transmigrasi yang
                            tersedia di sistem.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
