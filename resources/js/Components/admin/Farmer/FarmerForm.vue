<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from "axios";
import {
    ArrowLeft,
    Save,
    Check,
    Upload,
    User,
    MapPin,
    Sprout,
} from "@lucide/vue";
import { toast } from "vue-sonner";
import { Button, Field, Input, Select, Switch, Icon } from "@/Components/ui";

const props = defineProps<{
    initialData?: any;
    isEdit?: boolean;
    regions?: any[];
    villages?: any[];
    farmerGroups?: any[];
    commodities?: any[];
}>();

const form = useForm({
    name: props.initialData?.name || "",
    phone: props.initialData?.phone || "",
    land_area_ha: props.initialData?.land_area_ha ?? "",
    is_active: props.initialData?.is_active ?? true,
    region_id:
        props.initialData?.region?.id ?? (props.initialData?.region_id || ""),
    village_id:
        props.initialData?.village?.id ?? (props.initialData?.village_id || ""),
    farmer_group_id:
        props.initialData?.farmer_group?.id ??
        (props.initialData?.farmer_group_id || ""),
    commodities: props.initialData?.commodities?.map((c: any) => c.id) || [],
    photo: null as string | null,
});

const isUploadingPhoto = ref(false);
const photoPreviewUrl = ref(
    props.initialData?.photo?.thumb || props.initialData?.photo?.original || "",
);
const fileInputRef = ref<HTMLInputElement | null>(null);

const filteredVillages = computed(() => {
    if (!form.region_id) return props.villages || [];
    return (props.villages || []).filter(
        (v: any) => Number(v.region_id) === Number(form.region_id),
    );
});

const filteredFarmerGroups = computed(() => {
    if (!form.region_id) return props.farmerGroups || [];
    return (props.farmerGroups || []).filter(
        (g: any) => Number(g.region_id) === Number(form.region_id),
    );
});

watch(
    () => form.region_id,
    (newRegionId, oldRegionId) => {
        if (oldRegionId !== undefined && newRegionId !== oldRegionId) {
            const villageExists = filteredVillages.value.some(
                (v: any) => Number(v.id) === Number(form.village_id),
            );
            if (!villageExists) form.village_id = "";
            const groupExists = filteredFarmerGroups.value.some(
                (g: any) => Number(g.id) === Number(form.farmer_group_id),
            );
            if (!groupExists) form.farmer_group_id = "";
        }
    },
);

const toggleCommodity = (id: number) => {
    const idx = form.commodities.indexOf(id);
    if (idx === -1) {
        form.commodities.push(id);
    } else {
        form.commodities.splice(idx, 1);
    }
};

const triggerFileInput = () => {
    fileInputRef.value?.click();
};

const handlePhotoChange = async (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    isUploadingPhoto.value = true;
    try {
        const formData = new FormData();
        formData.append("file", file);
        const res = await axios.post("/admin/media/upload", formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        form.photo = res.data.folder;
        photoPreviewUrl.value = URL.createObjectURL(file);
        toast.success("Foto profil berhasil diunggah.");
    } catch (error) {
        toast.error("Gagal mengunggah foto.");
    } finally {
        isUploadingPhoto.value = false;
    }
};

const removePhoto = () => {
    form.photo = null;
    photoPreviewUrl.value = "";
    if (fileInputRef.value) {
        fileInputRef.value.value = "";
    }
};

const handleSubmit = () => {
    form.transform((data) => ({
        ...data,
        village_id: data.village_id === "" ? null : data.village_id,
        farmer_group_id:
            data.farmer_group_id === "" ? null : data.farmer_group_id,
        land_area_ha:
            data.land_area_ha === "" || data.land_area_ha === null
                ? null
                : Number(data.land_area_ha),
        photo: data.photo || null,
    }));

    if (props.isEdit && props.initialData?.id) {
        form.submit("put", `/admin/petani/${props.initialData.id}`, {
            onSuccess: () => {
                toast.success("Profil petani berhasil diperbarui.");
            },
            onError: () => {
                toast.error(
                    "Gagal memperbarui data petani. Periksa kembali formulir.",
                );
            },
        });
    } else {
        form.post("/admin/petani", {
            onSuccess: () => {
                toast.success("Petani baru berhasil ditambahkan.");
            },
            onError: () => {
                toast.error(
                    "Gagal menambahkan petani baru. Periksa kembali formulir.",
                );
            },
        });
    }
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-border/60 pb-5"
        >
            <div class="flex items-center gap-3">
                <Link
                    href="/admin/petani"
                    class="inline-flex size-10 items-center justify-center rounded-xl border border-border bg-white text-fg-muted hover:bg-muted hover:text-fg transition-colors shrink-0"
                    title="Kembali ke Direktori Petani"
                >
                    <Icon :icon="ArrowLeft" :size="18" />
                </Link>
                <div>
                    <h2 class="text-lg font-bold text-fg">
                        {{
                            isEdit ? "Edit Profil Petani" : "Tambah Petani Baru"
                        }}
                    </h2>
                    <p class="text-xs text-fg-muted">
                        {{
                            isEdit
                                ? "Perbarui identitas, nomor kontak WhatsApp, distrik, dan komoditas budidaya petani."
                                : "Lengkapi identitas profil petani baru dan tentukan komoditas budidayanya."
                        }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-center">
                <Link href="/admin/petani">
                    <Button type="button" variant="secondary" size="sm">
                        Batal
                    </Button>
                </Link>
                <Button
                    type="submit"
                    size="sm"
                    :loading="form.processing || isUploadingPhoto"
                    class="gap-1.5 font-semibold"
                >
                    <Icon :icon="Save" :size="16" />
                    <span>{{
                        isEdit ? "Simpan Perubahan" : "Simpan Petani"
                    }}</span>
                </Button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5"
                >
                    <div
                        class="flex items-center gap-2 border-b border-border/60 pb-3"
                    >
                        <Icon :icon="User" :size="18" class="text-brand" />
                        <h3 class="text-sm font-bold text-fg">
                            Informasi Petani / Mitra
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Field
                            label="Nama Petani / Mitra"
                            :error="form.errors.name"
                            required
                        >
                            <Input
                                v-model="form.name"
                                placeholder="Contoh: Pak Budi Santoso"
                                required
                            />
                        </Field>

                        <Field
                            label="No. WhatsApp / Telepon"
                            :error="form.errors.phone"
                            required
                        >
                            <Input
                                v-model="form.phone"
                                placeholder="Contoh: 081234567890"
                                required
                            />
                        </Field>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Field
                            label="Luas Lahan (Ha)"
                            :error="form.errors.land_area_ha"
                        >
                            <Input
                                v-model="form.land_area_ha"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="Contoh: 2.5"
                            />
                        </Field>

                        <Field
                            label="Status Publikasi"
                            :error="form.errors.is_active"
                        >
                            <div class="flex items-center gap-3 pt-2">
                                <Switch v-model="form.is_active" />
                                <span
                                    class="text-sm text-fg cursor-pointer select-none"
                                    @click="form.is_active = !form.is_active"
                                >
                                    {{
                                        form.is_active
                                            ? "Aktif (Ditampilkan)"
                                            : "Nonaktif (Disembunyikan)"
                                    }}
                                </span>
                            </div>
                        </Field>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5"
                >
                    <div
                        class="flex items-center gap-2 border-b border-border/60 pb-3"
                    >
                        <Icon :icon="MapPin" :size="18" class="text-brand" />
                        <h3 class="text-sm font-bold text-fg">
                            Kawasan Transmigrasi &amp; Kelompok Tani
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <Field
                            label="Distrik / Kawasan"
                            :error="form.errors.region_id"
                            required
                        >
                            <Select v-model="form.region_id" required>
                                <option value="">Pilih Distrik...</option>
                                <option
                                    v-for="r in regions"
                                    :key="r.id"
                                    :value="r.id"
                                >
                                    {{ r.name }}
                                </option>
                            </Select>
                        </Field>

                        <Field
                            label="Desa / Kampung"
                            :error="form.errors.village_id"
                        >
                            <Select v-model="form.village_id">
                                <option value="">Tanpa Desa / Kampung</option>
                                <option
                                    v-for="v in filteredVillages"
                                    :key="v.id"
                                    :value="v.id"
                                >
                                    {{ v.name }}
                                </option>
                            </Select>
                        </Field>

                        <Field
                            label="Kelompok Tani"
                            :error="form.errors.farmer_group_id"
                        >
                            <Select v-model="form.farmer_group_id">
                                <option value="">
                                    Mandiri / Tanpa Kelompok
                                </option>
                                <option
                                    v-for="g in filteredFarmerGroups"
                                    :key="g.id"
                                    :value="g.id"
                                >
                                    {{ g.name }}
                                </option>
                            </Select>
                        </Field>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5"
                >
                    <div
                        class="flex items-center gap-2 border-b border-border/60 pb-3"
                    >
                        <Icon :icon="Sprout" :size="18" class="text-brand" />
                        <h3 class="text-sm font-bold text-fg">
                            Komoditas Budidaya
                        </h3>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-fg"
                            >Pilih Komoditas Panen</label
                        >
                        <div
                            class="flex flex-wrap gap-1.5 max-h-52 overflow-y-auto p-2.5 rounded-xl border border-border/80 bg-muted/20"
                        >
                            <button
                                v-for="c in commodities"
                                :key="c.id"
                                type="button"
                                @click="toggleCommodity(c.id)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all cursor-pointer"
                                :class="
                                    form.commodities.includes(c.id)
                                        ? 'bg-brand text-white shadow-xs'
                                        : 'bg-white border border-border text-fg-muted hover:text-fg'
                                "
                            >
                                <Icon
                                    v-if="form.commodities.includes(c.id)"
                                    :icon="Check"
                                    :size="12"
                                />
                                <span>{{ c.name }}</span>
                            </button>
                            <span
                                v-if="!commodities || commodities.length === 0"
                                class="text-xs text-fg-muted px-2 py-1"
                            >
                                Belum ada master komoditas tersedia.
                            </span>
                        </div>
                        <p class="text-[11px] text-fg-muted">
                            Klik komoditas untuk menandai komoditas yang
                            dibudidayakan oleh petani ini.
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5"
                >
                    <div
                        class="flex items-center gap-2 border-b border-border/60 pb-3"
                    >
                        <Icon :icon="Upload" :size="18" class="text-brand" />
                        <h3 class="text-sm font-bold text-fg">
                            Foto Profil Petani
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <input
                            ref="fileInputRef"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handlePhotoChange"
                        />

                        <div
                            class="flex flex-col items-center justify-center p-6 rounded-2xl border-2 border-dashed border-border/80 bg-muted/10 text-center gap-3"
                        >
                            <div
                                class="size-24 rounded-full border border-border overflow-hidden bg-brand-weak/30 flex items-center justify-center shadow-xs"
                            >
                                <img
                                    v-if="photoPreviewUrl"
                                    :src="photoPreviewUrl"
                                    alt="Preview"
                                    class="size-full object-cover"
                                />
                                <Icon
                                    v-else
                                    :icon="User"
                                    :size="36"
                                    class="text-brand/60"
                                />
                            </div>

                            <div class="space-y-1">
                                <div class="text-xs font-semibold text-fg">
                                    {{
                                        photoPreviewUrl
                                            ? "Foto profil terunggah"
                                            : "Belum ada foto profil"
                                    }}
                                </div>
                                <div class="text-[11px] text-fg-muted">
                                    Format JPG, PNG, atau WEBP.
                                </div>
                            </div>

                            <div
                                class="flex flex-wrap items-center justify-center gap-2 pt-1"
                            >
                                <Button
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    class="gap-1.5 font-semibold"
                                    :loading="isUploadingPhoto"
                                    @click="triggerFileInput"
                                >
                                    <Icon :icon="Upload" :size="14" />
                                    <span>{{
                                        photoPreviewUrl
                                            ? "Ganti Foto"
                                            : "Unggah Foto"
                                    }}</span>
                                </Button>
                                <Button
                                    v-if="photoPreviewUrl || form.photo"
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    class="text-danger hover:bg-danger/10 font-semibold"
                                    @click="removePhoto"
                                >
                                    Hapus Foto
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
