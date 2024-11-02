<script setup>
import {Head, router, useForm} from '@inertiajs/vue3';
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
import InvoiceStatus from "@/Components/InvoiceStatus.vue";
import SuccessAction from "@/Components/Table/SuccessAction.vue";
import FailAction from "@/Components/Table/FailAction.vue";
import {useModalStore} from "@/store/modal.js";
import ConfirmModal from "@/Components/Modals/ConfirmModal.vue";

const modalStore = useModalStore();

const invoices = usePage().props.invoices;

const confirmSuccessWithdrawal = (invoice) => {
    modalStore.openConfirmModal({
        title: 'Вы уверены что хотите завершить заявку как успешную?',
        confirm_button_name: 'Подтвердить',
        confirm: () => {
            useForm({}).patch(route('admin.withdrawals.success', invoice.id), {
                preserveScroll: true,
                onFinish: () => {
                    router.visit(route('admin.withdrawals.index'))
                },
            });
        }
    });
};

const confirmFailParser = (invoice) => {
    modalStore.openConfirmModal({
        title: 'Вы уверены что хотите отклонить заявку?',
        confirm_button_name: 'Отклонить',
        confirm: () => {
            useForm({}).patch(route('admin.withdrawals.fail', invoice.id), {
                preserveScroll: true,
                onFinish: () => {
                    router.visit(route('admin.withdrawals.index'))
                },
            });
        }
    });
};

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
                                <template v-if="invoice.status === 'pending'">
                                    <SuccessAction @click.prevent="confirmSuccessWithdrawal(invoice)"/>
                                    <FailAction class="ml-3" @click.prevent="confirmFailParser(invoice)"/>
                                </template>
                            </fwb-table-cell>
                        </fwb-table-row>
                    </fwb-table-body>
                </fwb-table>
            </template>
        </MainTableSection>

        <ConfirmModal/>
    </div>
</template>
