<script setup lang="ts">
import { tv, type VariantProps } from 'tailwind-variants'

const card = tv({
  base: 'block border border-border bg-card rounded-card',
  variants: {
    padding: {
      none: '',
      sm: 'p-3',
      md: 'p-4',
      lg: 'p-6',
    },
    interactive: {
      true: 'transition-shadow transition-transform hover:shadow-soft hover:border-brand-weak active:scale-[0.98]',
      false: '',
    },
  },
  defaultVariants: { padding: 'md', interactive: false },
})

type CardVariants = VariantProps<typeof card>

withDefaults(
  defineProps<{
    as?: string
    padding?: CardVariants['padding']
    interactive?: boolean
  }>(),
  { as: 'div', padding: 'md', interactive: false },
)
</script>

<template>
  <component :is="as" :class="card({ padding, interactive })">
    <slot />
  </component>
</template>
