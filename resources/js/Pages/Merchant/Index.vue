<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    FwbButton,
} from 'flowbite-vue'
import { usePage } from '@inertiajs/vue3';
import MainTableSection from "@/Wrappers/MainTableSection.vue";
import HeadllesTable from "@/Components/HeadlesTable/HeadllesTable.vue";
import HeadlessTableTr from "@/Components/HeadlesTable/HeadlessTableTr.vue";
import HeadlessTableTh from "@/Components/HeadlesTable/HeadlessTableTh.vue";
import HeadlessTableTd from "@/Components/HeadlesTable/HeadlessTableTd.vue";

const merchants = usePage().props.merchants;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Мерчанты" />

        <MainTableSection
            title="Мерчанты"
            :data="merchants"
        >
            <template v-slot:button>
                <div v-if="!route().current('admin.*')">
                    <fwb-button @click="router.visit(route('merchants.create'))" color="default">Создать мерчант</fwb-button>
                </div>
            </template>
            <template v-slot:body>
                <HeadllesTable>
                    <HeadlessTableTr
                        v-for="merchant in merchants.data"
                        @click="router.visit(route('merchants.show', merchant.id))"
                        :hoverable="true"
                    >
                        <HeadlessTableTh>#{{ merchant.id }}</HeadlessTableTh>
                        <HeadlessTableTd>{{merchant.name}}</HeadlessTableTd>
                        <HeadlessTableTd>{{merchant.project_link}}</HeadlessTableTd>
                        <HeadlessTableTd>
                            <div class="flex items-center text-nowrap text-gray-900 dark:text-gray-200">
                                <template v-if="merchant.active">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Включен
                                </template>
                                <template v-else>
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> Выключен
                                </template>
                            </div>
                        </HeadlessTableTd>
                    </HeadlessTableTr>
                </HeadllesTable>
            </template>
        </MainTableSection>
    </div>
</template>
