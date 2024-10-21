<script setup>
defineProps({
    status: {
        type: String,
    },
});
</script>

<template>
    <div class="flex items-center text-nowrap">
        <template v-if="status === 'accepted'">
            <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Принят
        </template>
        <template v-if="status === 'canceled'">
            <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> Отклонен
        </template>
        <template v-if="status === 'pending'">
            <div class="h-2.5 w-2.5 rounded-full bg-yellow-500 me-2"></div> Ожидает
        </template>
    </div>
</template>
