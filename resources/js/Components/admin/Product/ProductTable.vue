<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import {
    Edit2,
    Trash2,
    Package,
    ArrowUp,
    ArrowDown,
    ArrowUpDown,
    Eye,
    Image as ImageIcon,
} from "@lucide/vue";
import {
    Icon,
    Badge,
    Button,
    EmptyState,
    AlertDialog,
} from "@/Components/ui";
import { toast } from "vue-sonner";

const props = defineProps<{
    productList: any[];
    sortBy: (column: string) => void;
    getSortDirection: (column: string) => "asc" | "desc" | null;
}>();

const executeDelete = (id: number) => {
    router.delete(`/admin/produk/${id}`, {
        onSuccess: () => {
            toast.success("Produk berhasil dihapus.");
        },
        onError: () => {
            toast.error("Gagal menghapus produk.");
        },
    });
};
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-fg">
            <thead
                class="border-b border-border/80 bg-muted/40 text-xs font-bold uppercase tracking-wider text-fg-muted select-none"
            >
                <tr>
                    <th
                        scope="col"
                        class="px-5 py-3.5 cursor-pointer hover:text-fg transition-colors group"
                        title="Klik untuk mengurutkan berdasarkan nama produk"
                        @click="sortBy('name')"
                    >
                        <div class="flex items-center gap-1.5">
                            <span>Produk Pangan</span>
                            <Icon
                                v-if="
                                    getSortDirection('name') ===
                                    'asc'
                                "
                                :icon="ArrowUp"
                                :size="14"
                                class="text-brand font-bold"
                            />
                            <Icon
                                v-else-if="
                                    getSortDirection('name') ===
                                    'desc'
                                "
                                :icon="ArrowDown"
                                :size="14"
                                class="text-brand font-bold"
                            />
                            <Icon
                                v-else
                                :icon="ArrowUpDown"
                                :size="14"
                                class="opacity-40 group-hover:opacity-100 transition-opacity"
                            />
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3.5">
                        Kategori
                    </th>
                    <th scope="col" class="px-4 py-3.5">
                        Petani / Kawasan
                    </th>
                    <th
                        scope="col"
                        class="px-4 py-3.5 cursor-pointer hover:text-fg transition-colors group"
                        title="Klik untuk mengurutkan berdasarkan harga"
                        @click="sortBy('price')"
                    >
                        <div class="flex items-center gap-1.5">
                            <span>Harga &amp; Satuan</span>
                            <Icon
                                v-if="
                                    getSortDirection('price') ===
                                    'asc'
                                "
                                :icon="ArrowUp"
                                :size="14"
                                class="text-brand font-bold"
                            />
                            <Icon
                                v-else-if="
                                    getSortDirection('price') ===
                                    'desc'
                                "
                                :icon="ArrowDown"
                                :size="14"
                                class="text-brand font-bold"
                            />
                            <Icon
                                v-else
                                :icon="ArrowUpDown"
                                :size="14"
                                class="opacity-40 group-hover:opacity-100 transition-opacity"
                            />
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3.5">Status</th>
                    <th scope="col" class="px-5 py-3.5 text-right">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
                <tr v-if="productList.length === 0">
                    <td colspan="6" class="px-5 py-12 text-center">
                        <EmptyState
                            title="Tidak ada produk ditemukan"
                            description="Belum ada data produk pangan yang sesuai dengan pencarian atau filter yang dipilih."
                            :icon="Package"
                        />
                    </td>
                </tr>
                <tr
                    v-for="item in productList"
                    :key="item.id"
                    class="transition-colors hover:bg-muted/20"
                >
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                v-if="item.image_url"
                                class="size-11 shrink-0 rounded-xl overflow-hidden border border-border/60"
                            >
                                <img
                                    :src="item.image_url"
                                    :alt="item.name"
                                    class="size-full object-cover"
                                />
                            </div>
                            <div
                                v-else
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-border/60 bg-muted/60 text-fg-muted"
                                title="Tidak ada gambar"
                            >
                                <Icon
                                    :icon="ImageIcon"
                                    :size="18"
                                />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="font-bold text-fg truncate leading-snug"
                                >
                                    {{ item.name }}
                                </p>
                                <div
                                    class="flex items-center gap-1.5 mt-0.5"
                                >
                                    <span
                                        v-if="item.is_featured"
                                        class="rounded bg-accent-weak px-1.5 py-0.2 text-[10px] font-bold text-accent"
                                    >
                                        Unggulan
                                    </span>
                                    <span
                                        class="text-[11px] text-fg-muted"
                                        >ID: #PRD-00{{
                                            item.id
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </td>
                    <td
                        class="px-4 py-4 text-xs font-medium text-fg-muted"
                    >
                        {{ item.category }}
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-xs font-semibold text-fg">
                            {{ item.farmer }}
                        </p>
                        <p class="text-[11px] text-fg-muted">
                            {{ item.region }}
                        </p>
                    </td>
                    <td class="px-4 py-4">
                        <p
                            class="text-xs font-extrabold text-brand"
                        >
                            {{ item.price }}
                        </p>
                        <p class="text-[11px] text-fg-muted">
                            {{ item.unit }}
                        </p>
                    </td>
                    <td class="px-4 py-4">
                        <Badge
                            :variant="
                                item.stock_available
                                    ? 'success'
                                    : 'danger'
                            "
                        >
                            {{
                                item.stock_available
                                    ? "Stok Tersedia"
                                    : "Habis"
                            }}
                        </Badge>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div
                            class="flex items-center justify-end gap-1.5"
                        >
                            <Link
                                :href="`/admin/produk/${item.id}`"
                            >
                                <Button
                                    variant="secondary"
                                    size="sm"
                                    class="size-8 p-0"
                                    title="Lihat Detail Produk"
                                >
                                    <Icon :icon="Eye" :size="14" />
                                </Button>
                            </Link>
                            <Link
                                :href="`/admin/produk/${item.id}/edit`"
                            >
                                <Button
                                    variant="secondary"
                                    size="sm"
                                    class="size-8 p-0"
                                    title="Edit Produk"
                                >
                                    <Icon
                                        :icon="Edit2"
                                        :size="14"
                                    />
                                </Button>
                            </Link>
                            <AlertDialog
                                title="Hapus Produk Pangan?"
                                :description="`Apakah Anda yakin ingin menghapus produk '${item.name}'? Tindakan ini tidak dapat dibatalkan.`"
                                confirm-label="Ya, Hapus Produk"
                                cancel-label="Batal"
                                :destructive="true"
                                @confirm="executeDelete(item.id)"
                            >
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex size-8 items-center justify-center rounded-lg border border-danger/30 bg-danger-weak/40 text-danger transition-all hover:bg-danger hover:text-white hover:border-danger shadow-xs cursor-pointer"
                                        title="Hapus Produk"
                                    >
                                        <Icon
                                            :icon="Trash2"
                                            :size="14"
                                        />
                                    </button>
                                </template>
                            </AlertDialog>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
