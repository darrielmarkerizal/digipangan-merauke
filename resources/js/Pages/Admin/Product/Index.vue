<script setup lang="ts">
import { computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminHeaderAction from "@/Components/admin/AdminHeaderAction.vue";
import FilterPanel from "@/Components/admin/FilterPanel.vue";
import ProductTable from "@/Components/admin/Product/ProductTable.vue";
import { Plus, Search } from "@lucide/vue";
import { Icon, Pagination } from "@/Components/ui";
import { useSearch } from "@/Composables/useSearch";
import { useSort } from "@/Composables/useSort";

const props = defineProps<{
    products?: {
        data: Array<{
            id: number;
            name: string;
            price: string | number;
            stock_available: boolean;
            is_featured: boolean;
            is_active: boolean;
            category?: { name: string };
            unit?: { name: string; symbol?: string };
            farmer?: { name: string };
            region?: { name: string };
            photos?: Array<{ thumb: string; original: string }>;
        }>;
        links?: any;
        meta?: any;
    };
    categories?: Array<{ id: number; name: string }>;
    regions?: Array<{ id: number; name: string }>;
    filters?: {
        search?: string;
        sort?: string;
        filter?: Record<string, any>;
    };
}>();

const { search } = useSearch();
const { getSortDirection, sortBy } = useSort();

const productList = computed(() => {
    const rawData = props.products?.data;
    const items = Array.isArray(rawData)
        ? rawData
        : (rawData as any)?.data || [];
    return items.map((p: any) => ({
        id: p.id,
        name: p.name,
        category: p.category?.name || "Umum",
        farmer: p.farmer?.name || "Petani Mitra",
        region: p.region?.name || "Kawasan Merauke",
        price:
            typeof p.price === "number"
                ? `Rp ${new Intl.NumberFormat("id-ID").format(p.price)}`
                : `Rp ${p.price}`,
        unit: p.unit?.name || "Kg",
        stock_available: p.stock_available,
        is_featured: p.is_featured,
        is_active: p.is_active,
        image_url: p.photos?.[0]?.thumb || p.photos?.[0]?.original || null,
    }));
});
</script>

<template>
    <AdminLayout
        title="Kelola Produk &amp; Komoditas"
        subtitle="Manajemen katalog komoditas hasil panen, penentuan harga, berat/satuan, serta status ketersediaan stok di 3 kawasan transmigrasi Merauke."
    >
        <template #actions>
            <AdminHeaderAction
                :icon="Plus"
                label="Tambah Produk"
                href="/admin/produk/tambah"
            />
        </template>

        <div class="space-y-4">
            <div
                class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
            >
                <div class="relative flex-1 max-w-md">
                    <Icon
                        :icon="Search"
                        :size="18"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 text-fg-muted"
                    />
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Cari nama produk komoditas..."
                        class="w-full rounded-xl border border-border/80 bg-white pl-10 pr-4 py-2 text-sm text-fg placeholder:text-fg-muted/70 focus:border-brand focus:outline-none shadow-xs"
                    />
                </div>

                <FilterPanel :categories="categories" :regions="regions" />
            </div>

            <div
                class="overflow-hidden rounded-2xl border border-border/80 bg-white shadow-xs"
            >
                <ProductTable
                    :product-list="productList"
                    :sort-by="sortBy"
                    :get-sort-direction="getSortDirection"
                />
            </div>

            <Pagination :meta="products?.meta" :links="products?.links" />
        </div>
    </AdminLayout>
</template>
