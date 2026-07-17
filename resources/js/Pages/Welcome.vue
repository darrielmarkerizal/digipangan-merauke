<script setup>
defineProps({
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Digipangan</h1>
            <p class="text-gray-500 mb-6">Inertia.js + Vue 3 sudah berjalan! 🎉</p>
            <div class="text-sm text-gray-400 space-y-1">
                <p>Laravel <span class="font-medium text-gray-600">{{ laravelVersion }}</span></p>
                <p>PHP <span class="font-medium text-gray-600">{{ phpVersion }}</span></p>
            </div>
        </div>
    </div>
</template>
