<script setup lang="ts">
import { tv, type VariantProps } from 'tailwind-variants'

const skeleton = tv({
  base: 'block bg-muted motion-safe:animate-pulse',
  variants: {
    radius: {
      control: 'rounded-control',
      card: 'rounded-card',
      full: 'rounded-full',
      none: 'rounded-none',
    },
  },
  defaultVariants: { radius: 'control' },
})

type SkeletonVariants = VariantProps<typeof skeleton>

withDefaults(defineProps<{ radius?: SkeletonVariants['radius'] }>(), {
  radius: 'control',
})
</script>

<template>
  <div :class="skeleton({ radius })" aria-hidden="true" />
</template>
