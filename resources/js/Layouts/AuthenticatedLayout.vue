<script setup>
import {usePage, router, Link} from '@inertiajs/vue3';
import {onMounted, ref} from 'vue'
import { initFlowbite } from 'flowbite'
import MenuModeSwitcher from "@/Layouts/Partials/MenuModeSwitcher.vue";
import TraderMenu from "@/Layouts/Partials/TraderMenu.vue";
import AdminMenu from "@/Layouts/Partials/AdminMenu.vue";
import NavBar from "@/Layouts/Partials/NavBar.vue";
import MerchantMenu from "@/Layouts/Partials/MerchantMenu.vue";

const is_admin = usePage().props.auth.is_admin;
const rates = usePage().props.data.rates;

const menuMode = ref('trader');

const switchMenuMode = (mode) => {
    menuMode.value = mode;
}

// initialize components based on data attribute selectors
onMounted(() => {
    if (route().current('admin.*')) {
        switchMenuMode('admin')
    }
    //TODO костыль
    if (route().current('merchants.*')) {
        switchMenuMode('merchant')
    }
    if (route().current('integration.*')) {
        switchMenuMode('merchant')
    }

    initFlowbite();

    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark')
    }
})

router.on('success', (event) => {
    initFlowbite()
})

const openDocs = () => {
    window.open('/docs', '_blank');
}
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <NavBar/>

            <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
                <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
                    <MenuModeSwitcher
                        v-if="is_admin"
                        v-model="menuMode"
                        @changed="switchMenuMode"
                    />
                    <TraderMenu
                        v-show="menuMode === 'trader'"
                    />
                    <MerchantMenu
                        v-show="menuMode === 'merchant'"
                    />
                    <AdminMenu
                        v-show="is_admin && menuMode === 'admin'"
                    />
<!--                    <div>
                        <ul class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                            <li>
                                <Link @click.prevent="openDocs" href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m14 9.006h-.335a1.647 1.647 0 0 1-1.647-1.647v-1.706a1.647 1.647 0 0 1 1.647-1.647L19 12M5 12v5h1.375A1.626 1.626 0 0 0 8 15.375v-1.75A1.626 1.626 0 0 0 6.375 12H5Zm9 1.5v2a1.5 1.5 0 0 1-1.5 1.5v0a1.5 1.5 0 0 1-1.5-1.5v-2a1.5 1.5 0 0 1 1.5-1.5v0a1.5 1.5 0 0 1 1.5 1.5Z"/>
                                    </svg>
                                    <span class="ms-3">Документация</span>
                                </Link>
                            </li>
                        </ul>
                    </div>-->
                    <div
                        v-show="menuMode !== 'admin'"
                        class="p-4 mt-6 rounded-lg border border-orange-400/25 bg-orange-200/10 dark:border-blue-400/25 dark:bg-blue-300/10"
                    >
                        <div class="flex items-center mb-3">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold me-2 px-2.5 py-0.5 rounded dark:bg-orange-200 dark:text-orange-900">Курс</span>
                            <span class="text-sm text-gray-700 font-semibold dark:text-white">Tether TRC-20</span>
                        </div>
                        <div class="text-sm text-blue-800 dark:text-blue-400">
                            <ul>
                                <li v-for="rate in rates">
                                    <span class="text-base text-gray-700 dark:text-gray-200 font-semibold mr-2">{{ rate.buy_price }}</span>
                                    <span class="text-xs font-semibold text-orange-400 dark:text-blue-400">{{ rate.code.toUpperCase() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>


            <div class="p-4 sm:ml-64">
                <!--max-w-7xl mx-auto  -->
                <div class="p-4 mt-14">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
