<script setup>
import {Head, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
} from 'flowbite-vue'
import EditAction from "@/Components/Table/EditAction.vue";
import MainTableSection from "@/Wrappers/MainTableSection.vue";

const currencies = usePage().props.currencies;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Валюты" />

        <MainTableSection
            title="Валюты"
            :data="currencies"
            :paginate="false"
        >
            <template v-slot:body>
                <fwb-table>
                    <fwb-table-head>
                        <fwb-table-head-cell>Код валюты</fwb-table-head-cell>
                        <fwb-table-head-cell>Покупка USDT</fwb-table-head-cell>
                        <fwb-table-head-cell>Продажа USDT</fwb-table-head-cell>
                        <fwb-table-head-cell>
                            <span class="sr-only">Действия</span>
                        </fwb-table-head-cell>
                    </fwb-table-head>
                    <fwb-table-body>
                        <fwb-table-row v-for="currency in currencies">
                            <fwb-table-cell>{{ currency.code.toUpperCase() }}</fwb-table-cell>
                            <fwb-table-cell>{{ currency.buy_price }}</fwb-table-cell>
                            <fwb-table-cell>{{ currency.sell_price }}</fwb-table-cell>
                            <fwb-table-cell>
                                <EditAction :link="route('admin.currencies.price-parsers.edit', currency.code)"></EditAction>
                            </fwb-table-cell>
                        </fwb-table-row>
                    </fwb-table-body>
                </fwb-table>
            </template>
        </MainTableSection>
    </div>
</template>
