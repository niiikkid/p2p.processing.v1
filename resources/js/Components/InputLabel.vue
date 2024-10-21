<script setup>
defineProps({
    value: {
        type: String,
    },
    error: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <label class="block font-medium text-sm" :class="error ? 'text-red-700 dark:text-red-500' : 'text-gray-700 dark:text-gray-300'">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
