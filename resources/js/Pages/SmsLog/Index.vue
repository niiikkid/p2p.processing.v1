<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
} from 'flowbite-vue'
import { usePage } from '@inertiajs/vue3';
import MainTableSection from "@/Wrappers/MainTableSection.vue";
import {useViewStore} from "@/store/view.js";

const viewStore = useViewStore();
const sms_logs = usePage().props.sms_logs;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Сообщения" />

        <MainTableSection
            title="Сообщения"
            :data="sms_logs"
        >
            <template v-slot:body>
                <fwb-table>
                    <fwb-table-head>
                        <fwb-table-head-cell>ID</fwb-table-head-cell>
                        <fwb-table-head-cell>Отправитель</fwb-table-head-cell>
                        <fwb-table-head-cell>Сообщение</fwb-table-head-cell>
                        <fwb-table-head-cell>Тип</fwb-table-head-cell>
                        <fwb-table-head-cell v-if="viewStore.isAdminViewMode">Владелец</fwb-table-head-cell>
                        <fwb-table-head-cell>Время</fwb-table-head-cell>
                        <fwb-table-head-cell>
                            <span class="sr-only">Действия</span>
                        </fwb-table-head-cell>
                    </fwb-table-head>
                    <fwb-table-body>
                        <fwb-table-row v-for="sms_log in sms_logs.data">
                            <fwb-table-cell>{{ sms_log.id }}</fwb-table-cell>
                            <fwb-table-cell>{{ sms_log.sender }}</fwb-table-cell>
                            <fwb-table-cell>{{ sms_log.message }}</fwb-table-cell>
                            <fwb-table-cell>{{ sms_log.type }}</fwb-table-cell>
                            <fwb-table-cell v-if="viewStore.isAdminViewMode">{{ sms_log.user.email }}</fwb-table-cell>
                            <fwb-table-cell>{{ sms_log.timestamp }}</fwb-table-cell>
                            <fwb-table-cell></fwb-table-cell>
                        </fwb-table-row>
                    </fwb-table-body>
                </fwb-table>
            </template>
        </MainTableSection>
    </div>
</template>
