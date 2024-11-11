<script setup>
import { Link, Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
    FwbButton
} from 'flowbite-vue'
import EditAction from "@/Components/Table/EditAction.vue";
import MainTableSection from "@/Wrappers/MainTableSection.vue";
import AddMobileIcon from "@/Components/AddMobileIcon.vue";

const users = usePage().props.users;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Пользователи" />

        <MainTableSection
            title="Пользователи"
            :data="users"
        >
            <template v-slot:button>
                <fwb-button
                    @click="router.visit(route('admin.users.create'))"
                    color="default"
                    class="hidden md:block"
                >Создать пользователя</fwb-button>
                <AddMobileIcon
                    @click="router.visit(route('admin.users.create'))"
                />
            </template>
            <template v-slot:body>
                <fwb-table>
                    <fwb-table-head>
                        <fwb-table-head-cell>ID</fwb-table-head-cell>
                        <fwb-table-head-cell>Имя</fwb-table-head-cell>
                        <fwb-table-head-cell>Почта</fwb-table-head-cell>
                        <fwb-table-head-cell>Роль</fwb-table-head-cell>
                        <fwb-table-head-cell>Создан</fwb-table-head-cell>
                        <fwb-table-head-cell>
                            <span class="sr-only">Действия</span>
                        </fwb-table-head-cell>
                    </fwb-table-head>
                    <fwb-table-body>
                        <fwb-table-row v-for="user in users.data">
                            <fwb-table-cell>{{ user.id }}</fwb-table-cell>
                            <fwb-table-cell>
                                <div class="inline-flex">
                                    <span>{{ user.name }}</span>
                                    <span
                                        v-if="user.banned_at"
                                        class="ml-2"
                                    >
                                        <svg class="w-4 h-4 text-red-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>
                            </fwb-table-cell>
                            <fwb-table-cell>{{ user.email }}</fwb-table-cell>
                            <fwb-table-cell>{{ user.role.name }}</fwb-table-cell>
                            <fwb-table-cell class="text-nowrap">{{ user.created_at }}</fwb-table-cell>
                            <fwb-table-cell class="text-nowrap">
                                <Link
                                    :href="route('admin.users.wallet.index', user.id)"
                                    class="mr-2 px-0 py-0 text-yellow-400 hover:text-yellow-500 dark:text-yellow-500 dark:hover:text-yellow-600 inline-flex items-center hover:underline"
                                >
                                    <svg class="w-[22px] h-[22px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                                    </svg>
                                </Link>
                                <EditAction :link="route('admin.users.edit', user.id)"></EditAction>
                            </fwb-table-cell>
                        </fwb-table-row>
                    </fwb-table-body>
                </fwb-table>
            </template>
        </MainTableSection>
    </div>
</template>
