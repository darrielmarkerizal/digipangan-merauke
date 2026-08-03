<script setup lang="ts">
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {
    Plus,
    Search,
    Edit2,
    Trash2,
    HelpCircle,
    ArrowUp,
    ArrowDown,
    ArrowUpDown,
    ToggleLeft,
    ToggleRight,
} from "@lucide/vue";
import { Icon, Button, Badge, AlertDialog, EmptyState, Pagination } from "@/Components/ui";
import { Link } from "@inertiajs/vue3";
import { useSearch } from "@/Composables/useSearch";
import { useSort } from "@/Composables/useSort";
import { formatTanggal } from "@/lib/format";
import { toast } from "vue-sonner";

const props = defineProps<{
    faqs?: {
        data: any[];
        links?: any;
        meta?: any;
    };
}>();

const { search } = useSearch();
const { getSortDirection, sortBy } = useSort();

const faqList = computed(() => {
    const rawData = props.faqs?.data;
    const items = Array.isArray(rawData) ? rawData : (rawData as any)?.data || [];
    return items;
});

const executeDelete = (id: number) => {
    router.delete(`/admin/faq/${id}`, {
        preserveScroll: true,
        onSuccess: () => toast.success("FAQ berhasil dihapus!"),
        onError: () => toast.error("Gagal menghapus FAQ."),
    });
};
</script>

<template>
    <AdminLayout title="Pusat Bantuan (FAQ)" subtitle="Kelola daftar pertanyaan yang sering diajukan oleh pengguna.">
        <template #actions>
            <Link href="/admin/faq/tambah">
                <Button class="gap-2 font-semibold shadow-sm">
                    <Icon :icon="Plus" :size="16" />
                    <span>Tambah FAQ</span>
                </Button>
            </Link>
        </template>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="relative flex-1 max-w-sm">
                    <Icon
                        :icon="Search"
                        :size="16"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted pointer-events-none"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari pertanyaan atau jawaban..."
                        class="w-full h-10 pl-10 pr-4 rounded-xl border border-border/80 bg-white text-sm text-fg placeholder:text-fg-muted/60 transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
                    />
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-fg">
                        <thead class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-5 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                                    @click="sortBy('sort_order')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>No</span>
                                        <Icon
                                            :icon="getSortDirection('sort_order') === 'asc' ? ArrowUp : getSortDirection('sort_order') === 'desc' ? ArrowDown : ArrowUpDown"
                                            :size="14"
                                            :class="getSortDirection('sort_order') ? 'text-brand' : 'text-fg-muted/40 group-hover:text-fg-muted'"
                                        />
                                    </div>
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                                    @click="sortBy('question')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>Pertanyaan</span>
                                        <Icon
                                            :icon="getSortDirection('question') === 'asc' ? ArrowUp : getSortDirection('question') === 'desc' ? ArrowDown : ArrowUpDown"
                                            :size="14"
                                            :class="getSortDirection('question') ? 'text-brand' : 'text-fg-muted/40 group-hover:text-fg-muted'"
                                        />
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3.5">Status</th>
                                <th
                                    scope="col"
                                    class="px-4 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                                    @click="sortBy('created_at')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>Dibuat</span>
                                        <Icon
                                            :icon="getSortDirection('created_at') === 'asc' ? ArrowUp : getSortDirection('created_at') === 'desc' ? ArrowDown : ArrowUpDown"
                                            :size="14"
                                            :class="getSortDirection('created_at') ? 'text-brand' : 'text-fg-muted/40 group-hover:text-fg-muted'"
                                        />
                                    </div>
                                </th>
                                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-if="faqList.length === 0">
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <EmptyState
                                        title="Belum ada FAQ"
                                        description="Tambah pertanyaan & jawaban yang sering ditanyakan oleh pengguna."
                                        :icon="HelpCircle"
                                    />
                                </td>
                            </tr>
                            <tr
                                v-for="(item, index) in faqList"
                                :key="item.id"
                                class="transition-colors hover:bg-muted/20"
                            >
                                <td class="px-5 py-4 text-fg-muted font-mono text-xs w-12">
                                    {{ item.sort_order ?? index + 1 }}
                                </td>
                                <td class="px-4 py-4 max-w-md">
                                    <p class="font-semibold text-fg line-clamp-1">{{ item.question }}</p>
                                    <p class="text-xs text-fg-muted mt-0.5 line-clamp-2 leading-relaxed">{{ item.answer }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <Badge :variant="item.is_active ? 'success' : 'neutral'" class="gap-1">
                                        <Icon :icon="item.is_active ? ToggleRight : ToggleLeft" :size="12" />
                                        {{ item.is_active ? "Aktif" : "Nonaktif" }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs text-fg-muted font-medium whitespace-nowrap">
                                        {{ formatTanggal(item.created_at) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Link :href="`/admin/faq/${item.id}/edit`">
                                            <Button variant="secondary" size="sm" class="size-8 p-0" title="Edit FAQ">
                                                <Icon :icon="Edit2" :size="14" />
                                            </Button>
                                        </Link>
                                        <AlertDialog
                                            title="Hapus FAQ?"
                                            :description="`Apakah Anda yakin ingin menghapus FAQ ini? Tindakan ini tidak dapat dibatalkan.`"
                                            confirm-label="Ya, Hapus"
                                            cancel-label="Batal"
                                            :destructive="true"
                                            @confirm="executeDelete(item.id)"
                                        >
                                            <template #trigger>
                                                <Button variant="danger-outline" size="sm" class="size-8 p-0" title="Hapus FAQ">
                                                    <Icon :icon="Trash2" :size="14" />
                                                </Button>
                                            </template>
                                        </AlertDialog>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination :meta="faqs?.meta" :links="faqs?.links" />
        </div>
    </AdminLayout>
</template>
