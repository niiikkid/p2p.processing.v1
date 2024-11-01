<script setup>
import Select from "@/Components/Select.vue";

import {useViewStore} from "@/store/view.js";

const viewStore = useViewStore();
</script>

<template>
    <div class="mb-4">
        <p class="text-sm text-gray-900 dark:text-gray-200 mb-1">Посмотреть сайт как</p>
        <select
            class="block w-full p-2 mb-6 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            required
            v-model="viewStore.viewMode"
        >
            <option value="admin" selected>Админ</option>
            <option value="trader" selected>Трейдер</option>
            <option value="merchant" selected>Мерчант</option>
        </select>
    </div>
</template>

<style scoped>

</style>
