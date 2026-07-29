<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {
    ArrowLeft,
    Edit2,
    MapPin,
    CheckCircle2,
    XCircle,
    Info,
} from "@lucide/vue";
import { Button, Icon, Badge } from "@/Components/ui";

const props = defineProps<{
    region: any;
}>();
</script>

<template>
    <AdminLayout
        :title="`Detail Wilayah: ${region.name}`"
        subtitle="Informasi lengkap mengenai profil kawasan transmigrasi ini."
    >
        <template #actions>
            <Link :href="`/admin/wilayah/${region.id}/edit`">
                <Button size="sm" class="gap-1.5 font-semibold">
                    <Icon :icon="Edit2" :size="16" />
                    <span>Edit Wilayah</span>
                </Button>
            </Link>
        </template>

        <div class="space-y-6">
            <div
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <Link
                    href="/admin/wilayah"
                    class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    <span>Kembali ke Daftar Wilayah</span>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Info (Read Only) -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-6"
                    >
                        <div
                            class="flex items-center gap-2 border-b border-border/60 pb-3"
                        >
                            <Icon :icon="Info" :size="18" class="text-brand" />
                            <h3 class="text-sm font-bold text-fg">
                                Profil dan Deskripsi
                            </h3>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Nama Wilayah</span
                                >
                                <span class="text-sm font-medium text-fg">{{
                                    region.name
                                }}</span>
                            </div>

                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Deskripsi</span
                                >
                                <p
                                    class="text-sm font-medium text-fg whitespace-pre-line"
                                >
                                    {{
                                        region.description ||
                                        "Belum ada deskripsi."
                                    }}
                                </p>
                            </div>

                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Potensi Pertanian</span
                                >
                                <p
                                    class="text-sm font-medium text-fg whitespace-pre-line"
                                >
                                    {{
                                        region.agricultural_potential ||
                                        "Belum ada data potensi."
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden"
                    >
                        <div class="p-6 border-b border-border/60">
                            <div class="flex items-center gap-2">
                                <Icon
                                    :icon="MapPin"
                                    :size="18"
                                    class="text-brand"
                                />
                                <h3 class="text-sm font-bold text-fg">
                                    Statistik
                                </h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Status</span
                                >
                                <Badge
                                    :variant="
                                        region.is_active ? 'success' : 'neutral'
                                    "
                                    class="gap-1 mt-1"
                                >
                                    <Icon
                                        :icon="
                                            region.is_active
                                                ? CheckCircle2
                                                : XCircle
                                        "
                                        :size="12"
                                    />
                                    <span>{{
                                        region.is_active
                                            ? "Aktif"
                                            : "Tidak Aktif"
                                    }}</span>
                                </Badge>
                            </div>

                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Luas Area</span
                                >
                                <span class="text-sm font-medium text-fg">{{
                                    region.area_km2
                                        ? `${region.area_km2} km²`
                                        : "-"
                                }}</span>
                            </div>

                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Populasi</span
                                >
                                <span class="text-sm font-medium text-fg">{{
                                    region.population
                                        ? `${region.population} Jiwa`
                                        : "-"
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
