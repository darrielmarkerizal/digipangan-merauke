<script setup lang="ts">
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import {
    Info,
    Target,
    Handshake,
    HelpCircle,
    Phone,
    Mail,
    UserRound,
    ExternalLink,
    Sprout,
    MessageCircleQuestion,
} from "@lucide/vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import {
    Icon,
    Badge,
    Accordion,
    AccordionItem,
    WhatsappIcon,
} from "@/Components/ui";

defineOptions({ layout: PublicLayout });

interface Partner {
    name: string;
    website_url: string | null;
    description: string | null;
    logo: { thumb: string; original: string } | null;
}

interface Faq {
    question: string;
    answer: string;
}

const props = defineProps<{
    settings: Record<string, string | null>;
    partners: Partner[];
    faqs: Faq[];
}>();

const background = computed(() => props.settings.about_background || "");
const purpose = computed(() => props.settings.about_purpose || "");
const contactName = computed(() => props.settings.admin_contact_name || "");
const contactPhone = computed(() => props.settings.admin_contact_phone || "");
const contactEmail = computed(() => props.settings.admin_contact_email || "");

const waLink = computed(() => {
    if (!contactPhone.value) return null;
    let clean = contactPhone.value.replace(/\D/g, "");
    if (clean.startsWith("0")) clean = "62" + clean.substring(1);
    return `https://wa.me/${clean}`;
});

const hasContact = computed(
    () => !!contactName.value || !!contactPhone.value || !!contactEmail.value,
);
</script>

<template>
    <Head title="Tentang DigiPangan Merauke">
        <meta
            name="description"
            content="Mengenal DigiPangan Merauke — etalase digital komoditas lokal kawasan transmigrasi, program kolaborasi Universitas Gadjah Mada dan Kementerian Transmigrasi RI."
        />
    </Head>

    <main class="min-h-screen bg-bg">
        <section class="relative overflow-hidden border-b border-border/80 bg-white">
            <div
                class="absolute inset-0 z-0 opacity-[0.04]"
                style="
                    background-image: radial-gradient(#000 1.5px, transparent 1.5px);
                    background-size: 32px 32px;
                "
            ></div>
            <div
                class="relative z-10 mx-auto max-w-[90rem] px-4 py-14 sm:px-6 sm:py-20 lg:px-8"
            >
                <div class="max-w-3xl">
                    <div class="mb-4">
                        <Badge variant="brand" :icon="Sprout">
                            Kedaulatan Pangan Nusantara
                        </Badge>
                    </div>
                    <h1
                        class="text-3xl font-extrabold tracking-tight text-fg sm:text-4xl lg:text-5xl"
                    >
                        Tentang DigiPangan Merauke
                    </h1>
                    <p
                        class="mt-4 text-base leading-relaxed text-fg-muted sm:text-lg"
                    >
                        Platform etalase digital komoditas lokal kawasan
                        transmigrasi Merauke — Muting, Ulilin, dan Elikobel —
                        hasil sinergi Universitas Gadjah Mada dan Kementerian
                        Transmigrasi RI.
                    </p>
                </div>
            </div>
        </section>

        <div
            class="mx-auto max-w-[90rem] px-4 py-12 sm:px-6 sm:py-16 lg:px-8"
        >
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-14">
                <div class="space-y-12 lg:col-span-8">
                    <section v-if="background">
                        <div class="mb-5 flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-lg bg-brand-weak text-brand"
                            >
                                <Icon :icon="Info" :size="20" />
                            </div>
                            <h2 class="text-2xl font-bold text-fg">
                                Latar Belakang Program
                            </h2>
                        </div>
                        <div
                            class="prose prose-base max-w-none rounded-3xl border border-border/60 bg-white p-6 leading-relaxed text-fg-muted shadow-soft ring-1 ring-fg/5 prose-headings:text-fg prose-a:text-brand prose-strong:text-fg sm:p-8"
                            v-html="background"
                        ></div>
                    </section>

                    <section v-if="purpose">
                        <div class="mb-5 flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-lg bg-brand-weak text-brand"
                            >
                                <Icon :icon="Target" :size="20" />
                            </div>
                            <h2 class="text-2xl font-bold text-fg">Tujuan</h2>
                        </div>
                        <div
                            class="prose prose-base max-w-none rounded-3xl border border-border/60 bg-white p-6 leading-relaxed text-fg-muted shadow-soft ring-1 ring-fg/5 prose-headings:text-fg prose-a:text-brand prose-strong:text-fg sm:p-8"
                            v-html="purpose"
                        ></div>
                    </section>

                    <section>
                        <div class="mb-5 flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-lg bg-brand-weak text-brand"
                            >
                                <Icon :icon="Handshake" :size="20" />
                            </div>
                            <h2 class="text-2xl font-bold text-fg">
                                Mitra Bisnis
                            </h2>
                        </div>

                        <div
                            v-if="partners.length > 0"
                            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                        >
                            <component
                                :is="partner.website_url ? 'a' : 'div'"
                                v-for="partner in partners"
                                :key="partner.name"
                                :href="partner.website_url || undefined"
                                :target="partner.website_url ? '_blank' : undefined"
                                :rel="
                                    partner.website_url
                                        ? 'noopener noreferrer'
                                        : undefined
                                "
                                :class="[
                                    'group flex items-start gap-4 rounded-2xl border border-border/80 bg-white p-5 shadow-xs transition-all',
                                    partner.website_url
                                        ? 'cursor-pointer hover:-translate-y-0.5 hover:shadow-soft'
                                        : '',
                                ]"
                            >
                                <div
                                    class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-border/60 bg-white p-2"
                                >
                                    <img
                                        v-if="partner.logo"
                                        :src="partner.logo.original"
                                        :alt="partner.name"
                                        class="size-full object-contain"
                                    />
                                    <Icon
                                        v-else
                                        :icon="Handshake"
                                        :size="24"
                                        class="text-brand/40"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <h3
                                            class="font-bold text-fg transition-colors group-hover:text-brand"
                                        >
                                            {{ partner.name }}
                                        </h3>
                                        <Icon
                                            v-if="partner.website_url"
                                            :icon="ExternalLink"
                                            :size="13"
                                            class="text-fg-muted"
                                        />
                                    </div>
                                    <p
                                        v-if="partner.description"
                                        class="mt-1 line-clamp-2 text-sm text-fg-muted"
                                    >
                                        {{ partner.description }}
                                    </p>
                                </div>
                            </component>
                        </div>

                        <div
                            v-else
                            class="rounded-2xl border border-dashed border-border bg-white p-8 text-center text-sm text-fg-muted"
                        >
                            Belum ada mitra bisnis yang ditampilkan.
                        </div>
                    </section>

                    <section>
                        <div class="mb-5 flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-lg bg-brand-weak text-brand"
                            >
                                <Icon :icon="MessageCircleQuestion" :size="20" />
                            </div>
                            <h2 class="text-2xl font-bold text-fg">
                                Pusat Bantuan
                            </h2>
                        </div>

                        <Accordion v-if="faqs.length > 0">
                            <AccordionItem
                                v-for="(faq, index) in faqs"
                                :key="index"
                                :value="`faq-${index}`"
                                :title="faq.question"
                            >
                                <div
                                    class="prose prose-sm max-w-none text-fg-muted prose-a:text-brand"
                                    v-html="faq.answer"
                                ></div>
                            </AccordionItem>
                        </Accordion>

                        <div
                            v-else
                            class="rounded-2xl border border-dashed border-border bg-white p-8 text-center text-sm text-fg-muted"
                        >
                            <div
                                class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-full bg-muted text-fg-muted"
                            >
                                <Icon :icon="HelpCircle" :size="24" />
                            </div>
                            Belum ada pertanyaan yang tersedia.
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-4">
                    <div
                        class="sticky top-24 rounded-3xl border border-border/80 bg-white p-6 shadow-soft ring-1 ring-fg/5"
                    >
                        <h2 class="text-lg font-bold text-fg">Kontak Admin</h2>
                        <p class="mt-1 text-sm text-fg-muted">
                            Hubungi pengelola platform untuk pertanyaan atau
                            kerja sama.
                        </p>

                        <div
                            v-if="!hasContact"
                            class="mt-5 rounded-xl border border-dashed border-border bg-muted/20 p-4 text-center text-sm text-fg-muted"
                        >
                            Informasi kontak admin belum tersedia.
                        </div>

                        <div v-else class="mt-5 space-y-3">
                            <div
                                v-if="contactName"
                                class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/20 p-3"
                            >
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-brand/10 text-brand"
                                >
                                    <Icon :icon="UserRound" :size="18" />
                                </div>
                                <div class="min-w-0">
                                    <p
                                        class="text-[11px] font-medium uppercase tracking-wider text-fg-muted"
                                    >
                                        Nama
                                    </p>
                                    <p class="truncate text-sm font-bold text-fg">
                                        {{ contactName }}
                                    </p>
                                </div>
                            </div>

                            <a
                                v-if="contactPhone"
                                :href="waLink || `tel:${contactPhone}`"
                                :target="waLink ? '_blank' : undefined"
                                :rel="waLink ? 'noopener noreferrer' : undefined"
                                class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/20 p-3 transition-colors hover:border-brand/40 hover:bg-brand/5"
                            >
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600"
                                >
                                    <Icon :icon="Phone" :size="18" />
                                </div>
                                <div class="min-w-0">
                                    <p
                                        class="text-[11px] font-medium uppercase tracking-wider text-fg-muted"
                                    >
                                        Telepon / WhatsApp
                                    </p>
                                    <p class="truncate text-sm font-bold text-fg">
                                        {{ contactPhone }}
                                    </p>
                                </div>
                            </a>

                            <a
                                v-if="contactEmail"
                                :href="`mailto:${contactEmail}`"
                                class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/20 p-3 transition-colors hover:border-brand/40 hover:bg-brand/5"
                            >
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600"
                                >
                                    <Icon :icon="Mail" :size="18" />
                                </div>
                                <div class="min-w-0">
                                    <p
                                        class="text-[11px] font-medium uppercase tracking-wider text-fg-muted"
                                    >
                                        Email
                                    </p>
                                    <p class="truncate text-sm font-bold text-fg">
                                        {{ contactEmail }}
                                    </p>
                                </div>
                            </a>
                        </div>

                        <a
                            v-if="waLink"
                            :href="waLink"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-3 text-sm font-bold text-white shadow-xs transition-colors hover:bg-brand-strong"
                        >
                            <WhatsappIcon :size="18" />
                            Hubungi via WhatsApp
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</template>
