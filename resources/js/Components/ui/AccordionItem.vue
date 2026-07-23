<script setup lang="ts">
import {
  AccordionItem,
  AccordionHeader,
  AccordionTrigger,
  AccordionContent,
} from 'reka-ui'
import { ChevronDown } from '@lucide/vue'
import Icon from './Icon.vue'

defineProps<{ value: string; title?: string }>()
</script>

<template>
  <AccordionItem :value="value" class="group">
    <AccordionHeader as="h3" class="flex">
      <AccordionTrigger
        class="flex min-h-11 flex-1 items-center justify-between gap-3 px-4 py-4 text-left text-base font-semibold text-fg hover:bg-muted"
      >
        <span><slot name="title">{{ title }}</slot></span>
        <Icon
          :icon="ChevronDown"
          :size="20"
          class="shrink-0 text-fg-muted transition-transform duration-200 group-data-[state=open]:rotate-180"
        />
      </AccordionTrigger>
    </AccordionHeader>
    <AccordionContent class="px-4 pb-4 text-fg-muted">
      <slot />
    </AccordionContent>
  </AccordionItem>
</template>
