<script setup>
import {Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimeTimeBonus from "@/Pages/Settings/Partials/PrimeTimeBonus.vue";
import SupportLink from "@/Pages/Settings/Partials/SupportLink.vue";
import LogoUpload from "@/Pages/Settings/Partials/LogoUpload.vue";

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <Head title="Настройки системы" />

    <div>
        <div>
            <div class="mx-auto space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <PrimeTimeBonus class="max-w-xl"></PrimeTimeBonus>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <SupportLink class="max-w-xl"></SupportLink>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <LogoUpload class="max-w-xl"></LogoUpload>
                </div>
            </div>
        </div>
    </div>
</template>
