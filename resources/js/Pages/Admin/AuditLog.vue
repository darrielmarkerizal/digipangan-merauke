<script setup lang="ts">
import { computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminPageCard from "@/Components/admin/AdminPageCard.vue";
import { Pagination } from "@/Components/ui";
import AuditLogTable from "@/Components/admin/AuditLogTable.vue";

const props = defineProps<{
    audits?: {
        data: any[];
        links?: any;
        meta?: any;
    };
    filters?: {
        event?: string;
        auditable_type?: string;
    };
}>();

const auditList = computed(() => {
    const rawData = props.audits?.data;
    return Array.isArray(rawData) ? rawData : (rawData as any)?.data || [];
});
</script>

<template>
    <AdminLayout
        title="Log Aktivitas Admin (Audit Log)"
        subtitle="Riwayat perubahan data dan jejak rekam aktivitas admin untuk menjaga transparansi, keandalan, dan keamanan sistem."
    >
        <div class="space-y-4">
            <AdminPageCard>
                <AuditLogTable :audit-list="auditList" />
            </AdminPageCard>

            <Pagination :meta="audits?.meta" :links="audits?.links" />
        </div>
    </AdminLayout>
</template>
