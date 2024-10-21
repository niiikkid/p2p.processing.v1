<script setup>
import {usePage, router} from '@inertiajs/vue3';
import {onMounted, ref} from 'vue'
import { initFlowbite } from 'flowbite'
import MenuModeSwitcher from "@/Layouts/Partials/MenuModeSwitcher.vue";
import TraderMenu from "@/Layouts/Partials/TraderMenu.vue";
import AdminMenu from "@/Layouts/Partials/AdminMenu.vue";
import NavBar from "@/Layouts/Partials/NavBar.vue";

const is_admin = usePage().props.auth.is_admin;

const menuMode = ref('trader');

const switchMenuMode = (mode) => {
    menuMode.value = mode;
}

// initialize components based on data attribute selectors
onMounted(() => {
    if (route().current('admin.*')) {
        switchMenuMode('admin')
    }

    initFlowbite();
    document.documentElement.classList.add('dark');
})

router.on('success', (event) => {
    initFlowbite()
})
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
                    <AdminMenu
                        v-show="is_admin && menuMode === 'admin'"
                    />
                </div>
            </aside>


            <div class="p-4 sm:ml-64">
                <!--max-w-7xl mx-auto  -->
                <div class="p-2 mt-14">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
