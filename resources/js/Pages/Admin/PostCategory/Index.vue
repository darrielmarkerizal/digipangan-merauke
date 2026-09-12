<script setup lang="ts">
import { computed, ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    Edit2,
    Layers,
    Plus,
    Save,
    Search,
    Tag,
    Trash2,
    X,
} from "@lucide/vue";
import {
    AlertDialog,
    Badge,
    Button,
    EmptyState,
    Field,
    Icon,
    Input,
    Pagination,
} from "@/Components/ui";
import { toast } from "vue-sonner";
import { useSort } from "@/Composables/useSort";
import { formatTanggal } from "@/lib/format";

interface CategoryItem {
    id: number;
    name: string;
    slug: string;
    posts_count?: number;
    created_at?: string;
}

const props = defineProps<{
    categories?: {
        data: CategoryItem[];
        links?: any;
        meta?: any;
    };
}>();

const search = ref("");
const isModalOpen = ref(false);
const editingCategory = ref<CategoryItem | null>(null);
const { getSortDirection, sortBy } = useSort();

const categoryList = computed(() => {
    const rawData = props.categories?.data;
    const items = Array.isArray(rawData) ? rawData : (rawData as any)?.data || [];
    const query = search.value.trim().toLowerCase();

    return items.filter((category: CategoryItem) =>
        !query || category.name.toLowerCase().includes(query),
    );
});

const form = useForm({ name: "" });

const openCreateModal = () => {
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (category: CategoryItem) => {
    editingCategory.value = category;
    form.name = category.name;
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
};

const handleSubmit = () => {
    const options = {
        onSuccess: () => {
            toast.success(
                editingCategory.value
                    ? "Kategori berita berhasil diperbarui."
                    : "Kategori berita berhasil ditambahkan.",
            );
            closeModal();
        },
        onError: () => {
            toast.error(
                editingCategory.value
                    ? "Gagal memperbarui kategori berita."
                    : "Gagal menambahkan kategori berita.",
            );
        },
    };

    if (editingCategory.value) {
        form.put(`/admin/kategori-berita/${editingCategory.value.id}`, options);
    } else {
        form.post("/admin/kategori-berita", options);
    }
};

const executeDelete = (id: number) => {
    router.delete(`/admin/kategori-berita/${id}`, {
        onSuccess: () => toast.success("Kategori berita berhasil dihapus."),
        onError: () => toast.error("Kategori berita tidak dapat dihapus karena masih digunakan."),
    });
};
</script>

<template>
    <AdminLayout
        title="Kelola Kategori Berita"
        subtitle="Kelola konteks berita dan artikel yang tersedia pada portal publik."
    >
        <template #actions>
            <Button class="gap-1.5 font-semibold" @click="openCreateModal">
                <Icon :icon="Plus" :size="16" />
                <span>Tambah Kategori</span>
            </Button>
        </template>

        <div class="space-y-4">
            <div class="relative w-full max-w-md">
                <Icon
                    :icon="Search"
                    :size="16"
                    class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted"
                />
                <Input v-model="search" type="search" placeholder="Cari kategori berita..." class="w-full pl-10" />
            </div>

            <div class="overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-fg">
                        <thead class="border-b border-border/60 bg-muted/30 text-xs font-bold uppercase tracking-wider text-fg-muted">
                            <tr>
                                <th class="cursor-pointer px-5 py-3.5" @click="sortBy('name')">
                                    <span class="inline-flex items-center gap-1.5">
                                        Nama Kategori
                                        <Icon :icon="getSortDirection('name') === 'asc' ? ArrowUp : getSortDirection('name') === 'desc' ? ArrowDown : ArrowUpDown" :size="14" />
                                    </span>
                                </th>
                                <th class="px-4 py-3.5">Jumlah Berita</th>
                                <th class="px-4 py-3.5">Tanggal Dibuat</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-if="categoryList.length === 0">
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <EmptyState title="Tidak ada kategori ditemukan" description="Belum ada kategori berita yang sesuai." :icon="Tag" />
                                </td>
                            </tr>
                            <tr v-for="category in categoryList" :key="category.id" class="transition-colors hover:bg-muted/20">
                                <td class="px-5 py-4 font-bold">{{ category.name }}</td>
                                <td class="px-4 py-4">
                                    <Badge variant="brand" class="gap-1">
                                        <Icon :icon="Layers" :size="12" />
                                        <span>{{ category.posts_count ?? 0 }} Berita</span>
                                    </Badge>
                                </td>
                                <td class="px-4 py-4 text-xs text-fg-muted">{{ category.created_at ? formatTanggal(category.created_at) : "-" }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Button variant="secondary" size="sm" class="size-8 p-0" title="Edit Kategori" @click="openEditModal(category)">
                                            <Icon :icon="Edit2" :size="14" />
                                        </Button>
                                        <AlertDialog
                                            title="Hapus Kategori Berita?"
                                            :description="`Hapus kategori '${category.name}'? Kategori yang masih digunakan berita tidak dapat dihapus.`"
                                            confirm-label="Ya, Hapus Kategori"
                                            cancel-label="Batal"
                                            :destructive="true"
                                            @confirm="executeDelete(category.id)"
                                        >
                                            <template #trigger>
                                                <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg border border-danger/30 bg-danger-weak/40 text-danger shadow-xs transition-all hover:border-danger hover:bg-danger hover:text-white" title="Hapus Kategori">
                                                    <Icon :icon="Trash2" :size="14" />
                                                </button>
                                            </template>
                                        </AlertDialog>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination :links="categories?.links" :meta="categories?.meta" />
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-fg/50 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md overflow-hidden rounded-card border border-border bg-card shadow-soft">
                <div class="flex items-center justify-between border-b border-border px-6 py-4">
                    <h3 class="text-base font-bold">{{ editingCategory ? "Edit Kategori Berita" : "Tambah Kategori Berita" }}</h3>
                    <button type="button" class="rounded-lg p-1 text-fg-muted hover:bg-muted hover:text-fg" @click="closeModal" aria-label="Tutup">
                        <Icon :icon="X" :size="18" />
                    </button>
                </div>
                <form class="space-y-4 p-6" @submit.prevent="handleSubmit">
                    <Field label="Nama Kategori Berita" :error="form.errors.name" required>
                        <Input v-model="form.name" placeholder="Contoh: Program Pemerintah" required />
                    </Field>
                    <div class="flex justify-end gap-3 pt-2">
                        <Button type="button" variant="secondary" @click="closeModal">Batal</Button>
                        <Button type="submit" :loading="form.processing" class="gap-1.5">
                            <Icon :icon="Save" :size="16" />
                            <span>Simpan</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
