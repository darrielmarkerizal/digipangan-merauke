<script setup lang="ts">
import { inject } from 'vue'
import { ChevronDown } from '@lucide/vue'
import { FIELD_KEY } from './field-context'
import Icon from './Icon.vue'

defineOptions({ inheritAttrs: false })

defineProps<{ modelValue?: string | number }>()
const emit = defineEmits<{ 'update:modelValue': [value: string] }>()

const field = inject(FIELD_KEY, null)

const base =
  'w-full min-h-11 appearance-none rounded-control border border-border bg-card pl-3 pr-9 text-base text-fg aria-invalid:border-danger disabled:opacity-60'
</script>

<template>
  <div class="relative">
    <select
      :id="field?.id.value"
      :value="modelValue"
      :aria-invalid="field?.invalid.value || undefined"
      :aria-describedby="field?.describedBy.value"
      :aria-required="field?.required.value || undefined"
      :class="base"
      v-bind="$attrs"
      @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
      <slot />
    </select>
    <Icon
      :icon="ChevronDown"
      :size="20"
      class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-fg-muted"
    />
  </div>
</template>
