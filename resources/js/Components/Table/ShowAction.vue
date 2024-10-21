<script setup>
defineProps({
    link: {
        type: String,
    },
});
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <Link
        :href="link"
        class="px-0 py-0 text-gray-900 justify-items-center dark:text-gray-400 dark:hover:text-blue-500 inline-flex items-center hover:underline"
    >
        <svg class="w-[22px] h-[22px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
            <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>
    </Link>
</template>

<style scoped>

</style>
