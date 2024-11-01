<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    FwbButton,
} from 'flowbite-vue'
import { usePage } from '@inertiajs/vue3';
import IsActiveStatus from "@/Components/IsActiveStatus.vue";
import EditAction from "@/Components/Table/EditAction.vue";
import PaymentDetail from "@/Components/PaymentDetail.vue";
import PaymentDetailLimit from "@/Components/PaymentDetailLimit.vue";
import MainTableSection from "@/Wrappers/MainTableSection.vue";
import HeadllesTable from "@/Components/HeadlesTable/HeadllesTable.vue";
import HeadlessTableTr from "@/Components/HeadlesTable/HeadlessTableTr.vue";
import HeadlessTableTh from "@/Components/HeadlesTable/HeadlessTableTh.vue";
import HeadlessTableTd from "@/Components/HeadlesTable/HeadlessTableTd.vue";
import {useViewStore} from "@/store/view.js";

const viewStore = useViewStore();
const payment_details = usePage().props.paymentDetails;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Реквизиты" />

        <MainTableSection
            title="Реквизиты"
            :data="payment_details"
        >
            <template v-slot:button>
                <fwb-button @click="router.visit(route(viewStore.adminPrefix + 'payment-details.create'))" color="default">Создать реквизиты</fwb-button>
            </template>
            <template v-slot:body>
                <HeadllesTable>
                    <HeadlessTableTr v-for="payment_detail in payment_details.data">
                        <HeadlessTableTh>#{{ payment_detail.id }}</HeadlessTableTh>
                        <HeadlessTableTd>
                            <div class="text-nowrap text-gray-900 dark:text-gray-200">{{ payment_detail.name }}</div>
                            <div class="text-nowrap text-gray-500 dark:text-gray-500">
                                {{ payment_detail.payment_gateway_name }}
                            </div>
                        </HeadlessTableTd>
                        <HeadlessTableTd class="text-gray-900 dark:text-gray-200">
                            <PaymentDetail :detail="payment_detail.detail" :type="payment_detail.detail_type"></PaymentDetail>
                        </HeadlessTableTd>
                        <HeadlessTableTd v-if="viewStore.isMerchantViewMode" class="text-gray-900 dark:text-gray-200">
                            {{ payment_detail.owner_email }}
                        </HeadlessTableTd>
                        <HeadlessTableTd>
                            <PaymentDetailLimit :current_daily_limit="payment_detail.current_daily_limit" :daily_limit="payment_detail.daily_limit"></PaymentDetailLimit>
                        </HeadlessTableTd>
                        <HeadlessTableTd>
                            <IsActiveStatus :is_active="payment_detail.is_active"></IsActiveStatus>
                        </HeadlessTableTd>
                        <HeadlessTableTd>
                            <div class="flex justify-center gap-2">
                                <EditAction :link="route(viewStore.adminPrefix + 'payment-details.edit', payment_detail.id)"></EditAction>
                            </div>
                        </HeadlessTableTd>
                    </HeadlessTableTr>
                </HeadllesTable>
            </template>
        </MainTableSection>
    </div>
</template>
