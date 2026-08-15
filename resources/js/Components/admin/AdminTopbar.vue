<script setup lang="ts">
import { Menu, MapPin } from "@lucide/vue";
import { Icon, Breadcrumb } from "@/Components/ui";
import AdminUserProfile from "./AdminUserProfile.vue";
import { useAuthGuard } from "@/Composables/useAuthGuard";

defineProps<{
    title?: string;
    subtitle?: string;
}>();

defineEmits<{
    (e: "toggle-sidebar"): void;
}>();

const { isDistrictAdmin, userRegion } = useAuthGuard();
</script>

<template>
    <header
        class="sticky top-0 z-30 flex h-14 w-full items-center justify-between border-b border-border/80 bg-white/90 px-4 backdrop-blur-xl md:px-6"
    >
        <div class="flex items-center gap-4">
            <button
                type="button"
                class="flex size-9 items-center justify-center rounded-xl border border-border/80 text-fg-muted transition-colors hover:bg-muted/60 hover:text-fg lg:hidden"
                aria-label="Buka navigasi"
                @click="$emit('toggle-sidebar')"
            >
                <Icon :icon="Menu" :size="18" />
            </button>
            <Breadcrumb
                :items="[
                    { label: 'Admin', href: '/admin/dashboard' },
                    ...(title ? [{ label: title }] : []),
                ]"
            />
        </div>

        <div class="flex items-center gap-3">
            <div
                v-if="isDistrictAdmin && userRegion"
                class="hidden sm:flex items-center gap-1.5 rounded-full border border-brand/30 bg-brand-weak/40 px-3 py-1 text-xs font-semibold text-brand shadow-xs"
            >
                <Icon :icon="MapPin" :size="13" class="text-brand shrink-0" />
                <span>Distrik {{ userRegion.name }}</span>
            </div>
            <AdminUserProfile />
        </div>
    </header>
</template>
