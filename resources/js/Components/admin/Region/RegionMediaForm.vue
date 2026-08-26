<script setup lang="ts">
import { onBeforeUnmount, ref } from "vue";
import axios from "axios";
import { Image as ImageIcon, Plus, Star, Trash2, Upload } from "@lucide/vue";
import { toast } from "vue-sonner";
import { Icon } from "@/Components/ui";

interface ExistingImage {
    id: number | string;
    original?: string;
    thumb?: string;
    card?: string;
}

interface ImageItem {
    id: string;
    url: string;
    file?: File;
    mediaId?: number | string;
}

const MAX_FILE_SIZE = 8 * 1024 * 1024;
const ACCEPTED_TYPES = ["image/jpeg", "image/jpg", "image/png", "image/webp", "image/gif"];

const props = defineProps<{
    form: any;
    initialCover?: ExistingImage | null;
    initialGallery?: ExistingImage[];
}>();

const coverImage = ref<ImageItem | null>(
    props.initialCover
        ? {
              id: `cover-${props.initialCover.id}`,
              url: props.initialCover.original || props.initialCover.card || props.initialCover.thumb || "",
              mediaId: props.initialCover.id,
          }
        : null,
);

const galleryImages = ref<ImageItem[]>(
    (props.initialGallery || []).map((image) => ({
        id: `gallery-${image.id}`,
        url: image.original || image.card || image.thumb || "",
        mediaId: image.id,
    })),
);

const isUploading = ref(false);

const revokePreview = (url: string) => {
    if (url.startsWith("blob:")) {
        URL.revokeObjectURL(url);
    }
};

const validateFile = (file: File): boolean => {
    if (!ACCEPTED_TYPES.includes(file.type)) {
        toast.error("Format gambar tidak didukung", {
            description: "Gunakan JPG, PNG, WEBP, atau GIF.",
        });
        return false;
    }

    if (file.size > MAX_FILE_SIZE) {
        toast.error("Ukuran gambar terlalu besar", {
            description: "Ukuran maksimal gambar adalah 8 MB.",
        });
        return false;
    }

    return true;
};

const handleCoverChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file || !validateFile(file)) {
        input.value = "";
        return;
    }

    if (coverImage.value) {
        revokePreview(coverImage.value.url);
    }

    coverImage.value = {
        id: `cover-new-${Date.now()}`,
        url: URL.createObjectURL(file),
        file,
    };
    input.value = "";
};

const handleGalleryChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files || []).filter(validateFile);

    files.forEach((file) => {
        galleryImages.value.push({
            id: `gallery-new-${Date.now()}-${Math.random().toString(36).slice(2)}`,
            url: URL.createObjectURL(file),
            file,
        });
    });

    if (files.length > 0) {
        toast.success(`${files.length} foto galeri ditambahkan.`);
    }

    input.value = "";
};

const removeGalleryImage = (index: number) => {
    const image = galleryImages.value[index];
    if (!image) return;

    revokePreview(image.url);
    galleryImages.value.splice(index, 1);
};

const uploadFile = async (file: File): Promise<string> => {
    const data = new FormData();
    data.append("file", file);

    const response = await axios.post("/admin/media/upload", data, {
        headers: { "Content-Type": "multipart/form-data" },
    });

    return response.data.folder;
};

const prepareUpload = async (): Promise<boolean> => {
    if (isUploading.value) return false;

    isUploading.value = true;
    const uploadedFolders: string[] = [];

    try {
        if (coverImage.value?.file) {
            const folder = await uploadFile(coverImage.value.file);
            uploadedFolders.push(folder);
            props.form.cover = folder;
        }

        const galleryFolders: string[] = [];
        for (const image of galleryImages.value) {
            if (image.file) {
                const folder = await uploadFile(image.file);
                uploadedFolders.push(folder);
                galleryFolders.push(folder);
            }
        }

        props.form.gallery = galleryFolders;
        props.form.retained_gallery = galleryImages.value
            .filter((image) => image.mediaId !== undefined)
            .map((image) => image.mediaId);

        return true;
    } catch (error: any) {
        await Promise.allSettled(
            uploadedFolders.map((folder) =>
                axios.delete("/admin/media/upload", { data: { folder } }),
            ),
        );
        toast.error("Gagal mengunggah foto distrik", {
            description:
                error.response?.data?.message ||
                "Periksa koneksi dan coba lagi.",
        });
        return false;
    } finally {
        isUploading.value = false;
    }
};

onBeforeUnmount(() => {
    if (coverImage.value) revokePreview(coverImage.value.url);
    galleryImages.value.forEach((image) => revokePreview(image.url));
});

defineExpose({ prepareUpload, isUploading });
</script>

<template>
    <div class="space-y-4 rounded-2xl border border-border/80 bg-white p-5 shadow-xs md:p-6">
        <div class="flex items-start justify-between gap-3 border-b border-border/60 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
                    <Icon :icon="ImageIcon" :size="18" />
                </span>
                <div>
                    <h2 class="text-base font-bold text-fg">Foto Profil Distrik</h2>
                    <p class="text-xs text-fg-muted">
                        Foto cover tampil pada kartu wilayah; foto tambahan tampil di galeri.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-fg">Cover Utama</h3>
                    <p class="text-xs text-fg-muted">Disarankan foto lanskap distrik.</p>
                </div>
                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl bg-brand px-3 py-2 text-xs font-bold text-white transition-colors hover:bg-brand-strong">
                    <Icon :icon="Upload" :size="14" />
                    {{ coverImage ? "Ganti Foto" : "Pilih Foto" }}
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="sr-only"
                        @change="handleCoverChange"
                    />
                </label>
            </div>

            <div class="relative aspect-[2.4/1] overflow-hidden rounded-xl border border-border/80 bg-brand-weak/30">
                <img
                    v-if="coverImage"
                    :src="coverImage.url"
                    alt="Pratinjau cover distrik"
                    class="size-full object-cover"
                />
                <div v-else class="flex size-full flex-col items-center justify-center gap-2 text-brand/50">
                    <Icon :icon="ImageIcon" :size="32" />
                    <span class="text-xs font-medium">Belum ada foto cover</span>
                </div>
                <span
                    v-if="coverImage"
                    class="absolute left-2 top-2 inline-flex items-center gap-1 rounded-full bg-brand px-2.5 py-1 text-[10px] font-extrabold text-white shadow-sm"
                >
                    <Icon :icon="Star" :size="12" class="fill-current text-amber-300" />
                    Cover Utama
                </span>
            </div>
            <p v-if="form.errors.cover" class="text-xs text-danger">{{ form.errors.cover }}</p>
        </div>

        <div class="space-y-2 border-t border-border/60 pt-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-fg">Galeri Distrik</h3>
                    <p class="text-xs text-fg-muted">Tambahkan foto pemandangan atau kegiatan pertanian.</p>
                </div>
                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl border border-border bg-white px-3 py-2 text-xs font-bold text-fg transition-colors hover:border-brand hover:text-brand">
                    <Icon :icon="Plus" :size="14" />
                    Tambah
                    <input
                        type="file"
                        multiple
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="sr-only"
                        @change="handleGalleryChange"
                    />
                </label>
            </div>

            <div v-if="galleryImages.length" class="grid grid-cols-3 gap-2.5">
                <div
                    v-for="(image, index) in galleryImages"
                    :key="image.id"
                    class="group relative aspect-square overflow-hidden rounded-xl border border-border/80 bg-muted/30"
                >
                    <img :src="image.url" alt="Pratinjau foto galeri distrik" class="size-full object-cover" />
                    <button
                        type="button"
                        class="absolute right-1.5 top-1.5 flex size-7 items-center justify-center rounded-lg bg-white/90 text-danger opacity-0 shadow-sm transition-opacity group-hover:opacity-100 focus:opacity-100"
                        aria-label="Hapus foto galeri distrik"
                        title="Hapus foto galeri"
                        @click="removeGalleryImage(index)"
                    >
                        <Icon :icon="Trash2" :size="14" />
                    </button>
                </div>
            </div>
            <p v-else class="rounded-xl border border-dashed border-border/90 bg-muted/20 px-4 py-5 text-center text-xs text-fg-muted">
                Belum ada foto tambahan.
            </p>
            <p v-if="form.errors.gallery" class="text-xs text-danger">{{ form.errors.gallery }}</p>
        </div>

        <p class="text-[11px] leading-relaxed text-fg-muted">
            Format yang didukung: JPG, PNG, WEBP, GIF. Ukuran maksimal 8 MB per foto.
        </p>
    </div>
</template>
