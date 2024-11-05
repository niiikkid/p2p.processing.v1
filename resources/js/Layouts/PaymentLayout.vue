<script setup>
import {computed, onMounted, ref} from "vue";
import {initFlowbite} from "flowbite";

const isDarkColorTheme = ref(false);

const switchThemeColorMode = () => {
    // if set via local storage previously
    if (localStorage.getItem('color-theme-payment')) {
        if (localStorage.getItem('color-theme-payment') === 'light') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme-payment', 'dark');
            isDarkColorTheme.value = true;
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme-payment', 'light');
            isDarkColorTheme.value = false;
        }

        // if NOT set via local storage previously
    } else {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme-payment', 'light');
            isDarkColorTheme.value = false;
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme-payment', 'dark');
            isDarkColorTheme.value = true;
        }
    }
}

onMounted(() => {
    initFlowbite();

    if (localStorage.getItem('color-theme-payment') === 'dark' || (!('color-theme-payment' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme-payment', 'dark');
        isDarkColorTheme.value = true;
    } else {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('color-theme-payment', 'light');
        isDarkColorTheme.value = false;
    }
})

</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div
            class="w-full sm:max-w-md"
        >
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl">Ваше название</h2>
                <button type="button" class="text-center flex items-center justify-center text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-xl text-xs px-2 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                    <span class="[&>svg]:h-4 [&>svg]:w-4 me-2">
                      <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="currentColor"
                          viewBox="0 0 496 512">
                        <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc. -->
                        <path
                            d="M248 8C111 8 0 119 0 256S111 504 248 504 496 393 496 256 385 8 248 8zM363 176.7c-3.7 39.2-19.9 134.4-28.1 178.3-3.5 18.6-10.3 24.8-16.9 25.4-14.4 1.3-25.3-9.5-39.3-18.7-21.8-14.3-34.2-23.2-55.3-37.2-24.5-16.1-8.6-25 5.3-39.5 3.7-3.8 67.1-61.5 68.3-66.7 .2-.7 .3-3.1-1.2-4.4s-3.6-.8-5.1-.5q-3.3 .7-104.6 69.1-14.8 10.2-26.9 9.9c-8.9-.2-25.9-5-38.6-9.1-15.5-5-27.9-7.7-26.8-16.3q.8-6.7 18.5-13.7 108.4-47.2 144.6-62.3c68.9-28.6 83.2-33.6 92.5-33.8 2.1 0 6.6 .5 9.6 2.9a10.5 10.5 0 0 1 3.5 6.7A43.8 43.8 0 0 1 363 176.7z" />
                      </svg>
                    </span>
                    Поддержка
                </button>
            </div>

            <div class="mt-4 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                <slot />
            </div>
            <div class="flex justify-center mt-3">
                <button @click.prevent="switchThemeColorMode" type="button" class="text-gray-800 dark:text-white border-none hover:text-gray-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:hover:text-gray-500 dark:focus:ring-blue-800">
                    <svg v-if="isDarkColorTheme" class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z" clip-rule="evenodd"/>
                    </svg>
                    <svg v-else class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5V3m0 18v-2M7.05 7.05 5.636 5.636m12.728 12.728L16.95 16.95M5 12H3m18 0h-2M7.05 16.95l-1.414 1.414M18.364 5.636 16.95 7.05M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
