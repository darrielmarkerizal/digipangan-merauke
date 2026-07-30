<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {
    ArrowLeft, Edit2, ShieldCheck, CheckCircle2, XCircle,
    User as UserIcon, Mail, Calendar, Clock, Shield
} from "@lucide/vue";
import { Button, Icon, Badge } from "@/Components/ui";
import { formatTanggal } from "@/lib/format";

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        is_active: boolean;
        avatar_url?: string | null;
        roles?: string[];
        created_at?: string;
        updated_at?: string;
    };
}>();

const roleLabel: Record<string, string> = {
    super_admin: "Super Admin",
    admin: "Admin",
};
</script>

<template>
    <AdminLayout
        :title="`Detail Pengguna: ${user.name}`"
        subtitle="Informasi lengkap akun pengguna dan peran sistem."
    >
        <template #actions>
            <Link :href="`/admin/user/${user.id}/edit`">
                <Button size="sm" class="gap-1.5 font-semibold">
                    <Icon :icon="Edit2" :size="16" />
                    <span>Edit Pengguna</span>
                </Button>
            </Link>
        </template>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <Link
                    href="/admin/user"
                    class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    <span>Kembali ke Daftar User</span>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-6">
                        <div class="flex items-center gap-4 pb-6 border-b border-border/60">
                            <div class="size-16 rounded-full bg-brand/10 flex items-center justify-center shrink-0 overflow-hidden border border-brand/20">
                                <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" class="size-full object-cover" />
                                <span v-else class="text-brand font-bold text-2xl">{{ user.name?.charAt(0)?.toUpperCase() }}</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-fg">{{ user.name }}</h2>
                                <p class="text-sm text-fg-muted flex items-center gap-1.5 mt-1">
                                    <Icon :icon="Mail" :size="14" />
                                    {{ user.email }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs font-semibold text-fg-muted uppercase tracking-wider block mb-1">Nama Lengkap</span>
                                <p class="text-sm font-semibold text-fg flex items-center gap-2">
                                    <Icon :icon="UserIcon" :size="15" class="text-brand" />
                                    {{ user.name }}
                                </p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-fg-muted uppercase tracking-wider block mb-1">Alamat Email</span>
                                <p class="text-sm font-semibold text-fg flex items-center gap-2">
                                    <Icon :icon="Mail" :size="15" class="text-brand" />
                                    {{ user.email }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-border/60">
                            <div>
                                <span class="text-xs font-semibold text-fg-muted uppercase tracking-wider block mb-1">Tanggal Bergabung</span>
                                <p class="text-sm font-semibold text-fg flex items-center gap-2">
                                    <Icon :icon="Calendar" :size="15" class="text-fg-muted" />
                                    {{ formatTanggal(user.created_at) }}
                                </p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-fg-muted uppercase tracking-wider block mb-1">Terakhir Diperbarui</span>
                                <p class="text-sm font-semibold text-fg flex items-center gap-2">
                                    <Icon :icon="Clock" :size="15" class="text-fg-muted" />
                                    {{ formatTanggal(user.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5">
                        <h3 class="text-sm font-bold text-fg border-b border-border/60 pb-3 flex items-center gap-2">
                            <Icon :icon="Shield" :size="16" class="text-brand" />
                            Status & Peran Akses
                        </h3>

                        <div>
                            <span class="text-xs font-semibold text-fg-muted block mb-2">Status Akun</span>
                            <Badge :variant="user.is_active ? 'success' : 'neutral'" class="gap-1 px-3 py-1 text-xs">
                                <Icon :icon="user.is_active ? CheckCircle2 : XCircle" :size="14" />
                                {{ user.is_active ? "Aktif (Dapat Login)" : "Nonaktif" }}
                            </Badge>
                        </div>

                        <div class="pt-3 border-t border-border/60">
                            <span class="text-xs font-semibold text-fg-muted block mb-2">Peran Pengguna</span>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="role in (user.roles ?? [])"
                                    :key="role"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full"
                                    :class="role === 'super_admin' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-blue-100 text-blue-700 border border-blue-200'"
                                >
                                    <Icon :icon="ShieldCheck" :size="13" />
                                    {{ roleLabel[role] ?? role }}
                                </span>
                                <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-fg-muted/60 italic">
                                    Tidak ada peran yang ditetapkan
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
