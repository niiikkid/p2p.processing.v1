<script setup>
import {Head, router} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    FwbButton,
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
import InvoiceStatus from "@/Components/InvoiceStatus.vue";

const invoices = usePage().props.invoices;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Заявки на вывод средств" />

        <MainTableSection
            title="Заявки на вывод средств"
            :data="invoices"
        >
            <template v-slot:body>
                <fwb-table>
                    <fwb-table-head>
                        <fwb-table-head-cell>ID</fwb-table-head-cell>
                        <fwb-table-head-cell>Сумма</fwb-table-head-cell>
                        <fwb-table-head-cell>Пользователь</fwb-table-head-cell>
                        <fwb-table-head-cell>Статус</fwb-table-head-cell>
                        <fwb-table-head-cell>Дата создания</fwb-table-head-cell>
                        <fwb-table-head-cell>
                            <span class="sr-only">Действия</span>
                        </fwb-table-head-cell>
                    </fwb-table-head>
                    <fwb-table-body>
                        <fwb-table-row v-for="invoice in invoices.data">
                            <fwb-table-cell>{{ invoice.id }}</fwb-table-cell>
                            <fwb-table-cell>{{ invoice.amount }} {{invoice.currency.toUpperCase()}}</fwb-table-cell>
                            <fwb-table-cell>{{ invoice.user.email }}</fwb-table-cell>
                            <fwb-table-cell>
                               <InvoiceStatus :status="invoice.status"></InvoiceStatus>
                            </fwb-table-cell>
                            <fwb-table-cell>{{ invoice.created_at }}</fwb-table-cell>
                            <fwb-table-cell>
<!--
                                <EditAction :link="route('admin.payment-gateways.edit', invoice.id)"></EditAction>
-->
                            </fwb-table-cell>
                        </fwb-table-row>
                    </fwb-table-body>
                </fwb-table>
            </template>
        </MainTableSection>
    </div>
</template>
