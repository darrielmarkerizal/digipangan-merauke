<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { Newspaper, CalendarDays, ArrowUpRight } from "@lucide/vue";
import { tv, type VariantProps } from "tailwind-variants";
import { Icon } from "@/Components/ui";
import { formatTanggal } from "@/lib/format";
import type { PostCard } from "@/types/home";

const card = tv({
    slots: {
        frame: "group flex h-full flex-col overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-soft",
        media: "relative overflow-hidden bg-muted/60",
        image: "size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105",
        body: "flex flex-1 flex-col p-5",
        title: "line-clamp-2 font-bold text-fg transition-colors group-hover:text-brand",
    },
    variants: {
        variant: {
            default: {
                media: "aspect-[16/10]",
                title: "text-base",
            },
            feature: {
                frame: "sm:flex-row",
                media: "aspect-[16/10] sm:aspect-auto sm:w-[45%] shrink-0",
                body: "sm:p-6",
                title: "text-lg sm:text-2xl",
            },
        },
    },
    defaultVariants: { variant: "default" },
});

type Variants = VariantProps<typeof card>;

const props = withDefaults(
    defineProps<{ post: PostCard; variant?: Variants["variant"] }>(),
    { variant: "default" },
);

const s = computed(() => card({ variant: props.variant }));
</script>

<template>
    <Link :href="`/berita/${post.slug}`" class="block h-full">
        <article :class="s.frame()">
            <div :class="s.media()">
                <img
                    v-if="post.cover"
                    :src="post.cover.card"
                    :alt="post.title"
                    loading="lazy"
                    :class="s.image()"
                />
                <div
                    v-else
                    class="flex size-full items-center justify-center bg-brand-weak/30 text-brand/30"
                    aria-hidden="true"
                >
                    <Icon :icon="Newspaper" :size="variant === 'feature' ? 56 : 36" />
                </div>

                <span
                    v-if="post.category"
                    class="absolute left-3 top-3 inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-xs font-bold text-brand shadow-sm ring-1 ring-fg/5 backdrop-blur"
                >
                    {{ post.category.name }}
                </span>
            </div>

            <div :class="s.body()">
                <p
                    class="mb-2 flex items-center gap-1.5 text-xs font-medium text-fg-muted"
                >
                    <Icon :icon="CalendarDays" :size="13" />
                    {{ formatTanggal(post.published_at) }}
                </p>

                <h3 :class="s.title()">{{ post.title }}</h3>

                <p
                    class="mt-2 line-clamp-2 text-sm leading-relaxed text-fg-muted"
                    :class="variant === 'feature' ? 'sm:line-clamp-3' : ''"
                >
                    {{ post.excerpt }}
                </p>

                <div
                    class="mt-auto flex items-center gap-1 pt-4 text-xs font-bold text-brand"
                >
                    <span class="group-hover:underline">Baca Selengkapnya</span>
                    <Icon
                        :icon="ArrowUpRight"
                        :size="14"
                        class="transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                    />
                </div>
            </div>
        </article>
    </Link>
</template>
