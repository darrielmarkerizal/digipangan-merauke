<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ArrowLeft, Edit2, Tag, Calendar, User, Eye, Info } from "@lucide/vue";
import { Button, Icon, Badge } from "@/Components/ui";
import { formatTanggal } from "@/lib/format";

const props = defineProps<{
    post: any;
}>();
</script>

<template>
    <AdminLayout
        :title="`Pratinjau Berita`"
        subtitle="Lihat bagaimana berita ini ditampilkan."
    >
        <template #actions>
            <Link :href="`/admin/berita/${post.id}/edit`">
                <Button size="sm" class="gap-1.5 font-semibold">
                    <Icon :icon="Edit2" :size="16" />
                    <span>Edit Berita</span>
                </Button>
            </Link>
        </template>

        <div class="space-y-6">
            <div
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <Link
                    href="/admin/berita"
                    class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
                >
                    <Icon :icon="ArrowLeft" :size="16" />
                    <span>Kembali ke Daftar Berita</span>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Info (Read Only) -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden"
                    >
                        <!-- Cover Preview -->
                        <div
                            class="w-full aspect-[21/9] bg-muted/30 relative flex items-center justify-center border-b border-border/60"
                        >
                            <img
                                v-if="post.cover_url"
                                :src="post.cover_url"
                                :alt="post.title"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="text-fg-muted/50 flex flex-col items-center gap-2"
                            >
                                <Icon :icon="Eye" :size="32" />
                                <span class="text-sm font-medium"
                                    >Tidak ada gambar sampul</span
                                >
                            </div>
                        </div>

                        <div class="p-8 space-y-8">
                            <!-- Title & Meta -->
                            <div class="space-y-4">
                                <h1
                                    class="text-2xl font-bold text-fg leading-tight"
                                >
                                    {{ post.title }}
                                </h1>
                                <div
                                    class="flex flex-wrap items-center gap-4 text-sm text-fg-muted"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <Icon :icon="User" :size="16" />
                                        <span>{{
                                            post.author?.name || "Anonim"
                                        }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <Icon :icon="Calendar" :size="16" />
                                        <span>{{ post.published_at ? formatTanggal(post.published_at) : 'Belum Dipublikasikan' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="prose max-w-none text-fg-muted leading-relaxed whitespace-pre-wrap"
                                v-html="post.body"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden"
                    >
                        <div class="p-6 border-b border-border/60">
                            <div class="flex items-center gap-2">
                                <Icon
                                    :icon="Info"
                                    :size="18"
                                    class="text-brand"
                                />
                                <h3 class="text-sm font-bold text-fg">
                                    Status & Metadata
                                </h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Status Publikasi</span
                                >
                                <Badge
                                    :variant="
                                        post.status === 'published'
                                            ? 'success'
                                            : 'neutral'
                                    "
                                    class="mt-1"
                                >
                                    <span class="capitalize">{{
                                        post.status
                                    }}</span>
                                </Badge>
                            </div>

                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Kategori</span
                                >
                                <div
                                    class="flex items-center gap-1.5 mt-1 text-sm font-medium text-fg"
                                >
                                    <Icon
                                        :icon="Tag"
                                        :size="14"
                                        class="text-fg-muted"
                                    />
                                    <span>{{
                                        post.category?.name || "-"
                                    }}</span>
                                </div>
                            </div>

                            <div>
                                <span
                                    class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1"
                                    >Slug URL</span
                                >
                                <div
                                    class="mt-1 text-sm text-fg-muted font-mono bg-muted/30 p-2 rounded-lg break-all"
                                >
                                    {{ post.slug }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
