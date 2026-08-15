<script setup lang="ts">
import { computed } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import { Button, Field, Input, Label, Switch } from "@/Components/ui";
import { Icon } from "@/Components/ui";
import { Save, ArrowLeft, Loader2, Eye, EyeOff } from "@lucide/vue";
import { ref } from "vue";

interface UserData {
    id?: number;
    name?: string;
    email?: string;
    is_active?: boolean;
    region_id?: number | null;
    roles?: string[];
}

const props = defineProps<{
    user?: UserData;
    roles: string[];
    regions?: Array<{ id: number; name: string }>;
    isEdit?: boolean;
}>();

const showPassword = ref(false);
const showConfirm = ref(false);

const form = useForm({
    name: props.user?.name ?? "",
    email: props.user?.email ?? "",
    password: "",
    password_confirmation: "",
    is_active: props.user?.is_active ?? true,
    region_id: props.user?.region_id ?? null,
    roles: props.user?.roles ?? [],
});

const toggleRole = (role: string) => {
    const idx = form.roles.indexOf(role);
    if (idx === -1) {
        form.roles.push(role);
    } else {
        form.roles.splice(idx, 1);
        if (role === 'admin_distrik') {
            form.region_id = null;
        }
    }
};

const submit = () => {
    if (props.isEdit && props.user?.id) {
        form.submit('put', `/admin/user/${props.user.id}`, { preserveScroll: true });
    } else {
        form.post("/admin/user", { preserveScroll: true });
    }
};

const roleLabel: Record<string, string> = {
    super_admin: "Super Admin",
    admin: "Admin",
    admin_distrik: "Admin Distrik",
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="flex items-center justify-between">
            <Link
                href="/admin/user"
                class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
            >
                <Icon :icon="ArrowLeft" :size="16" />
                <span>Kembali ke Daftar User</span>
            </Link>
            <Button type="submit" :disabled="form.processing" class="gap-2 font-semibold">
                <Icon v-if="form.processing" :icon="Loader2" :size="15" class="animate-spin" />
                <Icon v-else :icon="Save" :size="15" />
                {{ isEdit ? "Simpan Perubahan" : "Tambah Pengguna" }}
            </Button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="rounded-xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
                    <h3 class="text-sm font-bold text-fg border-b border-border/60 pb-3">Informasi Pengguna</h3>

                    <Field :error="form.errors.name">
                        <Label required>Nama Lengkap</Label>
                        <Input v-model="form.name" placeholder="Masukkan nama lengkap..." class="w-full" />
                        <p v-if="form.errors.name" class="text-xs text-danger mt-1">{{ form.errors.name }}</p>
                    </Field>

                    <Field :error="form.errors.email">
                        <Label required>Alamat Email</Label>
                        <Input v-model="form.email" type="email" placeholder="nama@email.com" class="w-full" />
                        <p v-if="form.errors.email" class="text-xs text-danger mt-1">{{ form.errors.email }}</p>
                    </Field>
                </div>

                <div class="rounded-xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
                    <h3 class="text-sm font-bold text-fg border-b border-border/60 pb-3">
                        {{ isEdit ? "Ubah Password (opsional)" : "Password" }}
                    </h3>
                    <p v-if="isEdit" class="text-xs text-fg-muted -mt-2">Kosongkan jika tidak ingin mengubah password.</p>

                    <Field :error="form.errors.password">
                        <Label :required="!isEdit">Password</Label>
                        <div class="relative">
                            <Input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Minimal 8 karakter"
                                class="w-full pr-10"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-fg-muted hover:text-fg transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <Icon :icon="showPassword ? EyeOff : Eye" :size="16" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-xs text-danger mt-1">{{ form.errors.password }}</p>
                    </Field>

                    <Field :error="form.errors.password_confirmation">
                        <Label :required="!isEdit">Konfirmasi Password</Label>
                        <div class="relative">
                            <Input
                                v-model="form.password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                placeholder="Ulangi password"
                                class="w-full pr-10"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-fg-muted hover:text-fg transition-colors"
                                @click="showConfirm = !showConfirm"
                            >
                                <Icon :icon="showConfirm ? EyeOff : Eye" :size="16" />
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="text-xs text-danger mt-1">{{ form.errors.password_confirmation }}</p>
                    </Field>
                </div>
            </div>

            <div class="space-y-5">
                <div class="rounded-xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
                    <h3 class="text-sm font-bold text-fg border-b border-border/60 pb-3">Peran & Status</h3>

                    <div>
                        <p class="text-xs font-bold text-fg mb-2">Peran Pengguna</p>
                        <div class="space-y-2">
                            <label
                                v-for="role in roles"
                                :key="role"
                                class="flex items-center gap-3 cursor-pointer group rounded-lg border border-border/60 px-3 py-2.5 transition-colors hover:bg-muted/40"
                                :class="form.roles.includes(role) ? 'border-brand/40 bg-brand-weak/20' : ''"
                            >
                                <input
                                    type="checkbox"
                                    :checked="form.roles.includes(role)"
                                    @change="toggleRole(role)"
                                    class="rounded border-border text-brand focus:ring-brand"
                                />
                                <span class="text-sm font-medium text-fg">{{ roleLabel[role] ?? role }}</span>
                            </label>
                        </div>
                        <p v-if="form.errors.roles" class="text-xs text-danger mt-1">{{ form.errors.roles }}</p>
                    </div>

                    <!-- Distrik Penugasan (Only when Admin Distrik selected) -->
                    <div v-if="form.roles.includes('admin_distrik')" class="pt-2 border-t border-border/60 space-y-2">
                        <Field :error="form.errors.region_id">
                            <Label required>Distrik Penugasan</Label>
                            <select
                                v-model="form.region_id"
                                class="flex h-10 w-full rounded-xl border border-border/80 bg-white px-3 py-2 text-sm ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option :value="null" disabled>Pilih Distrik Penugasan</option>
                                <option v-for="r in (regions ?? [])" :key="r.id" :value="r.id">
                                    {{ r.name }}
                                </option>
                            </select>
                            <p class="text-[11px] text-fg-muted mt-1">Admin ini hanya dapat mengakses dan mengelola data pada distrik yang dipilih.</p>
                            <p v-if="form.errors.region_id" class="text-xs text-danger mt-1">{{ form.errors.region_id }}</p>
                        </Field>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-border/60">
                        <div>
                            <p class="text-sm font-semibold text-fg">Status Aktif</p>
                            <p class="text-xs text-fg-muted mt-0.5">Pengguna dapat login</p>
                        </div>
                        <Switch v-model="form.is_active" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
