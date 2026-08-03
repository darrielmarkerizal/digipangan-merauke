<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FilterPanel from "@/Components/admin/FilterPanel.vue";
import UserTable from "@/Components/admin/User/UserTable.vue";
import { Plus, Search } from "@lucide/vue";
import { Icon, Button, Pagination } from "@/Components/ui";
import { useSearch } from "@/Composables/useSearch";
import { useSort } from "@/Composables/useSort";

const props = defineProps<{
    users?: {
        data: any[];
        links?: any;
        meta?: any;
    };
    roles?: string[];
}>();

const { search } = useSearch();
const { getSortDirection, sortBy } = useSort();

const userList = computed(() => {
    const rawData = props.users?.data;
    return Array.isArray(rawData) ? rawData : (rawData as any)?.data || [];
});
</script>

<template>
    <AdminLayout title="User & Hak Akses" subtitle="Kelola akun pengguna admin dan pengaturan peran serta hak akses sistem.">
        <template #actions>
            <Link href="/admin/user/tambah">
                <Button class="gap-2 font-semibold shadow-sm">
                    <Icon :icon="Plus" :size="16" />
                    <span>Tambah Pengguna</span>
                </Button>
            </Link>
        </template>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="relative flex-1 max-w-sm">
                    <Icon :icon="Search" :size="16" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted pointer-events-none" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama atau email..."
                        class="w-full h-10 pl-10 pr-4 rounded-xl border border-border/80 bg-white text-sm text-fg placeholder:text-fg-muted/60 transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none"
                    />
                </div>
                <FilterPanel module="user" :roles="roles" />
            </div>

            <div class="overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs">
                <UserTable
                    :user-list="userList"
                    :sort-by="sortBy"
                    :get-sort-direction="getSortDirection"
                />
            </div>

            <Pagination :meta="users?.meta" :links="users?.links" />
        </div>
    </AdminLayout>
</template>
