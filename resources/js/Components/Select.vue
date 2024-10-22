<script setup>
const model = defineModel({
    type: String,
    required: true,
});
defineProps({
    error: {
        type: Boolean,
        default: false,
    },
    items: {
        type: Object,
        default: false,
    },
    key: {
        type: String,
        default: false,
    },
    value: {
        type: String,
        default: false,
    },
    name: {
        type: String,
        default: false,
    },
    default_title: {
        type: String,
        default: false,
    },
});

</script>

<template>
    <select
        :class="!error
        ? 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
        : 'bg-red-50 border border-red-500 text-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-900 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500'"
        required
        v-model="model"
    >
        <option value="0" selected>{{ default_title }}</option>
        <option v-for="item in items" :key="item[key]" :value="item[value]">{{ item[name] }}</option>
    </select>
</template>

<style scoped>

</style>
