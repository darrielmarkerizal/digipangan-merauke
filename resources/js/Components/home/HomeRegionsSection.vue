<script setup lang="ts">
import RegionCard from "@/Components/region/RegionCard.vue";
import { EmptyState } from "@/Components/ui";
import type { RegionCard as RegionCardType } from "@/types/home";

defineProps<{
    regions: RegionCardType[];
    grain: string;
}>();
</script>

<template>
    <section
        class="flex min-h-[100dvh] w-full flex-col justify-center border-b border-border/80 bg-white py-16 sm:py-20 lg:py-28"
    >
        <div class="mx-auto max-w-[90rem] px-3 sm:px-5 lg:px-6">
            <div class="mx-auto max-w-3xl text-center space-y-3 sm:space-y-4">
                <span
                    class="text-xs font-bold uppercase tracking-widest text-brand"
                >
                    Kawasan Transmigrasi Merauke
                </span>
                <h2
                    class="text-2xl font-extrabold tracking-tight text-fg sm:text-3xl lg:text-4xl"
                >
                    Jelajahi Potensi Alam per Distrik
                </h2>
                <p class="text-sm text-fg-muted sm:text-base">
                    Dari sawah pangan Muting, hortikultura Ulilin, hingga
                    perkebunan Elikobel. Setiap kawasan memiliki karakter
                    agraris unik yang siap disuplai ke pasar.
                </p>
            </div>

            <div
                v-if="regions.length > 0"
                class="mt-10 sm:mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 sm:gap-8"
            >
                <RegionCard
                    v-for="region in regions"
                    :key="region.slug"
                    :region="region"
                />
            </div>

            <div v-else class="mt-10 sm:mt-14">
                <EmptyState
                    title="Belum Ada Data Kawasan"
                    description="Saat ini belum ada data kawasan transmigrasi yang terdaftar."
                />
            </div>
        </div>
    </section>
</template>
