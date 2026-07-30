<script setup lang="ts">
import { ref } from "vue";
import {
    Clock,
    User,
    Database,
    Activity,
    ChevronDown,
    ChevronUp,
} from "@lucide/vue";
import { Icon, Badge, EmptyState } from "@/Components/ui";
import { formatWaktuLengkap } from "@/lib/format";

const props = defineProps<{
    auditList: any[];
}>();

const expandedRows = ref<number[]>([]);

const toggleRow = (id: number) => {
    const index = expandedRows.value.indexOf(id);
    if (index > -1) {
        expandedRows.value.splice(index, 1);
    } else {
        expandedRows.value.push(id);
    }
};

const getEventBadge = (event: string) => {
    switch (event) {
        case "created":
            return { label: "Dibuat", variant: "success" };
        case "updated":
            return { label: "Diubah", variant: "warning" };
        case "deleted":
            return { label: "Dihapus", variant: "danger" };
        case "restored":
            return { label: "Dipulihkan", variant: "info" };
        default:
            return { label: event, variant: "neutral" };
    }
};

const formatKey = (key: string) => {
    return key
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

const formatValue = (value: any) => {
    if (value === null || value === undefined) return "-";
    if (typeof value === "boolean") return value ? "Ya" : "Tidak";
    if (typeof value === "object") return JSON.stringify(value);
    return String(value);
};
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-fg">
            <thead
                class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider"
            >
                <tr>
                    <th scope="col" class="px-5 py-3.5">Waktu</th>
                    <th scope="col" class="px-4 py-3.5">Aksi</th>
                    <th scope="col" class="px-4 py-3.5">Aktor</th>
                    <th scope="col" class="px-4 py-3.5">Entitas</th>
                    <th scope="col" class="px-5 py-3.5 text-right">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/60">
                <tr v-if="auditList.length === 0">
                    <td colspan="5" class="px-5 py-12 text-center">
                        <EmptyState
                            title="Belum ada catatan audit"
                            description="Semua aktivitas tambah, ubah, dan hapus akan tercatat di sini."
                            :icon="Activity"
                        />
                    </td>
                </tr>
                <template v-for="item in auditList" :key="item.id">
                    <tr class="transition-colors hover:bg-muted/20">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <Icon
                                    :icon="Clock"
                                    :size="14"
                                    class="text-fg-muted"
                                />
                                <span class="font-medium">{{
                                    formatWaktuLengkap(item.created_at).replace(
                                        /\./g,
                                        ":",
                                    )
                                }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <Badge
                                :variant="
                                    getEventBadge(item.event).variant as any
                                "
                            >
                                {{ getEventBadge(item.event).label }}
                            </Badge>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <Icon
                                    :icon="User"
                                    :size="14"
                                    class="text-fg-muted"
                                />
                                <span class="font-medium text-brand">{{
                                    item.user
                                        ? item.user.name
                                        : "Sistem / Guest"
                                }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <Icon
                                    :icon="Database"
                                    :size="14"
                                    class="text-fg-muted"
                                />
                                <span
                                    >{{ item.auditable_type }} #{{
                                        item.auditable_id
                                    }}</span
                                >
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button
                                @click="toggleRow(item.id)"
                                class="inline-flex items-center justify-center p-2 rounded-full hover:bg-muted text-fg-muted hover:text-fg transition-colors"
                            >
                                <Icon
                                    :icon="
                                        expandedRows.includes(item.id)
                                            ? ChevronUp
                                            : ChevronDown
                                    "
                                    :size="16"
                                />
                            </button>
                        </td>
                    </tr>
                    <tr
                        v-if="expandedRows.includes(item.id)"
                        class="bg-muted/10"
                    >
                        <td colspan="5" class="px-5 py-4 border-t-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <p
                                        class="text-xs font-bold text-fg-muted uppercase"
                                    >
                                        Nilai Lama
                                    </p>
                                    <div
                                        class="bg-white p-3 rounded-lg border border-border/50 text-xs overflow-x-auto"
                                    >
                                        <span
                                            v-if="
                                                !item.old_values ||
                                                Object.keys(item.old_values)
                                                    .length === 0
                                            "
                                            class="text-fg-muted italic"
                                            >-</span
                                        >
                                        <ul v-else class="space-y-2">
                                            <li
                                                v-for="(
                                                    value, key
                                                ) in item.old_values"
                                                :key="key"
                                                class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-2"
                                            >
                                                <span
                                                    class="font-medium text-fg-muted sm:w-1/3 shrink-0"
                                                    >{{
                                                        formatKey(String(key))
                                                    }}:</span
                                                >
                                                <span
                                                    class="text-danger break-words flex-1"
                                                    >{{
                                                        formatValue(value)
                                                    }}</span
                                                >
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <p
                                        class="text-xs font-bold text-fg-muted uppercase"
                                    >
                                        Nilai Baru
                                    </p>
                                    <div
                                        class="bg-white p-3 rounded-lg border border-border/50 text-xs overflow-x-auto"
                                    >
                                        <span
                                            v-if="
                                                !item.new_values ||
                                                Object.keys(item.new_values)
                                                    .length === 0
                                            "
                                            class="text-fg-muted italic"
                                            >-</span
                                        >
                                        <ul v-else class="space-y-2">
                                            <li
                                                v-for="(
                                                    value, key
                                                ) in item.new_values"
                                                :key="key"
                                                class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-2"
                                            >
                                                <span
                                                    class="font-medium text-fg-muted sm:w-1/3 shrink-0"
                                                    >{{
                                                        formatKey(String(key))
                                                    }}:</span
                                                >
                                                <span
                                                    class="text-success break-words flex-1"
                                                    >{{
                                                        formatValue(value)
                                                    }}</span
                                                >
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="mt-4 inline-flex gap-6 text-xs text-fg-muted bg-white px-3 py-2 rounded-lg border border-border/50"
                            >
                                <span
                                    ><strong>IP:</strong>
                                    {{ item.ip_address || "-" }}</span
                                >
                                <span
                                    ><strong>URL:</strong>
                                    {{ item.url || "-" }}</span
                                >
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>
