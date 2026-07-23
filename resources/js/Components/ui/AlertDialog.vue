<script setup lang="ts">
import {
  AlertDialogRoot,
  AlertDialogTrigger,
  AlertDialogPortal,
  AlertDialogOverlay,
  AlertDialogContent,
  AlertDialogTitle,
  AlertDialogDescription,
  AlertDialogCancel,
  AlertDialogAction,
} from 'reka-ui'
import Button from './Button.vue'

withDefaults(
  defineProps<{
    title: string
    description?: string
    confirmLabel?: string
    cancelLabel?: string
    destructive?: boolean
  }>(),
  { confirmLabel: 'Hapus', cancelLabel: 'Batal', destructive: true },
)

const emit = defineEmits<{ confirm: [] }>()
</script>

<template>
  <AlertDialogRoot>
    <AlertDialogTrigger as-child>
      <slot name="trigger" />
    </AlertDialogTrigger>
    <AlertDialogPortal>
      <AlertDialogOverlay class="fixed inset-0 z-40 bg-fg/50" />
      <AlertDialogContent
        class="fixed left-1/2 top-1/2 z-50 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 -translate-y-1/2 rounded-card border border-border bg-card p-6 shadow-soft focus:outline-none"
      >
        <AlertDialogTitle class="text-lg font-semibold text-fg">
          {{ title }}
        </AlertDialogTitle>
        <AlertDialogDescription v-if="description" class="mt-2 text-sm text-fg-muted">
          {{ description }}
        </AlertDialogDescription>
        <div class="mt-6 flex justify-end gap-3">
          <AlertDialogCancel as-child>
            <Button variant="secondary">{{ cancelLabel }}</Button>
          </AlertDialogCancel>
          <AlertDialogAction as-child>
            <Button :variant="destructive ? 'danger' : 'primary'" @click="emit('confirm')">
              {{ confirmLabel }}
            </Button>
          </AlertDialogAction>
        </div>
      </AlertDialogContent>
    </AlertDialogPortal>
  </AlertDialogRoot>
</template>
