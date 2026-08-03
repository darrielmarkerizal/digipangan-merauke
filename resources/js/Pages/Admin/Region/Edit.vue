<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ArrowLeft, Save } from "@lucide/vue";
import { Icon, Input, Button, Label, Textarea } from "@/Components/ui";

const props = defineProps<{
    region: any;
}>();

const form = useForm({
    name: props.region.name || "",
    description: props.region.description || "",
    agricultural_potential: props.region.agricultural_potential || "",
    area_km2: props.region.area_km2 || "",
    population: props.region.population || "",
    is_active: props.region.is_active,
});

const handleSubmit = () => {
    form.submit('put', `/admin/wilayah/${props.region.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AdminLayout
        :title="`Edit Wilayah: ${region.name}`"
        subtitle="Ubah informasi detail tentang kawasan transmigrasi ini."
    >
        <template #actions>
            <Button
                @click="handleSubmit"
                :disabled="form.processing"
                class="gap-1.5 font-semibold"
            >
                <Icon :icon="Save" :size="16" />
                <span>{{
                    form.processing ? "Menyimpan..." : "Simpan Perubahan"
                }}</span>
            </Button>
        </template>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <Link
                    href="/admin/wilayah"
                    class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    <span>Kembali ke Daftar Wilayah</span>
                </Link>
            </div>

            <div
                class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs max-w-3xl"
            >
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5 md:col-span-2">
                            <Label
                                for="name"
                                class="text-sm font-semibold text-fg"
                                >Nama Wilayah
                                <span class="text-danger">*</span></Label
                            >
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Contoh: Distrik Muting"
                                :error="!!form.errors.name"
                            />
                            <span
                                v-if="form.errors.name"
                                class="text-xs text-danger"
                                >{{ form.errors.name }}</span
                            >
                        </div>

                        <div class="space-y-1.5 md:col-span-2">
                            <Label
                                for="description"
                                class="text-sm font-semibold text-fg"
                                >Deskripsi</Label
                            >
                            <Textarea
                                id="description"
                                v-model="form.description"
                                :rows="3"
                                placeholder="Gambaran umum mengenai wilayah ini..."
                                :error="!!form.errors.description"
                            />
                            <span
                                v-if="form.errors.description"
                                class="text-xs text-danger"
                                >{{ form.errors.description }}</span
                            >
                        </div>

                        <div class="space-y-1.5 md:col-span-2">
                            <Label
                                for="agricultural_potential"
                                class="text-sm font-semibold text-fg"
                                >Potensi Pertanian</Label
                            >
                            <Textarea
                                id="agricultural_potential"
                                v-model="form.agricultural_potential"
                                :rows="3"
                                placeholder="Jelaskan potensi pertanian di daerah ini..."
                                :error="!!form.errors.agricultural_potential"
                            />
                            <span
                                v-if="form.errors.agricultural_potential"
                                class="text-xs text-danger"
                                >{{ form.errors.agricultural_potential }}</span
                            >
                        </div>

                        <div class="space-y-1.5">
                            <Label
                                for="area_km2"
                                class="text-sm font-semibold text-fg"
                                >Luas Area (km²)</Label
                            >
                            <Input
                                id="area_km2"
                                v-model="form.area_km2"
                                type="number"
                                step="0.01"
                                placeholder="Contoh: 150.5"
                                :error="!!form.errors.area_km2"
                            />
                            <span
                                v-if="form.errors.area_km2"
                                class="text-xs text-danger"
                                >{{ form.errors.area_km2 }}</span
                            >
                        </div>

                        <div class="space-y-1.5">
                            <Label
                                for="population"
                                class="text-sm font-semibold text-fg"
                                >Populasi (Jiwa)</Label
                            >
                            <Input
                                id="population"
                                v-model="form.population"
                                type="number"
                                placeholder="Contoh: 5000"
                                :error="!!form.errors.population"
                            />
                            <span
                                v-if="form.errors.population"
                                class="text-xs text-danger"
                                >{{ form.errors.population }}</span
                            >
                        </div>

                        <div
                            class="space-y-1.5 md:col-span-2 flex items-center gap-2"
                        >
                            <input
                                id="is_active"
                                type="checkbox"
                                v-model="form.is_active"
                                class="size-4 rounded border-border text-brand focus:ring-brand"
                            />
                            <Label
                                for="is_active"
                                class="text-sm font-semibold text-fg mb-0 cursor-pointer"
                            >
                                Tandai sebagai Wilayah Aktif
                            </Label>
                            <span
                                v-if="form.errors.is_active"
                                class="text-xs text-danger block w-full mt-1"
                                >{{ form.errors.is_active }}</span
                            >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
