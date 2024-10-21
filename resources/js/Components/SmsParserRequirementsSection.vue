<script setup>

</script>

<template>
    <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Важные указания:</h2>
    <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 mb-2">
        <li>
            Необходимо указывать имя группы <span class="font-semibold text-gray-900 dark:text-white">amount</span> для суммы перевода. (только цифровую часть)
            <span class="text-yellow-400">*</span>
        </li>
        <li>
            Необходимо указывать имя группы <span class="font-semibold text-gray-900 dark:text-white">card_type</span> для типа карты если она есть. Например: VISA,MIR...
        </li>
        <li>
            Необходимо указывать имя группы <span class="font-semibold text-gray-900 dark:text-white">card_last_digits</span> для последних 4х цифр карты если они есть.
        </li>
        <li>
            Имя группы указывается в формате <span class="font-semibold text-gray-900 dark:text-white">?{{'<amount>'}}</span>
            <span class="text-yellow-400">*</span>
        </li>
    </ul>
    <span class="text-sm text-gray-600">Обязательные поля помечены <span class="text-yellow-400">*</span></span>

</template>

<style scoped>

</style>
