<script setup lang="ts">
import { router, Link } from "@inertiajs/vue3";
import {
    Edit2, Trash2, Users, Eye,
    ArrowUp, ArrowDown, ArrowUpDown,
    ShieldCheck, CheckCircle2, XCircle,
} from "@lucide/vue";
import { Icon, Button, Badge, AlertDialog, EmptyState } from "@/Components/ui";
import { formatTanggal } from "@/lib/format";
import { toast } from "vue-sonner";

const props = defineProps<{
    userList: any[];
    sortBy: (column: string) => void;
    getSortDirection: (column: string) => "asc" | "desc" | null;
}>();

const roleLabel: Record<string, string> = {
    super_admin: "Super Admin",
    admin: "Admin",
};

const executeDelete = (id: number) => {
    router.delete(`/admin/user/${id}`, {
        preserveScroll: true,
        onSuccess: () => toast.success("Pengguna berhasil dihapus!"),
        onError: (errors: any) => {
            const msg = errors?.message ?? "Gagal menghapus pengguna.";
            toast.error(msg);
        },
    });
};
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-fg">
            <thead class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider">
                <tr>
                    <th
                        scope="col"
                        class="px-5 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                        @click="sortBy('name')"
                    >
                        <div class="flex items-center gap-2">
                            <span>Pengguna</span>
                            <Icon
                                :icon="getSortDirection('name') === 'asc' ? ArrowUp : getSortDirection('name') === 'desc' ? ArrowDown : ArrowUpDown"
                                :size="14"
                                :class="getSortDirection('name') ? 'text-brand' : 'text-fg-muted/40 group-hover:text-fg-muted'"
                            />
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3.5">Peran</th>
                    <th scope="col" class="px-4 py-3.5">Status</th>
                    <th
                        scope="col"
                        class="px-4 py-3.5 cursor-pointer hover:bg-muted/50 transition-colors group"
                        @click="sortBy('created_at')"
                    >
                        <div class="flex items-center gap-2">
                            <span>Bergabung</span>
                            <Icon
                                :icon="getSortDirection('created_at') === 'asc' ? ArrowUp : getSortDirection('created_at') === 'desc' ? ArrowDown : ArrowUpDown"
                                :size="14"
                                :class="getSortDirection('created_at') ? 'text-brand' : 'text-fg-muted/40 group-hover:text-fg-muted'"
                            />
                        </div>
                    </th>
                    <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
                <tr v-if="userList.length === 0">
                    <td colspan="5" class="px-5 py-12 text-center">
                        <EmptyState
                            title="Belum ada pengguna"
                            description="Tambahkan akun admin untuk mengelola sistem DigiPangan."
                            :icon="Users"
                        />
                    </td>
                </tr>
                <tr
                    v-for="item in userList"
                    :key="item.id"
                    class="transition-colors hover:bg-muted/20"
                >
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-full bg-brand/10 flex items-center justify-center shrink-0 overflow-hidden">
                                <img v-if="item.avatar_url" :src="item.avatar_url" :alt="item.name" class="size-full object-cover" />
                                <span v-else class="text-brand font-bold text-sm">{{ item.name?.charAt(0)?.toUpperCase() }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-fg">{{ item.name }}</p>
                                <p class="text-xs text-fg-muted">{{ item.email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="role in (item.roles ?? [])"
                                :key="role"
                                class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full"
                                :class="role === 'super_admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'"
                            >
                                <Icon :icon="ShieldCheck" :size="10" />
                                {{ roleLabel[role] ?? role }}
                            </span>
                            <span v-if="!item.roles || item.roles.length === 0" class="text-xs text-fg-muted/60 italic">Tanpa peran</span>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <Badge :variant="item.is_active ? 'success' : 'neutral'" class="gap-1">
                            <Icon :icon="item.is_active ? CheckCircle2 : XCircle" :size="12" />
                            {{ item.is_active ? "Aktif" : "Nonaktif" }}
                        </Badge>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-xs text-fg-muted font-medium whitespace-nowrap">
                            {{ formatTanggal(item.created_at) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <Link :href="`/admin/user/${item.id}`">
                                <Button variant="secondary" size="sm" class="size-8 p-0" title="Lihat Detail">
                                    <Icon :icon="Eye" :size="14" />
                                </Button>
                            </Link>
                            <Link :href="`/admin/user/${item.id}/edit`">
                                <Button variant="secondary" size="sm" class="size-8 p-0" title="Edit Pengguna">
                                    <Icon :icon="Edit2" :size="14" />
                                </Button>
                            </Link>
                            <AlertDialog
                                title="Hapus Pengguna?"
                                :description="`Apakah Anda yakin ingin menghapus akun '${item.name}'? Tindakan ini tidak dapat dibatalkan.`"
                                confirm-label="Ya, Hapus"
                                cancel-label="Batal"
                                :destructive="true"
                                @confirm="executeDelete(item.id)"
                            >
                                <template #trigger>
                                    <Button variant="danger-outline" size="sm" class="size-8 p-0" title="Hapus Pengguna">
                                        <Icon :icon="Trash2" :size="14" />
                                    </Button>
                                </template>
                            </AlertDialog>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
