<script setup lang="ts">
import { ref } from "vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ArrowLeft, Save, Users, MapPin, Plus, Trash2, X } from "@lucide/vue";
import {
    Button,
    Field,
    Input,
    Select,
    Icon,
    Badge,
    EmptyState,
    AlertDialog,
} from "@/Components/ui";
import { toast } from "vue-sonner";

const props = defineProps<{
    farmerGroup: any;
    regions: any[];
    members: any[];
    availableFarmers: any[];
}>();

const form = useForm({
    name: props.farmerGroup.name,
    region_id: props.farmerGroup.region_id,
});

const handleSubmit = () => {
    form.submit("put", `/admin/kelompok-tani/${props.farmerGroup.id}`, {
        onSuccess: () => {
            toast.success("Kelompok tani berhasil diperbarui.");
        },
        onError: () => {
            toast.error(
                "Gagal memperbarui kelompok tani. Periksa kembali isian Anda.",
            );
        },
    });
};

const isAddMemberModalOpen = ref(false);
const attachForm = useForm({
    farmer_id: "",
});

const openAddMemberModal = () => {
    attachForm.reset();
    attachForm.clearErrors();
    isAddMemberModalOpen.value = true;
};

const closeAddMemberModal = () => {
    isAddMemberModalOpen.value = false;
};

const handleAddMember = () => {
    attachForm.post(
        `/admin/kelompok-tani/${props.farmerGroup.id}/tambah-petani`,
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success("Petani berhasil ditambahkan ke kelompok.");
                closeAddMemberModal();
            },
            onError: () => {
                toast.error("Gagal menambahkan petani.");
            },
        },
    );
};

const handleRemoveMember = (farmerId: number) => {
    router.post(
        `/admin/kelompok-tani/${props.farmerGroup.id}/keluarkan-petani`,
        {
            farmer_id: farmerId,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success("Petani berhasil dikeluarkan dari kelompok.");
            },
            onError: () => {
                toast.error("Gagal mengeluarkan petani.");
            },
        },
    );
};
</script>

<template>
    <AdminLayout
        :title="`Edit Kelompok Tani: ${farmerGroup.name}`"
        subtitle="Perbarui profil kelompok tani dan kelola daftar anggota petani yang tergabung di dalamnya."
    >
        <div class="space-y-6">
            <div
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <Link
                    href="/admin/kelompok-tani"
                    class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    <span>Kembali ke Daftar Kelompok Tani</span>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 space-y-6">
                    <form @submit.prevent="handleSubmit">
                        <div
                            class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-6"
                        >
                            <div
                                class="flex items-center gap-2 border-b border-border/60 pb-3"
                            >
                                <Icon
                                    :icon="MapPin"
                                    :size="18"
                                    class="text-brand"
                                />
                                <h3 class="text-sm font-bold text-fg">
                                    Informasi Kelompok Tani
                                </h3>
                            </div>

                            <div class="space-y-5">
                                <Field
                                    label="Distrik / Kawasan"
                                    :error="form.errors.region_id"
                                    required
                                >
                                    <Select v-model="form.region_id" required>
                                        <option value="">
                                            Pilih Distrik...
                                        </option>
                                        <option
                                            v-for="r in regions"
                                            :key="r.id"
                                            :value="r.id"
                                        >
                                            {{ r.name }}
                                        </option>
                                    </Select>
                                    <p class="text-[11px] text-fg-muted mt-1.5">
                                        Mengubah distrik akan memengaruhi daftar
                                        calon anggota yang bisa ditambahkan.
                                    </p>
                                </Field>

                                <Field
                                    label="Nama Kelompok Tani"
                                    :error="form.errors.name"
                                    required
                                >
                                    <Input
                                        v-model="form.name"
                                        placeholder="Contoh: Kelompok Tani Elikobel"
                                        required
                                    />
                                </Field>
                            </div>

                            <div
                                class="flex items-center justify-end gap-3 pt-4 border-t border-border/60"
                            >
                                <Button
                                    type="submit"
                                    size="sm"
                                    :loading="form.processing"
                                    class="gap-1.5 font-semibold w-full justify-center"
                                >
                                    <Icon :icon="Save" :size="16" />
                                    <span>Simpan Perubahan</span>
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden"
                    >
                        <div
                            class="p-6 border-b border-border/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-10 items-center justify-center rounded-xl bg-brand-weak/50 text-brand"
                                >
                                    <Icon :icon="Users" :size="20" />
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-fg">
                                        Daftar Anggota Petani
                                    </h3>
                                    <p class="text-xs text-fg-muted">
                                        Petani yang tergabung di kelompok tani
                                        ini.
                                    </p>
                                </div>
                            </div>
                            <Button
                                size="sm"
                                variant="secondary"
                                class="gap-1.5 font-semibold"
                                @click="openAddMemberModal"
                            >
                                <Icon :icon="Plus" :size="15" />
                                <span>Tambahkan Anggota</span>
                            </Button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-fg">
                                <thead
                                    class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider"
                                >
                                    <tr>
                                        <th scope="col" class="px-6 py-3.5">
                                            Nama Petani
                                        </th>
                                        <th scope="col" class="px-6 py-3.5">
                                            No. Telepon
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3.5 text-right"
                                        >
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border/60">
                                    <tr v-if="members.length === 0">
                                        <td
                                            colspan="3"
                                            class="px-6 py-12 text-center"
                                        >
                                            <EmptyState
                                                title="Belum ada anggota"
                                                description="Kelompok tani ini belum memiliki anggota. Klik 'Tambahkan Anggota' untuk memasukkan petani yang ada di distrik ini."
                                                :icon="Users"
                                            />
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="member in members"
                                        :key="member.id"
                                        class="hover:bg-muted/20 transition-colors"
                                    >
                                        <td class="px-6 py-4">
                                            <Link
                                                :href="`/admin/petani/${member.id}`"
                                                class="font-bold text-fg hover:text-brand hover:underline transition-colors block"
                                            >
                                                {{ member.name }}
                                            </Link>
                                            <div
                                                class="text-xs text-fg-muted mt-0.5"
                                            >
                                                ID: #PTN-00{{ member.id }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 font-medium text-fg-muted"
                                        >
                                            {{ member.phone }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <AlertDialog
                                                title="Keluarkan Petani?"
                                                :description="`Apakah Anda yakin ingin mengeluarkan '${member.name}' dari kelompok tani ini? Status petani akan menjadi Mandiri (tanpa kelompok).`"
                                                confirm-label="Ya, Keluarkan"
                                                cancel-label="Batal"
                                                :destructive="true"
                                                @confirm="
                                                    handleRemoveMember(
                                                        member.id,
                                                    )
                                                "
                                            >
                                                <template #trigger>
                                                    <Button
                                                        size="sm"
                                                        variant="danger-secondary"
                                                        class="gap-1 font-semibold"
                                                    >
                                                        <Icon
                                                            :icon="Trash2"
                                                            :size="14"
                                                        />
                                                        <span>Keluarkan</span>
                                                    </Button>
                                                </template>
                                            </AlertDialog>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="isAddMemberModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-fg/50 backdrop-blur-xs transition-opacity"
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-card border border-border bg-card shadow-soft animate-in fade-in zoom-in-95 duration-200"
            >
                <div
                    class="flex items-center justify-between border-b border-border px-6 py-4"
                >
                    <h3 class="text-base font-bold text-fg">
                        Tambahkan Anggota Petani
                    </h3>
                    <button
                        type="button"
                        class="rounded-lg p-1 text-fg-muted hover:bg-muted hover:text-fg transition-colors"
                        @click="closeAddMemberModal"
                    >
                        <Icon :icon="X" :size="18" />
                    </button>
                </div>

                <form @submit.prevent="handleAddMember" class="p-6 space-y-5">
                    <div
                        v-if="availableFarmers.length === 0"
                        class="py-4 text-center"
                    >
                        <EmptyState
                            title="Tidak ada calon anggota"
                            description="Tidak ditemukan petani Mandiri (tanpa kelompok) di distrik ini. Anda harus mendaftarkan petani baru terlebih dahulu."
                            :icon="Users"
                        />
                    </div>
                    <div v-else class="space-y-4">
                        <Field
                            label="Pilih Petani"
                            :error="attachForm.errors.farmer_id"
                            required
                        >
                            <Select v-model="attachForm.farmer_id" required>
                                <option value="">
                                    Pilih petani yang akan ditambahkan...
                                </option>
                                <option
                                    v-for="farmer in availableFarmers"
                                    :key="farmer.id"
                                    :value="farmer.id"
                                >
                                    {{ farmer.name }} ({{ farmer.phone }})
                                </option>
                            </Select>
                            <p class="text-[11px] text-fg-muted mt-1.5">
                                Hanya menampilkan petani yang berada di distrik
                                yang sama dan belum memiliki kelompok tani.
                            </p>
                        </Field>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <Button
                            type="button"
                            variant="secondary"
                            @click="closeAddMemberModal"
                        >
                            Tutup
                        </Button>
                        <Button
                            v-if="availableFarmers.length > 0"
                            type="submit"
                            :loading="attachForm.processing"
                            class="gap-1.5"
                        >
                            <Icon :icon="Plus" :size="16" />
                            <span>Tambahkan ke Kelompok</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
