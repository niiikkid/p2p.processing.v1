<script setup>
const emit = defineEmits(['changed']);

const menuMode = defineModel({
    type: String,
    required: true,
});

const switchMenuMode = (mode) => {
    emit('changed', mode);
}
</script>

<template>
    <div class="mb-4">
        <ul class="hidden border border-gray-200 dark:border-gray-700 text-sm font-medium text-center text-gray-500 rounded-lg sm:flex dark:divide-gray-700 dark:text-gray-400 overflow-hidden">
            <li class="w-full focus-within:z-10">
                <a
                    @click.prevent="switchMenuMode('trader')"
                    href="#"
                    class="inline-block w-full p-2 border-r-0 border-gray-200 dark:border-gray-700 hover:text-gray-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-700"
                    :class="menuMode === 'trader' ? 'text-gray-900 bg-gray-200 border-r dark:bg-gray-700 dark:text-white' : 'bg-white dark:bg-gray-800'"
                >
                    Трейдер
                </a>
            </li>
            <li class="w-full focus-within:z-10">
                <a @click.prevent="switchMenuMode('admin')"
                   href="#"
                   class="inline-block w-full p-2 border-s-0 border-gray-200 dark:border-gray-700 hover:text-gray-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-700"
                   :class="menuMode === 'admin' ? 'text-gray-900 bg-gray-200 border-r dark:bg-gray-700 dark:text-white' : 'bg-white dark:bg-gray-800'"
                >
                    Админ
                </a>
            </li>
        </ul>
    </div>
</template>

<style scoped>

</style>
