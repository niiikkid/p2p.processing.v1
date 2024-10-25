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
<!--                <HeadllesTable>
                    <HeadlessTableTr
                        v-for="merchant in merchants.data"
                        @click="router.visit(route('merchants.show', merchant.id))"
                        :hoverable="true"
                    >
                        <HeadlessTableTh>#{{ merchant.id }}</HeadlessTableTh>
                        <HeadlessTableTd>{{merchant.name}}</HeadlessTableTd>
                        <HeadlessTableTd>{{merchant.domain}}</HeadlessTableTd>
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
                </HeadllesTable>-->
                <section class="bg-gray-50 antialiased dark:bg-gray-900">
                    <div class="mx-auto max-w-screen-xl 2xl:px-0">
                        <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-3">
                            <div
                                v-for="merchant in merchants.data"
                                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                            >
                                <div>
                                    <div class="text-lg font-semibold leading-tight text-gray-900 dark:text-gray-200">
                                        {{ merchant.name }}
                                    </div>

                                    <div class="mt-2 flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">доход за сегодня</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">2400$</p>
                                    </div>

                                    <p class="mt-2 text-lg font-extrabold leading-tight text-gray-900 dark:text-blue-500">{{ merchant.domain }}</p>

                                    <div class="mt-4 text-sm flex items-end justify-between">
                                        <ul class="flex items-center">
                                            <div class="flex items-center text-nowrap text-gray-900 dark:text-gray-200">
                                                <template v-if="merchant.validated_at">
                                                    <div class="h-2.5 w-2.5 rounded-full bg-yellow-500 me-2"></div> На модерации
                                                </template>
                                                <template v-else-if="merchant.banned_at">
                                                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> Заблокирован
                                                </template>
                                                <template v-else-if="merchant.active">
                                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Включен
                                                </template>
                                                <template v-else>
                                                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> Выключен
                                                </template>
                                            </div>
                                        </ul>

                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-primary-700 px-0 py-0 text-sm font-medium text-white hover:text-blue-500 hover:bg-primary-800 focus:outline-none focus:ring-4  focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                            @click.prevent="router.visit(route('merchants.show', merchant.id))"
                                        >
                                            Перейти
                                            <svg class="ml-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>
        </MainTableSection>
    </div>
</template>
