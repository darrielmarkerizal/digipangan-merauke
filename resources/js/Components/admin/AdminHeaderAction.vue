<script setup lang="ts">
import type { Component } from "vue";
import { Link } from "@inertiajs/vue3";
import { Button, Icon } from "@/Components/ui";

withDefaults(
    defineProps<{
        label: string;
        icon?: Component;
        href?: string;
        variant?: "primary" | "secondary" | "danger" | "ghost";
    }>(),
    {
        variant: "primary",
    },
);

defineEmits<{
    (e: "click"): void;
}>();
</script>

<template>
    <Link v-if="href" :href="href">
        <Button
            size="sm"
            :variant="variant"
            class="gap-2 shadow-xs cursor-pointer"
        >
            <Icon v-if="icon" :icon="icon" :size="15" />
            <span>{{ label }}</span>
        </Button>
    </Link>
    <Button
        v-else
        size="sm"
        :variant="variant"
        class="gap-2 shadow-xs cursor-pointer"
        @click="$emit('click')"
    >
        <Icon v-if="icon" :icon="icon" :size="15" />
        <span>{{ label }}</span>
    </Button>
</template>
