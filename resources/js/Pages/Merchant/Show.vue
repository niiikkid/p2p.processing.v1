<script setup>
import {Head, router, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GoBackButton from "@/Components/GoBackButton.vue";
import {computed, onMounted, ref} from "vue";
import HeadlessTableTd from "@/Components/HeadlesTable/HeadlessTableTd.vue";
import OrderStatus from "@/Components/OrderStatus.vue";
import HeadlessTableTh from "@/Components/HeadlesTable/HeadlessTableTh.vue";
import HeadllesTable from "@/Components/HeadlesTable/HeadllesTable.vue";
import HeadlessTableTr from "@/Components/HeadlesTable/HeadlessTableTr.vue";
import DateTime from "@/Components/DateTime.vue";
import {FwbPagination} from "flowbite-vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputHelper from "@/Components/InputHelper.vue";
import SaveButton from "@/Components/Form/SaveButton.vue";

const merchant = usePage().props.merchant;
const orders = usePage().props.orders;

const form = useForm({
    callback_url: '',
});

const tab = ref('statistics');

const openPage = (page = null) => {
    let data = {
        tab: tab.value
    };
    if (page) {
        data.page = page;
    }
    router.visit(route(route().current(), merchant.id), { data: data})
}

onMounted(() => {
    let urlParams = new URLSearchParams(window.location.search);
    tab.value = urlParams.get('tab') ?? 'statistics'
})

const currentPage = ref(orders?.meta?.current_page)

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head :title="'Мерчант - ' + merchant.name"/>

        <div class="mx-auto space-y-4">
            <div class="mb-3">
                <GoBackButton @click="router.visit(route('merchants.index'))"/>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Мерчант - {{ merchant.name }}
                        </h2>
                    </header>

                    <div>
                        <div class="mt-6 space-y-6">
                            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                                <li class="me-2">
                                    <a @click.prevent="tab = 'statistics'; openPage()" href="#" :class="tab === 'statistics' ? 'inline-flex px-4 py-2 text-white bg-blue-600 rounded-lg active' : 'inline-flex px-4 py-2 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'" aria-current="page">
                                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"/>
                                        </svg>
                                        Статистика
                                    </a>
                                </li>
                                <li class="me-2">
                                    <a @click.prevent="tab = 'payments'; openPage(1)" href="#" :class="tab === 'payments' ? 'inline-flex px-4 py-2 text-white bg-blue-600 rounded-lg active' : 'inline-flex px-4 py-2 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'" aria-current="page">
                                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2"/>
                                        </svg>
                                        Платежи
                                    </a>
                                </li>
                                <li class="me-2">
                                    <a @click.prevent="tab = 'settings'; openPage()" href="#" :class="tab === 'settings' ? 'inline-flex px-4 py-2 text-white bg-blue-600 rounded-lg active' : 'inline-flex px-4 py-2 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'" aria-current="page">
                                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z"/>
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                        </svg>
                                        Настройки
                                    </a>
                                </li>
                            </ul>
                            <div v-if="tab === 'statistics'">
                                <section>
                                    <div class="mx-auto text-center">
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg grid max-w-full mx-auto text-gray-900 lg:grid-cols-4 sm:grid-cols-3 dark:text-white">
                                            <div class="border-r border-gray-200 dark:border-gray-700 py-5 flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">100  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Доход за сегодня</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 126
                                                </div>
                                            </div>
                                            <div class="border-r border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">200  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Доход за вчера</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 240
                                                </div>
                                            </div>
                                            <div class="border-r border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">140  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Доход за месяц</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 500
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">50  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Даход за все время</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 1400
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div v-if="tab === 'payments'">
                                <h2 class="text-gray-500 text-sm">Здесь отображаются только завершенные платежи</h2>
                                <HeadllesTable>
                                    <HeadlessTableTr
                                        v-for="order in orders.data"
                                    >
                                        <HeadlessTableTh>#{{ order.id }}</HeadlessTableTh>
                                        <HeadlessTableTd class="px-6 py-2">
                                            <div class="text-nowrap text-gray-900 dark:text-gray-200">{{ order.amount }} {{ order.currency.toUpperCase() }}</div>
                                        </HeadlessTableTd>
                                        <HeadlessTableTd class="px-6 py-2">
                                            <div class="text-nowrap text-gray-900 dark:text-gray-200">{{ order.amount_usdt }} {{ order.profit_currency.toUpperCase() }}</div>
                                        </HeadlessTableTd>
                                        <HeadlessTableTd>
                                            <DateTime class="justify-center" :data="order.created_at"/>
                                        </HeadlessTableTd>
                                    </HeadlessTableTr>
                                </HeadllesTable>
                                <fwb-pagination
                                    v-model="currentPage"
                                    :total-items="orders.meta.total"
                                    previous-label="Назад" next-label="Вперед"
                                    @page-changed="openPage"
                                    :per-page="orders.meta.per_page"
                                ></fwb-pagination>
                            </div>
                            <div v-if="tab === 'settings'" class="grid grid-cols-5 gap-8">
                                <div class="col-span-2">
                                    <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">Магазин</h3>

                                    <ul class="text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <li class="w-full px-4 py-3 border-b border-gray-200 rounded-t-lg dark:border-gray-600 flex justify-between">
                                            <span class="text-gray-900">Название</span>
                                            <span class="text-gray-500">Пам Пам</span>
                                        </li>
                                        <li class="w-full px-4 py-3 border-b border-gray-200 dark:border-gray-600 flex justify-between">
                                            <span class="text-gray-900">Описание</span>
                                            <span class="text-gray-500">УКП ЙУКП ЦУП УЦКПЦУКП </span>
                                        </li>
                                        <li class="w-full px-4 py-3 border-b border-gray-200 dark:border-gray-600 flex justify-between">
                                            <span class="text-gray-900">Домен</span>
                                            <span class="text-gray-500">laravel.com</span>
                                        </li>
                                        <li class="w-full px-4 py-3 border-b border-gray-200 rounded-t-lg dark:border-gray-600 flex justify-between">
                                            <span>Статус</span>
                                                <span>
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Остановлен</span>
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Активен</span>
                                            </span>
                                        </li>
                                        <li class="w-full px-4 py-3 rounded-b-lg flex justify-between">
                                            <span class="text-gray-900">Secret Key</span>
                                            <span class="text-gray-500">rwehwerkghjjienrglkwenrg</span>
                                        </li>
                                    </ul>
                                    <div class="my-8">
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-3">Обработчик платежей</h3>
                                        <div class="p-6 bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                            <p class="mb-5 text-sm font-medium text-gray-500 dark:text-gray-300">
                                                Установите ссылку на Ваш обработчик для получения уведомлений. По ней мы будем отправлять POST запросы о статусах платежей.
                                            </p>
                                            <form class="space-y-4" method="post">
                                                <div>
                                                    <InputLabel
                                                        for="callback_url"
                                                        value="Укажите ссылку"
                                                        :error="!!form.errors.callback_url"
                                                    />

                                                    <TextInput
                                                        id="callback_url"
                                                        v-model="form.callback_url"
                                                        type="text"
                                                        class="mt-1 block w-full"
                                                        placeholder="https://example.com/webhook"
                                                        :error="!!form.errors.callback_url"
                                                        @input="form.clearErrors('callback_url')"
                                                    />

                                                    <InputError :message="form.errors.callback_url" class="mt-2" />
                                                </div>

                                                <SaveButton
                                                    :disabled="form.processing"
                                                    :saved="form.recentlySuccessful"
                                                ></SaveButton>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-3">
                                    <div class="bg-white">
                                        <div class="flex justify-between items-center mb-3">
                                            <h3 class="text-xl font-medium text-gray-900 dark:text-white">Комиссии</h3>
                                            <div class="flex items-center">
                                                <span class="flex text-xs text-gray-900 mr-3">
                                                    <svg class="w-4 h-4 text-gray-500 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z"/>
                                                    </svg>
                                                    Комиссия магазина
                                                </span>
                                                <span class="flex text-xs text-gray-900 mr-3">
                                                    <svg class="w-4 h-4 text-gray-500 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                    </svg>
                                                    Комиссия клиента
                                                </span>
                                                <button type="button" class="px-2 py-1 text-xs font-medium text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                                                    Изменить
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-5">
                                            <div>
                                                <span class="text-xs font-semibold py-1 px-3 border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                                    RUB
                                                </span>
                                            </div>
                                            <div class="mt-3 grid grid-cols-2 gap-2">
                                                <div class="text-sm font-semibold py-2 px-3 border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <div class="text-gray-900">SBP</div>
                                                            <div class="text-xs flex">
                                                                <div class="flex items-center mr-1 text-gray-500">
                                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z"/>
                                                                    </svg>
                                                                    0.0%
                                                                </div>
                                                                <div class="flex items-center text-gray-500">
                                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                                    </svg>
                                                                    9.0%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-gray-900 text-xl">
                                                            9%
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        <svg class="w-4 h-4 text-gray-500 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z"/>
                                                        </svg>
                                                        <input type="range" value="0" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer range-sm dark:bg-gray-700">
                                                        <svg class="w-4 h-4 text-gray-500 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div>
                                                <span class="text-xs font-semibold py-1 px-3 border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                                    RUB
                                                </span>
                                            </div>
                                            <div class="mt-3 grid grid-cols-2 gap-2">
                                                <div class="flex justify-between items-center text-sm font-semibold py-2 px-3 border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                                                    <div>
                                                        <div class="text-gray-900">SBP</div>
                                                        <div class="text-xs flex">
                                                            <div class="flex items-center mr-1 text-gray-500">
                                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z"/>
                                                                </svg>
                                                                0.0%
                                                            </div>
                                                            <div class="flex items-center text-gray-500">
                                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                                </svg>
                                                                9.0%
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-gray-900 text-xl">
                                                        9%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
