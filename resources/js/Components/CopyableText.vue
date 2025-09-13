<script setup>
import { useClipboard } from '@vueuse/core'

const props = defineProps({
    text: { type: String, required: true }, // что копировать
    display: { type: String, default: '' }, // что показывать (если пусто, показываем text)
})

const { copy, copied } = useClipboard()
</script>

<template>
    <a
        href="#"
        :data-tooltip-target="'tooltip-copyable'+$.uid"
        @click.prevent="copy(text)"
        class="hover:bg-gray-100 dark:hover:bg-gray-700 p-1 rounded text-nowrap"
    >
        <code class="text-xs">{{ display || text }}</code>
    </a>
    <div :id="'tooltip-copyable'+$.uid" role="tooltip"
         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
        <span v-if="!copied">Скопировать</span>
        <span v-else>Скопировано!</span>
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>
</template>

<style scoped>
</style>
