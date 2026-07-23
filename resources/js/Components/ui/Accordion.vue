<script setup lang="ts">
import { AccordionRoot } from 'reka-ui'

withDefaults(defineProps<{ defaultValue?: string | string[] }>(), {})
</script>

<template>
  <AccordionRoot
    type="single"
    :collapsible="true"
    :default-value="defaultValue as string"
    class="divide-y divide-border overflow-hidden rounded-card border border-border bg-card"
  >
    <slot />
  </AccordionRoot>
</template>
