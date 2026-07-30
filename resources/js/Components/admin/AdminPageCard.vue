<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="rounded-xl border border-border/80 bg-white p-5 shadow-xs">
        <div
            class="overflow-hidden rounded-xl border border-border/80 bg-muted/10"
        >
            <div
                v-if="title || description"
                class="flex flex-col items-center justify-center py-14 text-center px-4"
            >
                <p v-if="title" class="text-sm font-bold text-fg">
                    {{ title }}
                </p>
                <p
                    v-if="description"
                    class="mt-1 text-xs text-fg-muted max-w-sm"
                >
                    {{ description }}
                </p>
            </div>
            <slot />
        </div>
    </div>
</template>
