<script setup>
const text = defineModel({
    type: String,
    required: true,
});
</script>

<template>
    <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ text }}</p>
</template>
