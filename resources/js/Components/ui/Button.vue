<script setup lang="ts">
import { computed, useAttrs, type Component } from "vue";
import { Primitive } from "reka-ui";
import { tv, type VariantProps } from "tailwind-variants";
import Spinner from "./Spinner.vue";

defineOptions({ inheritAttrs: false });

const buttonVariants = tv({
    base: [
        "relative inline-flex items-center justify-center gap-2 rounded-control font-medium",
        "whitespace-nowrap select-none",
        "transition-[background-color,box-shadow,transform] active:scale-[0.98]",
        "disabled:pointer-events-none disabled:opacity-60 aria-disabled:pointer-events-none aria-disabled:opacity-60",
    ],
    variants: {
        variant: {
            primary: "bg-brand text-on-brand hover:bg-brand-strong",
            secondary: "border border-border bg-card text-fg hover:bg-muted",
            ghost: "text-brand hover:bg-brand-weak",
            whatsapp: "bg-brand text-on-brand hover:bg-brand-strong",
            danger: "bg-danger text-white hover:bg-danger/90",
            "danger-outline": "border border-danger/30 bg-danger-weak/40 text-danger hover:bg-danger hover:text-white hover:border-danger",
            "danger-secondary": "border border-red-200 bg-red-50 text-red-700 hover:bg-red-600 hover:text-white",
        },
        size: {
            sm: "min-h-9 gap-1.5 px-3 text-sm",
            md: "min-h-11 px-5 text-base",
            lg: "min-h-12 px-6 text-lg",
        },
        iconOnly: { true: "aspect-square px-0", false: "" },
        fullWidth: { true: "w-full", false: "" },
    },
    defaultVariants: {
        variant: "primary",
        size: "md",
        iconOnly: false,
        fullWidth: false,
    },
});

type Variants = VariantProps<typeof buttonVariants>;

const props = withDefaults(
    defineProps<{
        variant?: Variants["variant"];
        size?: Variants["size"];
        iconOnly?: boolean;
        fullWidth?: boolean;
        loading?: boolean;
        disabled?: boolean;
        as?: string | Component;
        asChild?: boolean;
        type?: "button" | "submit" | "reset";
        ariaLabel?: string;
    }>(),
    {
        variant: "primary",
        size: "md",
        iconOnly: false,
        fullWidth: false,
        loading: false,
        disabled: false,
        as: "button",
        asChild: false,
        type: "button",
    },
);

const attrs = useAttrs();

if (import.meta.env.DEV) {
    const hasName =
        props.ariaLabel || attrs["aria-label"] || attrs["aria-labelledby"];
    if (props.iconOnly && !hasName) {
        console.warn(
            "[Button] `iconOnly` wajib disertai `aria-label` demi aksesibilitas.",
        );
    }
}

const isNativeButton = computed(() => props.as === "button");
const isDisabled = computed(() => props.disabled || props.loading);
</script>

<template>
    <Primitive
        :as="as"
        :as-child="asChild"
        :class="buttonVariants({ variant, size, iconOnly, fullWidth })"
        :type="isNativeButton ? type : undefined"
        :disabled="isNativeButton && isDisabled ? true : undefined"
        :aria-disabled="!isNativeButton && isDisabled ? 'true' : undefined"
        :aria-busy="loading || undefined"
        :aria-label="ariaLabel"
        v-bind="$attrs"
    >
        <Spinner
            v-if="loading"
            :size="size === 'sm' ? 16 : 20"
            class="absolute"
        />
        <span
            :class="['inline-flex items-center gap-2', loading && 'invisible']"
        >
            <slot />
        </span>
    </Primitive>
</template>
