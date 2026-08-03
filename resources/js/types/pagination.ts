import type { PaginationMeta, PaginationLinks } from "@/Components/ui/Pagination.vue";

export type { PaginationMeta, PaginationLinks };

export interface PaginatedData<T> {
    data: T[];
    links?: PaginationLinks | Array<{ url: string | null; label: string; active: boolean }>;
    meta?: PaginationMeta;
}
