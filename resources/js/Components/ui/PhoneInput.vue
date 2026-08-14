<script setup lang="ts">
import { ref, watch, inject, computed } from "vue";
import { FIELD_KEY } from "./field-context";

defineOptions({ inheritAttrs: false });

const props = withDefaults(
    defineProps<{
        modelValue?: string | number | null;
        placeholder?: string;
        disabled?: boolean;
    }>(),
    {
        modelValue: "",
        placeholder: "81234567890",
        disabled: false,
    },
);

const emit = defineEmits<{
    "update:modelValue": [value: string];
}>();

const field = inject(FIELD_KEY, null);

const extractLocalDigits = (
    val: string | number | null | undefined,
): string => {
    if (!val) return "";
    let digits = String(val).replace(/\D/g, "");
    if (digits.startsWith("62")) {
        digits = digits.slice(2);
    }
    while (digits.startsWith("0")) {
        digits = digits.slice(1);
    }
    if (digits.length > 13) {
        digits = digits.slice(0, 13);
    }
    return digits;
};

const localValue = ref(extractLocalDigits(props.modelValue));

watch(
    () => props.modelValue,
    (newVal) => {
        const extracted = extractLocalDigits(newVal);
        if (extracted !== localValue.value) {
            localValue.value = extracted;
        }
    },
);

const handleInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const cleaned = extractLocalDigits(target.value);
    localValue.value = cleaned;
    target.value = cleaned;
    emit("update:modelValue", cleaned ? `62${cleaned}` : "");
};

const isInvalid = computed(() => field?.invalid.value ?? false);
</script>

<template>
    <div
        class="flex min-h-11 w-full rounded-control border bg-card overflow-hidden transition-all duration-150 focus-within:border-brand focus-within:ring-2 focus-within:ring-brand/20"
        :class="[
            isInvalid ? 'border-danger' : 'border-border',
            disabled ? 'opacity-60 cursor-not-allowed' : '',
        ]"
    >
        <div
            class="flex items-center border-r border-border bg-muted/40 px-3 text-sm font-semibold text-fg-muted select-none"
        >
            <span>+62</span>
        </div>
        <input
            :id="field?.id.value"
            :value="localValue"
            type="tel"
            inputmode="numeric"
            :placeholder="placeholder"
            :disabled="disabled"
            :aria-invalid="field?.invalid.value || undefined"
            :aria-describedby="field?.describedBy.value"
            :aria-required="field?.required.value || undefined"
            class="w-full min-h-11 bg-transparent px-3 text-base text-fg placeholder:text-fg-muted focus:outline-hidden disabled:cursor-not-allowed"
            v-bind="$attrs"
            @input="handleInput"
        />
    </div>
</template>
