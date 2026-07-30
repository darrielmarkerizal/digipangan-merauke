<script setup lang="ts">
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Button, Field, Input, Label, Switch, Textarea } from "@/Components/ui";
import { Icon } from "@/Components/ui";
import { Save, ArrowLeft, Loader2 } from "@lucide/vue";
import { Link } from "@inertiajs/vue3";

interface FaqData {
    id?: number;
    question?: string;
    answer?: string;
    sort_order?: number;
    is_active?: boolean;
}

const props = defineProps<{
    faq?: FaqData;
    isEdit?: boolean;
}>();

const form = useForm({
    question: props.faq?.question ?? "",
    answer: props.faq?.answer ?? "",
    sort_order: props.faq?.sort_order ?? 0,
    is_active: props.faq?.is_active ?? true,
});

const submit = () => {
    if (props.isEdit && props.faq?.id) {
        form.put(`/admin/faq/${props.faq.id}`, {
            preserveScroll: true,
        });
    } else {
        form.post("/admin/faq", {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="flex items-center justify-between">
            <Link
                href="/admin/faq"
                class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
            >
                <Icon :icon="ArrowLeft" :size="16" />
                <span>Kembali ke Daftar FAQ</span>
            </Link>
            <Button type="submit" :disabled="form.processing" class="gap-2 font-semibold">
                <Icon v-if="form.processing" :icon="Loader2" :size="15" class="animate-spin" />
                <Icon v-else :icon="Save" :size="15" />
                {{ isEdit ? "Simpan Perubahan" : "Tambah FAQ" }}
            </Button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="rounded-xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
                    <Field :error="form.errors.question">
                        <Label required>Pertanyaan (Question)</Label>
                        <Input
                            v-model="form.question"
                            placeholder="Masukkan pertanyaan yang sering ditanyakan..."
                            class="w-full"
                        />
                        <p v-if="form.errors.question" class="text-xs text-danger mt-1">
                            {{ form.errors.question }}
                        </p>
                    </Field>

                    <Field :error="form.errors.answer">
                        <Label required>Jawaban (Answer)</Label>
                        <Textarea
                            v-model="form.answer"
                            placeholder="Tulis jawaban yang jelas dan informatif..."
                            :rows="8"
                            class="w-full resize-none"
                        />
                        <p v-if="form.errors.answer" class="text-xs text-danger mt-1">
                            {{ form.errors.answer }}
                        </p>
                    </Field>
                </div>
            </div>

            <div class="space-y-5">
                <div class="rounded-xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
                    <h3 class="text-sm font-bold text-fg border-b border-border/60 pb-3">Pengaturan</h3>

                    <Field :error="form.errors.sort_order">
                        <Label>Urutan Tampil</Label>
                        <Input
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                            placeholder="0"
                            class="w-full"
                        />
                        <p class="text-xs text-fg-muted mt-1">Angka lebih kecil tampil lebih atas.</p>
                        <p v-if="form.errors.sort_order" class="text-xs text-danger mt-1">
                            {{ form.errors.sort_order }}
                        </p>
                    </Field>

                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <p class="text-sm font-semibold text-fg">Status Aktif</p>
                            <p class="text-xs text-fg-muted mt-0.5">Tampilkan di halaman publik</p>
                        </div>
                        <Switch v-model="form.is_active" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
