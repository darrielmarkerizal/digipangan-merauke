<script setup lang="ts">
import { ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Search, Edit2, MapPin, CheckCircle2, XCircle, Eye } from "@lucide/vue";
import { Icon, Input, Button, Badge, EmptyState } from "@/Components/ui";

const props = defineProps<{
    regions?: {
        data: any[];
        links?: any;
        meta?: any;
    };
}>();

const search = ref("");

const regionList = computed(() => {
    const rawData = props.regions?.data;
    const items = Array.isArray(rawData)
        ? rawData
        : (rawData as any)?.data || [];

    if (!search.value) return items;
    const query = search.value.toLowerCase();
    return items.filter((r: any) => r.name.toLowerCase().includes(query));
});
</script>

<template>
    <AdminLayout
        title="Kelola Wilayah"
        subtitle="Manajemen profil distrik transmigrasi Muting, Ulilin, dan Elikobel."
    >
        <template #actions>
        </template>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row w-full sm:w-auto justify-between gap-3 items-center">
                <div class="relative w-full sm:w-80">
                    <Icon
                        :icon="Search"
                        :size="16"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted pointer-events-none"
                    />
                    <Input
                        v-model="search"
                        type="search"
                        placeholder="Cari wilayah..."
                        class="pl-10 w-full"
                    />
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-fg">
                        <thead
                            class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider"
                        >
                            <tr>
                                <th scope="col" class="px-5 py-3.5">
                                    Nama Wilayah
                                </th>
                                <th scope="col" class="px-4 py-3.5">Luas (km²)</th>
                                <th scope="col" class="px-4 py-3.5">Populasi</th>
                                <th scope="col" class="px-4 py-3.5">Status</th>
                                <th scope="col" class="px-5 py-3.5 text-right">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-if="regionList.length === 0">
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <EmptyState
                                        title="Tidak ada wilayah ditemukan"
                                        description="Belum ada data wilayah yang sesuai dengan pencarian."
                                        :icon="MapPin"
                                    />
                                </td>
                            </tr>
                            <tr
                                v-for="item in regionList"
                                :key="item.id"
                                class="transition-colors hover:bg-muted/20"
                            >
                                <td class="px-5 py-4 font-bold text-fg">
                                    {{ item.name }}
                                </td>
                                <td class="px-4 py-4 text-fg-muted">
                                    {{ item.area_km2 || "-" }}
                                </td>
                                <td class="px-4 py-4 text-fg-muted">
                                    {{ item.population || "-" }}
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
                                        <Link :href="`/admin/wilayah/${item.id}`">
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                class="size-8 p-0"
                                                title="Lihat Detail Wilayah"
                                            >
                                                <Icon :icon="Eye" :size="14" />
                                            </Button>
                                        </Link>
                                        <Link
                                            :href="`/admin/wilayah/${item.id}/edit`"
                                        >
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                class="size-8 p-0"
                                                title="Edit Wilayah"
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
            </div>
        </div>
    </AdminLayout>
</template>
