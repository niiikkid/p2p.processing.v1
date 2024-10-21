<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PaymentDetail from "@/Components/PaymentDetail.vue";
import {ref} from "vue";
import DisputeStatus from "@/Components/DisputeStatus.vue";
import {useModalStore} from "@/store/modal.js";
import DisputeModal from "@/Modals/DisputeModal.vue";
import CancelDisputeModal from "@/Modals/CancelDisputeModal.vue";
import SmsLogsModal from "@/Modals/SmsLogsModal.vue";
import ConfirmModal from "@/Components/Modals/ConfirmModal.vue";
import MainTableSection from "@/Wrappers/MainTableSection.vue";
import HeadllesTable from "@/Components/HeadlesTable/HeadllesTable.vue";
import HeadlessTableTr from "@/Components/HeadlesTable/HeadlessTableTr.vue";
import HeadlessTableTh from "@/Components/HeadlesTable/HeadlessTableTh.vue";
import HeadlessTableTd from "@/Components/HeadlesTable/HeadlessTableTd.vue";
import DateTime from "@/Components/DateTime.vue";

const disputes = usePage().props.disputes;
const modalStore = useModalStore();

const openPage = (page) => {
    router.visit(route((route().current('admin.*') ? 'admin.' : '') + 'disputes.index'), { data: {
            page
        } })
}

const confirmAcceptDispute = (dispute) => {
    modalStore.openConfirmModal({
        title: 'Вы уверены что хотите принять спор #' + dispute?.id + '?',
        body: 'В таком случае, сделка будет закрыта как оплаченная.',
        confirm_button_name: 'Принять спор',
        confirm: () => {
            useForm({}).patch(route('disputes.accept', dispute.id), {
                preserveScroll: true,
                onFinish: () => {
                    modalStore.closeAll()
                    router.visit(route((route().current('admin.*') ? 'admin.' : '') + 'disputes.index'), {
                        only: ['disputes'],
                    })
                },
            });
        }
    });
}

const confirmRollbackDispute = (dispute) => {
    modalStore.openConfirmModal({
        title: 'Вы уверены что хотите открыть спор #' + dispute?.id + '?',
        body: 'Референтная сделка не изменит свой статус.',
        confirm_button_name: 'Открыть спор',
        confirm: () => {
            useForm({}).patch(route('disputes.rollback', dispute.id), {
                preserveScroll: true,
                onFinish: () => {
                    modalStore.closeAll()
                    router.visit(route((route().current('admin.*') ? 'admin.' : '') + 'disputes.index'), {
                        only: ['disputes'],
                    })
                },
            });
        }
    });
};

const is_admin = usePage().props.auth.is_admin;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Споры" />

        <MainTableSection
            title="Споры по сделкам"
            :data="disputes"
        >
            <template v-slot:body>
                <HeadllesTable>
                    <HeadlessTableTr
                        v-for="dispute in disputes.data"
                        @click="modalStore.openDisputeModal({dispute})"
                        :hoverable="true"
                    >
                        <HeadlessTableTh>#{{ dispute.id }}</HeadlessTableTh>
                        <HeadlessTableTd>
                            <PaymentDetail
                                :detail="dispute.payment_detail.detail"
                                :type="dispute.payment_detail.type"
                                :copyable="false"
                                class="dark:text-gray-200"
                            ></PaymentDetail>
                            <div class="text-nowrap dark:text-gray-500">{{ dispute.payment_detail.name }}</div>
                        </HeadlessTableTd>
                        <HeadlessTableTd>
                            <div class="text-nowrap dark:text-gray-200">{{ dispute.order.amount }} {{dispute.order.currency.toUpperCase()}}</div>
                            <div class="text-nowrap dark:text-gray-500">{{ dispute.order.amount_usdt }} {{dispute.order.profit_currency.toUpperCase()}}</div>
                        </HeadlessTableTd>
                        <HeadlessTableTd v-if="is_admin">
                            <div class="text-nowrap dark:text-gray-200">Трейдер</div>
                            <div class="text-nowrap dark:text-gray-500">{{ dispute.user.email }}</div>
                        </HeadlessTableTd>
                        <HeadlessTableTd>
                            <DisputeStatus :status="dispute.status"></DisputeStatus>
                        </HeadlessTableTd>
                        <HeadlessTableTd>
                            <DateTime :data="dispute.created_at"></DateTime>
                        </HeadlessTableTd>
                    </HeadlessTableTr>
                </HeadllesTable>
            </template>
        </MainTableSection>

        <DisputeModal
            @accept="confirmAcceptDispute"
            @cancel="modalStore.openDisputeCancelModal({dispute:$event})"
            @rollback="confirmRollbackDispute"
        />

        <CancelDisputeModal/>

        <SmsLogsModal/>
        <ConfirmModal/>
    </div>
</template>

<style scoped>

</style>
