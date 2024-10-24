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
                            <div v-if="tab === 'settings'" class="grid grid-cols-2 gap-4">
                                <div>
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
                                    <div class="p-4 my-8 bg-white border border-gray-200 rounded-lg sm:p-6 lg:p-8 dark:bg-gray-800 dark:border-gray-700" aria-label="Subscribe to the Flowbite newsletter">
                                        <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">Обработчик платежей</h3>
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
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
