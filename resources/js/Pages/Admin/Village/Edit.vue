<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ArrowLeft, Save } from "@lucide/vue";
import { Icon, Input, Button, Label, Select } from "@/Components/ui";

const props = defineProps<{
    village: any;
    regions: any[];
}>();

const form = useForm({
    region_id: props.village.region_id || "",
    name: props.village.name || "",
    is_active: props.village.is_active,
});

const handleSubmit = () => {
    form.submit('put', `/admin/desa/${props.village.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AdminLayout
        :title="`Edit Desa: ${village.name}`"
        subtitle="Ubah informasi detail tentang desa ini."
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
                    href="/admin/desa"
                    class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    <span>Kembali ke Daftar Desa</span>
                </Link>
            </div>

            <div
                class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs max-w-3xl"
            >
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5 md:col-span-2">
                            <Label
                                for="region_id"
                                class="text-sm font-semibold text-fg"
                                >Distrik / Wilayah
                                <span class="text-danger">*</span></Label
                            >
                            <Select
                                id="region_id"
                                v-model="form.region_id"
                                :error="!!form.errors.region_id"
                            >
                                <option value="">Pilih wilayah asal...</option>
                                <option
                                    v-for="r in regions"
                                    :key="r.id"
                                    :value="r.id"
                                >
                                    {{ r.name }}
                                </option>
                            </Select>
                            <span
                                v-if="form.errors.region_id"
                                class="text-xs text-danger"
                                >{{ form.errors.region_id }}</span
                            >
                        </div>

                        <div class="space-y-1.5 md:col-span-2">
                            <Label
                                for="name"
                                class="text-sm font-semibold text-fg"
                                >Nama Desa
                                <span class="text-danger">*</span></Label
                            >
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Contoh: Kampung Muting"
                                :error="!!form.errors.name"
                            />
                            <span
                                v-if="form.errors.name"
                                class="text-xs text-danger"
                                >{{ form.errors.name }}</span
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
                                Tandai sebagai Desa Aktif
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
