<script setup lang="ts">
import { ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminPageCard from "@/Components/admin/AdminPageCard.vue";
import { Search, Edit2, Home, CheckCircle2, XCircle, Eye } from "@lucide/vue";
import { Icon, Input, Button, Badge, EmptyState } from "@/Components/ui";

const props = defineProps<{
    villages?: {
        data: any[];
        links?: any;
        meta?: any;
    };
}>();

const search = ref("");

const villageList = computed(() => {
    const rawData = props.villages?.data;
    const items = Array.isArray(rawData)
        ? rawData
        : (rawData as any)?.data || [];

    if (!search.value) return items;
    const query = search.value.toLowerCase();
    return items.filter(
        (r: any) =>
            r.name.toLowerCase().includes(query) ||
            (r.region?.name && r.region.name.toLowerCase().includes(query)),
    );
});
</script>

<template>
    <AdminLayout
        title="Master Desa"
        subtitle="Manajemen data desa yang tergabung dalam kawasan transmigrasi."
    >
        <AdminPageCard>
            <template #header-actions>
                <div class="relative w-full sm:w-64">
                    <Icon
                        :icon="Search"
                        :size="16"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-fg-muted"
                    />
                    <Input
                        v-model="search"
                        type="search"
                        placeholder="Cari desa atau distrik..."
                        class="pl-9 w-full"
                    />
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-fg">
                    <thead
                        class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider"
                    >
                        <tr>
                            <th scope="col" class="px-5 py-3.5">Nama Desa</th>
                            <th scope="col" class="px-4 py-3.5">
                                Kawasan / Distrik
                            </th>
                            <th scope="col" class="px-4 py-3.5">Status</th>
                            <th scope="col" class="px-5 py-3.5 text-right">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr v-if="villageList.length === 0">
                            <td colspan="4" class="px-5 py-12 text-center">
                                <EmptyState
                                    title="Tidak ada desa ditemukan"
                                    description="Belum ada data desa yang sesuai dengan pencarian."
                                    :icon="Home"
                                />
                            </td>
                        </tr>
                        <tr
                            v-for="item in villageList"
                            :key="item.id"
                            class="transition-colors hover:bg-muted/20"
                        >
                            <td class="px-5 py-4 font-bold text-fg">
                                {{ item.name }}
                            </td>
                            <td class="px-4 py-4 text-fg-muted font-medium">
                                {{ item.region?.name || "-" }}
                            </td>
                            <td class="px-4 py-4">
                                <Badge
                                    :variant="
                                        item.is_active ? 'success' : 'neutral'
                                    "
                                    class="gap-1"
                                >
                                    <Icon
                                        :icon="
                                            item.is_active
                                                ? CheckCircle2
                                                : XCircle
                                        "
                                        :size="12"
                                    />
                                    <span>{{
                                        item.is_active ? "Aktif" : "Tidak Aktif"
                                    }}</span>
                                </Badge>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div
                                    class="flex items-center justify-end gap-1.5"
                                >
                                    <Link :href="`/admin/desa/${item.id}`">
                                        <Button
                                            variant="secondary"
                                            size="sm"
                                            class="size-8 p-0"
                                            title="Lihat Detail Desa"
                                        >
                                            <Icon :icon="Eye" :size="14" />
                                        </Button>
                                    </Link>
                                    <Link :href="`/admin/desa/${item.id}/edit`">
                                        <Button
                                            variant="secondary"
                                            size="sm"
                                            class="size-8 p-0"
                                            title="Edit Desa"
                                        >
                                            <Icon :icon="Edit2" :size="14" />
                                        </Button>
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </AdminPageCard>
    </AdminLayout>
</template>
