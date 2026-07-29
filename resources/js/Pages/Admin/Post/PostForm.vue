<script setup lang="ts">
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { ArrowLeft, Save, FileText, Image as ImageIcon, Upload } from "@lucide/vue";
import {
    Icon,
    Input,
    Button,
    Label,
    Select,
} from "@/Components/ui";
import { toast } from "vue-sonner";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import axios from "axios";

const props = defineProps<{
    form: any;
    categories: any[];
    isEdit?: boolean;
}>();

const emit = defineEmits(["submit"]);

const coverPreview = ref<string | null>(props.form.cover || null);
const coverFile = ref<File | null>(null);
const isUploading = ref(false);
const isPreviewMode = ref(false);

const handleCoverChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        coverFile.value = file;
        coverPreview.value = URL.createObjectURL(file);
    }
};

const removeCover = () => {
    coverFile.value = null;
    coverPreview.value = null;
    props.form.cover = null;
};

const handleSubmit = async () => {
    if (coverFile.value) {
        isUploading.value = true;
        try {
            const formData = new FormData();
            formData.append("file", coverFile.value);
            const res = await axios.post("/api/v1/media/upload", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            props.form.cover = res.data.folder;
        } catch (e) {
            toast.error("Gagal mengunggah gambar cover.");
            isUploading.value = false;
            return;
        }
    }
    emit("submit");
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <Link
                href="/admin/berita"
                class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
            >
                <Icon :icon="ArrowLeft" :size="16" />
                <span>Kembali ke Daftar Berita</span>
            </Link>

            <div class="flex p-1 bg-muted/30 rounded-lg border border-border inline-flex self-start sm:self-auto">
                <button 
                    type="button" 
                    @click="isPreviewMode = false" 
                    :class="['px-4 py-1.5 text-sm font-semibold rounded-md transition-all flex items-center gap-2', !isPreviewMode ? 'bg-white text-brand shadow-sm border border-border/50' : 'text-fg-muted hover:text-fg']"
                >
                    <Icon :icon="FileText" :size="14" />
                    Mode Tulis
                </button>
                <button 
                    type="button" 
                    @click="isPreviewMode = true" 
                    :class="['px-4 py-1.5 text-sm font-semibold rounded-md transition-all flex items-center gap-2', isPreviewMode ? 'bg-white text-brand shadow-sm border border-border/50' : 'text-fg-muted hover:text-fg']"
                >
                    <Icon :icon="ImageIcon" :size="14" />
                    Pratinjau
                </button>
            </div>
        </div>

        <form @submit.prevent="handleSubmit" v-show="!isPreviewMode">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-6"
                    >
                        <div
                            class="flex items-center gap-2 border-b border-border/60 pb-3"
                        >
                            <Icon
                                :icon="FileText"
                                :size="18"
                                class="text-brand"
                            />
                            <h3 class="text-sm font-bold text-fg">
                                Konten Berita
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <Label
                                    for="title"
                                    class="text-sm font-semibold text-fg"
                                    >Judul Berita
                                    <span class="text-danger">*</span></Label
                                >
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Masukkan judul berita yang menarik"
                                    :error="!!form.errors.title"
                                />
                                <span
                                    v-if="form.errors.title"
                                    class="text-xs text-danger"
                                    >{{ form.errors.title }}</span
                                >
                            </div>

                            <div class="space-y-1.5">
                                <Label
                                    for="body"
                                    class="text-sm font-semibold text-fg"
                                    >Isi Berita
                                    <span class="text-danger">*</span></Label
                                >
                                <div class="border rounded-md border-border/80 overflow-hidden" :class="{'border-danger': form.errors.body}">
                                  <QuillEditor
                                    theme="snow"
                                    v-model:content="form.body"
                                    contentType="html"
                                    placeholder="Tulis konten berita atau artikel di sini..."
                                    style="min-height: 300px;"
                                  />
                                </div>
                                <span
                                    v-if="form.errors.body"
                                    class="text-xs text-danger"
                                    >{{ form.errors.body }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Config -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Gambar Sampul -->
                    <div
                        class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden"
                    >
                        <div class="p-6 border-b border-border/60">
                            <div class="flex items-center gap-2">
                                <Icon
                                    :icon="ImageIcon"
                                    :size="18"
                                    class="text-brand"
                                />
                                <h3 class="text-sm font-bold text-fg">
                                    Cover Berita
                                </h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div
                                    class="relative flex aspect-[4/3] w-full flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-border/90 bg-muted/20 transition-colors hover:border-brand/50 hover:bg-brand-weak/10"
                                >
                                    <div v-if="coverPreview" class="relative h-full w-full group">
                                        <img
                                            :src="coverPreview"
                                            alt="Cover Preview"
                                            class="absolute inset-0 h-full w-full object-cover"
                                        />
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button
                                                type="button"
                                                @click.prevent="removeCover"
                                                class="px-3 py-1.5 rounded-xl bg-white text-xs font-semibold text-danger shadow-md cursor-pointer hover:bg-danger-weak"
                                            >
                                                Hapus Cover
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="flex flex-col items-center justify-center space-y-2 p-4 text-center"
                                    >
                                        <div
                                            class="rounded-full bg-brand-weak p-3 text-brand"
                                        >
                                            <Icon :icon="Upload" :size="20" />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-fg"
                                            >
                                                Unggah Cover
                                            </p>
                                            <p
                                                class="text-xs text-fg-muted mt-1"
                                            >
                                                JPG, PNG, atau WebP
                                            </p>
                                        </div>
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        class="absolute inset-0 cursor-pointer opacity-0"
                                        @change="handleCoverChange"
                                    />
                                </div>
                                <span
                                    v-if="form.errors.cover"
                                    class="text-xs text-danger block"
                                    >{{ form.errors.cover }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Pengaturan Publikasi -->
                    <div
                        class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden"
                    >
                        <div class="p-6 border-b border-border/60">
                            <h3 class="text-sm font-bold text-fg">
                                Pengaturan Publikasi
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="space-y-1.5">
                                <Label
                                    for="post_category_id"
                                    class="text-sm font-semibold text-fg"
                                    >Kategori
                                    <span class="text-danger">*</span></Label
                                >
                                <Select
                                    id="post_category_id"
                                    v-model="form.post_category_id"
                                    :error="!!form.errors.post_category_id"
                                >
                                    <option value="">Pilih Kategori...</option>
                                    <option
                                        v-for="c in categories"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.name }}
                                    </option>
                                </Select>
                                <span
                                    v-if="form.errors.post_category_id"
                                    class="text-xs text-danger"
                                    >{{ form.errors.post_category_id }}</span
                                >
                            </div>

                            <div class="space-y-1.5">
                                <Label
                                    for="status"
                                    class="text-sm font-semibold text-fg"
                                    >Status Publikasi</Label
                                >
                                <Select
                                    id="status"
                                    v-model="form.status"
                                    :error="!!form.errors.status"
                                >
                                    <option value="draft">Draft</option>
                                    <option value="published">
                                        Diterbitkan (Published)
                                    </option>
                                </Select>
                                <span
                                    v-if="form.errors.status"
                                    class="text-xs text-danger"
                                    >{{ form.errors.status }}</span
                                >
                            </div>

                            <div class="pt-4 border-t border-border/60">
                                <Button
                                    type="submit"
                                    :disabled="form.processing || isUploading"
                                    class="w-full gap-1.5 font-semibold"
                                >
                                    <Icon :icon="Save" :size="16" />
                                    <span
                                        >{{ form.processing || isUploading ? "Menyimpan..." : (isEdit ? "Simpan Perubahan" : "Simpan Berita") }}</span
                                    >
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Preview Mode -->
        <div v-if="isPreviewMode" class="max-w-4xl mx-auto pb-12 animate-in fade-in slide-in-from-bottom-4 duration-300">
            <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden">
                <!-- Cover Image (Preview) -->
                <div class="w-full aspect-[21/9] bg-muted/20 relative overflow-hidden">
                    <img 
                        v-if="coverPreview"
                        :src="coverPreview" 
                        alt="Cover Berita"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-fg-muted">
                        <Icon :icon="ImageIcon" :size="48" class="opacity-20 mb-2" />
                        <span class="text-sm font-medium">Belum ada cover gambar</span>
                    </div>
                </div>

                <div class="p-6 md:p-10 lg:p-12">
                    <div class="max-w-3xl mx-auto space-y-8">
                        <!-- Header / Meta -->
                        <div class="space-y-4 border-b border-border/60 pb-8">
                            <h1 class="text-3xl md:text-4xl font-extrabold text-fg leading-tight">
                                {{ form.title || 'Judul Berita Belum Diisi' }}
                            </h1>
                        </div>
                        
                        <!-- Content -->
                        <div 
                            class="prose max-w-none text-fg-muted leading-relaxed whitespace-pre-wrap"
                            v-html="form.body || '<p class=\'text-center italic opacity-50 py-10\'>Konten berita masih kosong...</p>'"
                        >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.ql-container {
    font-family: inherit !important;
    font-size: 14px !important;
}
.ql-editor {
    min-height: 300px;
}
</style>
