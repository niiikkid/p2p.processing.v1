<script setup>
import {Head, router} from '@inertiajs/vue3';
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
import IsActiveStatus from "@/Components/IsActiveStatus.vue";
import EditAction from "@/Components/Table/EditAction.vue";
import MainTableSection from "@/Wrappers/MainTableSection.vue";
import AddMobileIcon from "@/Components/AddMobileIcon.vue";

const payment_gateways = usePage().props.paymentGateways;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Платежные методы" />

        <MainTableSection
            title="Платежные методы"
            :data="payment_gateways"
        >
            <template v-slot:button>
                <button
                    @click="router.visit(route('admin.payment-gateways.create'))"
                    type="button"
                    class="hidden md:block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                >
                    Создать метод
                </button>
                <AddMobileIcon
                    @click="router.visit(route('admin.payment-gateways.create'))"
                />
            </template>
            <template v-slot:body>
                <fwb-table>
                    <fwb-table-head>
                        <fwb-table-head-cell>Название</fwb-table-head-cell>
                        <fwb-table-head-cell>Код</fwb-table-head-cell>
                        <fwb-table-head-cell>Валюта</fwb-table-head-cell>
                        <fwb-table-head-cell>Мин/Макс лимит</fwb-table-head-cell>
                        <fwb-table-head-cell>Комиссия %</fwb-table-head-cell>
                        <fwb-table-head-cell>Парсеры</fwb-table-head-cell>
                        <fwb-table-head-cell>Статус</fwb-table-head-cell>
                        <fwb-table-head-cell>
                            <span class="sr-only">Действия</span>
                        </fwb-table-head-cell>
                    </fwb-table-head>
                    <fwb-table-body>
                        <fwb-table-row v-for="payment_gateway in payment_gateways.data">
                            <fwb-table-cell>{{ payment_gateway.name }}</fwb-table-cell>
                            <fwb-table-cell>{{ payment_gateway.code }}</fwb-table-cell>
                            <fwb-table-cell>{{ payment_gateway.currency }}</fwb-table-cell>
                            <fwb-table-cell>{{ payment_gateway.min_limit }}/{{ payment_gateway.max_limit }}</fwb-table-cell>
                            <fwb-table-cell>{{ payment_gateway.commission_rate }}% / {{ payment_gateway.service_commission_rate }}%</fwb-table-cell>
                            <fwb-table-cell>{{ payment_gateway.sms_parsers_count }}</fwb-table-cell>
                            <fwb-table-cell>
                                <IsActiveStatus :is_active="payment_gateway.is_active"></IsActiveStatus>
                            </fwb-table-cell>
                            <fwb-table-cell>
                                <EditAction :link="route('admin.payment-gateways.edit', payment_gateway.id)"></EditAction>
                            </fwb-table-cell>
                        </fwb-table-row>
                    </fwb-table-body>
                </fwb-table>
            </template>
        </MainTableSection>
    </div>
</template>
