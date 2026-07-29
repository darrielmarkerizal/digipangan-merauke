<script setup lang="ts">
import { ref, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminPageCard from "@/Components/admin/AdminPageCard.vue";
import FilterPanel from "@/Components/admin/FilterPanel.vue";
import {
    Search,
    Plus,
    Trash2,
    Edit2,
    FileText,
    CheckCircle2,
    FileEdit,
    Eye,
} from "@lucide/vue";
import { Icon, Button, Badge, AlertDialog, EmptyState } from "@/Components/ui";
import { useSearch } from "@/Composables/useSearch";
import { useSort } from "@/Composables/useSort";
import { formatTanggal } from "@/lib/format";
import { toast } from "vue-sonner";
import { Pagination } from "@/Components/ui";
import { ArrowUp, ArrowDown, ArrowUpDown } from "@lucide/vue";

const props = defineProps<{
    posts?: {
        data: any[];
        links?: any;
        meta?: any;
    };
    authors?: { id: number; name: string }[];
}>();

const { search } = useSearch();
const { getSortDirection, sortBy } = useSort();
const isDeleting = ref(false);

const postList = computed(() => {
    const rawData = props.posts?.data;
    const items = Array.isArray(rawData)
        ? rawData
        : (rawData as any)?.data || [];
    return items;
});

const executeDelete = (id: number) => {
    isDeleting.value = true;
    router.delete(`/admin/berita/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Berita berhasil dihapus!");
        },
        onError: () => {
            toast.error("Gagal menghapus berita.");
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>

<template>
    <AdminLayout
        title="Kelola Berita & Artikel"
        subtitle="Manajemen publikasi konten, berita, dan pelatihan."
    >
        <template #actions>
            <Link href="/admin/berita/tambah">
                <Button class="gap-2 font-semibold shadow-sm">
                    <Icon :icon="Plus" :size="16" />
                    <span>Tulis Berita Baru</span>
                </Button>
            </Link>
        </template>

        <div class="space-y-4">
            <div
                class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3"
            >
                <div class="relative flex-1 max-w-sm">
                    <Icon
                        :icon="Search"
                        :size="16"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted pointer-events-none"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari judul atau konten..."
                        class="w-full h-10 pl-10 pr-4 rounded-xl border border-border/80 bg-white text-sm text-fg placeholder:text-fg-muted/60 transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
                    />
                </div>

                <FilterPanel module="post" :authors="authors as any" />
            </div>

            <AdminPageCard>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-fg">
                        <thead
                            class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider"
                        >
                            <tr>
                                <th
                                    scope="col"
                                    class="px-5 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                                    @click="sortBy('title')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>Judul Berita</span>
                                        <Icon
                                            :icon="
                                                getSortDirection('title') ===
                                                'asc'
                                                    ? ArrowUp
                                                    : getSortDirection(
                                                            'title',
                                                        ) === 'desc'
                                                      ? ArrowDown
                                                      : ArrowUpDown
                                            "
                                            :size="14"
                                            :class="
                                                getSortDirection('title')
                                                    ? 'text-brand'
                                                    : 'text-fg-muted/40 group-hover:text-fg-muted'
                                            "
                                        />
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3.5">
                                    Kategori
                                </th>
                                <th scope="col" class="px-4 py-3.5">Penulis</th>
                                <th
                                    scope="col"
                                    class="px-4 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                                    @click="sortBy('status')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>Status Publikasi</span>
                                        <Icon
                                            :icon="
                                                getSortDirection('status') ===
                                                'asc'
                                                    ? ArrowUp
                                                    : getSortDirection(
                                                            'status',
                                                        ) === 'desc'
                                                      ? ArrowDown
                                                      : ArrowUpDown
                                            "
                                            :size="14"
                                            :class="
                                                getSortDirection('status')
                                                    ? 'text-brand'
                                                    : 'text-fg-muted/40 group-hover:text-fg-muted'
                                            "
                                        />
                                    </div>
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                                    @click="sortBy('published_at')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>Tanggal</span>
                                        <Icon
                                            :icon="
                                                getSortDirection(
                                                    'published_at',
                                                ) === 'asc'
                                                    ? ArrowUp
                                                    : getSortDirection(
                                                            'published_at',
                                                        ) === 'desc'
                                                      ? ArrowDown
                                                      : ArrowUpDown
                                            "
                                            :size="14"
                                            :class="
                                                getSortDirection('published_at')
                                                    ? 'text-brand'
                                                    : 'text-fg-muted/40 group-hover:text-fg-muted'
                                            "
                                        />
                                    </div>
                                </th>
                                <th scope="col" class="px-5 py-3.5 text-right">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-if="postList.length === 0">
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <EmptyState
                                        title="Tidak ada berita ditemukan"
                                        description="Belum ada publikasi yang sesuai dengan pencarian Anda."
                                        :icon="FileText"
                                    />
                                </td>
                            </tr>
                            <tr
                                v-for="item in postList"
                                :key="item.id"
                                class="transition-colors hover:bg-muted/20"
                            >
                                <td
                                    class="px-5 py-4 font-bold text-fg max-w-xs truncate"
                                >
                                    {{ item.title }}
                                </td>
                                <td class="px-4 py-4 text-fg-muted font-medium">
                                    {{ item.category?.name || "-" }}
                                </td>
                                <td class="px-4 py-4 text-fg-muted">
                                    {{ item.author?.name || "-" }}
                                </td>
                                <td class="px-4 py-4">
                                    <Badge
                                        :variant="
                                            item.status === 'published'
                                                ? 'success'
                                                : 'neutral'
                                        "
                                        class="gap-1"
                                    >
                                        <Icon
                                            v-if="item.status === 'published'"
                                            :icon="CheckCircle2"
                                            :size="12"
                                        />
                                        <Icon
                                            v-else
                                            :icon="FileEdit"
                                            :size="12"
                                        />
                                        <span class="capitalize">{{
                                            item.status
                                        }}</span>
                                    </Badge>
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        v-if="
                                            item.status === 'published' &&
                                            item.published_at
                                        "
                                        class="text-xs text-fg-muted font-medium whitespace-nowrap"
                                    >
                                        {{ formatTanggal(item.published_at) }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-xs text-fg-muted/60 italic"
                                    >
                                        Belum terbit
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div
                                        class="flex items-center justify-end gap-1.5"
                                    >
                                        <Link
                                            :href="`/admin/berita/${item.id}`"
                                        >
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                class="size-8 p-0"
                                                title="Lihat Berita"
                                            >
                                                <Icon :icon="Eye" :size="14" />
                                            </Button>
                                        </Link>
                                        <Link
                                            :href="`/admin/berita/${item.id}/edit`"
                                        >
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                class="size-8 p-0"
                                                title="Edit Berita"
                                            >
                                                <Icon
                                                    :icon="Edit2"
                                                    :size="14"
                                                />
                                            </Button>
                                        </Link>
                                        <AlertDialog
                                            title="Hapus Berita?"
                                            :description="`Apakah Anda yakin ingin menghapus berita '${item.title}'? Tindakan ini tidak dapat dibatalkan.`"
                                            confirm-label="Ya, Hapus"
                                            cancel-label="Batal"
                                            :destructive="true"
                                            @confirm="executeDelete(item.id)"
                                        >
                                            <template #trigger>
                                                <Button
                                                    variant="danger-outline"
                                                    size="sm"
                                                    class="size-8 p-0"
                                                    title="Hapus Berita"
                                                >
                                                    <Icon
                                                        :icon="Trash2"
                                                        :size="14"
                                                    />
                                                </Button>
                                            </template>
                                        </AlertDialog>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </AdminPageCard>

            <!-- Pagination -->
            <div v-if="posts?.meta && posts.meta.last_page > 0" class="mt-4">
                <Pagination :meta="posts.meta" />
            </div>
        </div>
    </AdminLayout>
</template>
