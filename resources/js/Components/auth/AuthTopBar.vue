<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { Sprout } from "@lucide/vue";
import { Icon } from "@/Components/ui";
</script>

<template>
    <div class="flex items-center justify-end lg:hidden">
        <Link href="/" class="flex items-center gap-2.5">
            <span
                class="flex size-9 items-center justify-center rounded-xl bg-brand text-on-brand shadow-sm"
            >
                <Icon :icon="Sprout" :size="18" />
            </span>
            <span class="text-lg font-bold tracking-tight text-fg">
                DigiPangan<span class="font-medium text-fg-muted">
                    Merauke</span
                >
            </span>
        </Link>
    </div>
</template>
