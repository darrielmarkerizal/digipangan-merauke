<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { MapPin, CircleX, ArrowUpRight, Sprout } from '@lucide/vue'
import { tv, type VariantProps } from 'tailwind-variants'
import { Icon } from '@/Components/ui'
import { useRupiah } from '@/Composables/useRupiah'
import type { ProductCard } from '@/types/home'

const card = tv({
  slots: {
    frame:
      'group flex h-full flex-col overflow-hidden rounded-card border border-border/80 bg-white shadow-xs transition-[transform,box-shadow] duration-300 ease-out hover:-translate-y-1 hover:shadow-soft',
    media: 'relative overflow-hidden bg-muted/60',
    image:
      'size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105',
    body: 'flex flex-1 flex-col',
    title: 'line-clamp-2 font-semibold text-fg transition-colors group-hover:text-brand',
    price: 'font-bold tabular-nums text-brand',
    footer: 'flex items-end justify-between gap-2',
  },
  variants: {
    variant: {
      default: {
        media: 'aspect-[4/3]',
        body: 'gap-1 p-4',
        title: 'text-base',
        price: 'text-lg',
        footer: 'mt-auto pt-3',
      },
      spotlight: {
        media: 'aspect-square',
        body: 'gap-1.5 p-5',
        title: 'text-xl',
        price: 'text-2xl',
        footer: 'mt-auto pt-3',
      },
      horizontal: {
        frame: 'flex-row',
        media: 'w-[40%] shrink-0',
        body: 'justify-center gap-1 p-5',
        title: 'text-lg',
        price: 'text-xl',
        footer: 'pt-2',
      },
    },
  },
  defaultVariants: { variant: 'default' },
})

type Variants = VariantProps<typeof card>

const props = withDefaults(
  defineProps<{ product: ProductCard; variant?: Variants['variant'] }>(),
  { variant: 'default' },
)

const { formatRupiah } = useRupiah()

const s = computed(() => card({ variant: props.variant }))
const harga = computed(() => formatRupiah(Number(props.product.price)))
const alt = computed(() =>
  props.product.region
    ? `${props.product.name} dari ${props.product.region.name}`
    : props.product.name,
)
</script>

<template>
  <Link :href="`/produk/${product.slug}`" class="block h-full">
    <article :class="s.frame()">
      <div :class="s.media()">
        <img
          v-if="product.photo"
          :src="product.photo.card"
          :alt="alt"
          loading="lazy"
          :class="s.image()"
        />
        <div
          v-else
          class="flex size-full items-center justify-center bg-muted/60 text-brand/40"
          aria-hidden="true"
        >
          <Icon :icon="Sprout" :size="variant === 'spotlight' ? 56 : 36" />
        </div>

        <span
          v-if="!product.stock_available"
          class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-card/90 px-2.5 py-1 text-xs font-semibold text-danger shadow-sm ring-1 ring-fg/5 backdrop-blur"
        >
          <Icon :icon="CircleX" :size="13" /> Stok habis
        </span>
      </div>

      <div :class="s.body()">
        <p
          v-if="product.region"
          class="flex items-center gap-1 text-sm text-fg-muted"
        >
          <Icon :icon="MapPin" :size="14" /> {{ product.region.name }}
        </p>

        <h3 :class="s.title()">{{ product.name }}</h3>

        <p
          v-if="variant === 'spotlight' && product.farmer"
          class="truncate text-sm text-fg-muted"
        >
          {{ product.farmer.name }}
        </p>

        <div :class="s.footer()">
          <span :class="s.price()">{{ harga }}</span>
          <span
            class="flex size-8 -translate-x-1 items-center justify-center rounded-full bg-brand-weak text-brand opacity-0 transition-all duration-300 ease-premium group-hover:translate-x-0 group-hover:opacity-100"
            aria-hidden="true"
          >
            <Icon :icon="ArrowUpRight" :size="16" />
          </span>
        </div>
      </div>
    </article>
  </Link>
</template>
