<script setup lang="ts">
import { Icon } from "@/Components/ui";
import { MessageCircle } from "@lucide/vue";

defineProps<{
    popularProducts: Array<{
        id: number;
        name: string;
        region: string;
        contact_count: number;
    }>;
}>();
</script>

<template>
    <div class="h-full rounded-2xl border border-border/80 bg-white p-5 shadow-xs">
        <h3 class="font-bold text-fg">Top Produk Diminati</h3>
        <p class="mb-4 text-xs text-fg-muted">Produk dengan interaksi WA terbanyak</p>
        
        <div v-if="popularProducts.length === 0" class="text-sm text-fg-muted text-center py-4">
            Belum ada data.
        </div>

        <div class="space-y-3">
            <div v-for="(product, index) in popularProducts" :key="product.id" class="flex items-center justify-between p-3 border border-border/50 rounded-xl bg-muted/10 transition-colors hover:bg-muted/20">
                <div class="flex items-center gap-3">
                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-brand text-white text-xs font-bold shadow-sm">
                        {{ Number(index) + 1 }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-fg">{{ product.name }}</p>
                        <p class="text-xs text-fg-muted">{{ product.region }}</p>
                    </div>
                </div>
                <div class="text-right flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 rounded-md shadow-sm border border-green-200">
                    <Icon :icon="MessageCircle" :size="14" />
                    <span class="text-xs font-bold">{{ product.contact_count }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
