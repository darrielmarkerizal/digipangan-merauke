<script setup lang="ts">
import { type Component } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronRight } from '@lucide/vue'
import Icon from './Icon.vue'

export interface BreadcrumbItem {
  label: string
  href?: string
  icon?: Component
}

withDefaults(
  defineProps<{
    items?: BreadcrumbItem[]
    separator?: 'slash' | 'chevron'
  }>(),
  {
    items: () => [],
    separator: 'slash',
  },
)
</script>

<template>
  <nav aria-label="Breadcrumb" class="flex items-center text-sm">
    <ol v-if="items && items.length > 0" class="flex flex-wrap items-center gap-1.5 sm:gap-2">
      <li
        v-for="(item, index) in items"
        :key="index"
        class="inline-flex items-center gap-1.5 sm:gap-2"
      >
        <span
          v-if="index > 0"
          class="text-border/80 select-none font-normal"
          aria-hidden="true"
        >
          <Icon
            v-if="separator === 'chevron'"
            :icon="ChevronRight"
            :size="14"
            class="text-fg-muted/60"
          />
          <span v-else class="text-border font-medium">/</span>
        </span>

        <Link
          v-if="item.href && index !== items.length - 1"
          :href="item.href"
          class="inline-flex items-center gap-1.5 font-medium text-fg-muted transition-colors hover:text-fg"
        >
          <Icon v-if="item.icon" :icon="item.icon" :size="15" />
          <span>{{ item.label }}</span>
        </Link>

        <span
          v-else
          class="inline-flex items-center gap-1.5"
          :class="index === items.length - 1 ? 'font-semibold text-fg' : 'font-medium text-fg-muted'"
          :aria-current="index === items.length - 1 ? 'page' : undefined"
        >
          <Icon v-if="item.icon" :icon="item.icon" :size="15" />
          <span>{{ item.label }}</span>
        </span>
      </li>
    </ol>
    <div v-else class="flex items-center gap-2 text-sm">
      <slot />
    </div>
  </nav>
</template>
