<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { UserPlus, Eye, EyeOff } from "@lucide/vue";
import { toast } from "vue-sonner";
import { Button, Field, Input, PhoneInput, Select, Icon } from "@/Components/ui";

const props = defineProps<{
    regions?: any[];
    villages?: any[];
    farmerGroups?: any[];
}>();

const form = useForm({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
    region_id: "",
    village_id: "",
    farmer_group_id: "",
    land_area_ha: "",
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const filteredVillages = computed(() => {
    if (!form.region_id) return [];
    return (props.villages || []).filter(
        (v: any) => Number(v.region_id) === Number(form.region_id),
    );
});

const filteredFarmerGroups = computed(() => {
    if (!form.region_id) return [];
    return (props.farmerGroups || []).filter(
        (g: any) => Number(g.region_id) === Number(form.region_id),
    );
});

watch(
    () => form.region_id,
    (newRegionId, oldRegionId) => {
        if (oldRegionId !== undefined && newRegionId !== oldRegionId) {
            form.village_id = "";
            form.farmer_group_id = "";
        }
    },
);

const submit = () => {
    form.transform((data) => ({
        ...data,
        village_id: data.village_id === "" ? null : data.village_id,
        farmer_group_id: data.farmer_group_id === "" ? null : data.farmer_group_id,
        land_area_ha:
            data.land_area_ha === "" || data.land_area_ha === null
                ? null
                : Number(data.land_area_ha),
    }));

    form.post("/daftar", {
        onSuccess: () => {
            toast.success("Pendaftaran berhasil", {
                description: "Selamat datang di DigiPangan Merauke.",
            });
        },
        onError: () => {
            toast.error("Gagal mendaftar", {
                description: "Periksa kembali isian formulir Anda.",
            });
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Field label="Nama Lengkap" :error="form.errors.name" required>
                <Input
                    v-model="form.name"
                    placeholder="Contoh: Pak Budi Santoso"
                    autocomplete="name"
                    :disabled="form.processing"
                />
            </Field>

            <Field label="No. WhatsApp / Telepon" :error="form.errors.phone" required>
                <PhoneInput
                    v-model="form.phone"
                    placeholder="81234567890"
                    autocomplete="tel"
                    :disabled="form.processing"
                />
            </Field>
        </div>

        <Field label="Alamat Email" :error="form.errors.email" required>
            <Input
                v-model="form.email"
                type="email"
                placeholder="nama@email.com"
                autocomplete="username"
                :disabled="form.processing"
            />
        </Field>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Field label="Kata Sandi" :error="form.errors.password" required>
                <div class="relative">
                    <Input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Minimal 8 karakter"
                        autocomplete="new-password"
                        :disabled="form.processing"
                        class="pr-11"
                    />
                    <button
                        type="button"
                        aria-label="Tampilkan kata sandi"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-fg-muted transition-colors hover:text-fg"
                        @click="showPassword = !showPassword"
                    >
                        <Icon :icon="showPassword ? EyeOff : Eye" :size="18" />
                    </button>
                </div>
            </Field>

            <Field
                label="Konfirmasi Kata Sandi"
                :error="form.errors.password_confirmation"
                required
            >
                <div class="relative">
                    <Input
                        v-model="form.password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        placeholder="Ulangi kata sandi"
                        autocomplete="new-password"
                        :disabled="form.processing"
                        class="pr-11"
                    />
                    <button
                        type="button"
                        aria-label="Tampilkan konfirmasi kata sandi"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-fg-muted transition-colors hover:text-fg"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                    >
                        <Icon :icon="showPasswordConfirmation ? EyeOff : Eye" :size="18" />
                    </button>
                </div>
            </Field>
        </div>

        <div class="h-px bg-border/60" />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Field label="Distrik / Kawasan" :error="form.errors.region_id" required>
                <Select v-model="form.region_id" required :disabled="form.processing">
                    <option value="">Pilih Distrik...</option>
                    <option v-for="r in regions" :key="r.id" :value="r.id">
                        {{ r.name }}
                    </option>
                </Select>
            </Field>

            <Field label="Desa / Kampung" :error="form.errors.village_id">
                <Select v-model="form.village_id" :disabled="form.processing">
                    <option value="">Tanpa Desa / Kampung</option>
                    <option v-for="v in filteredVillages" :key="v.id" :value="v.id">
                        {{ v.name }}
                    </option>
                </Select>
            </Field>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Field
                label="Kelompok Tani"
                :error="form.errors.farmer_group_id"
                helper="Boleh dikosongkan jika belum tergabung kelompok tani."
            >
                <Select v-model="form.farmer_group_id" :disabled="form.processing">
                    <option value="">Mandiri / Tanpa Kelompok</option>
                    <option v-for="g in filteredFarmerGroups" :key="g.id" :value="g.id">
                        {{ g.name }}
                    </option>
                </Select>
            </Field>

            <Field label="Luas Lahan (Ha)" :error="form.errors.land_area_ha">
                <Input
                    v-model="form.land_area_ha"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="Contoh: 2.5"
                    :disabled="form.processing"
                />
            </Field>
        </div>

        <Button type="submit" fullWidth :loading="form.processing" class="mt-3 shadow-sm">
            <Icon v-if="!form.processing" :icon="UserPlus" :size="18" />
            <span>{{ form.processing ? "Mendaftarkan akun..." : "Daftar Sebagai Petani" }}</span>
        </Button>
    </form>
</template>
