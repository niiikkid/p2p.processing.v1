<script setup>
defineProps({
    is_active: {
        type: String,
    },
});
</script>

<template>
    <div class="flex items-center text-nowrap text-gray-900 dark:text-gray-200">
        <template v-if="is_active">
            <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> В работе
        </template>
        <template v-else>
            <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> Не в работе
        </template>
    </div>
</template>
