<script setup>
import {Head, router, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {FwbPagination} from 'flowbite-vue'
import {onMounted, ref} from "vue";
import GoBackButton from "@/Components/GoBackButton.vue";
import DepositModal from "@/Modals/Wallet/DepositModal.vue";
import {useModalStore} from "@/store/modal.js";
import WithdrawalModal from "@/Modals/Wallet/WithdrawalModal.vue";
import EmptyTable from "@/Components/EmptyTable.vue";

const wallet = usePage().props.wallet;
const reserve_balance = usePage().props.reserve_balance;
const invoices = usePage().props.invoices;
const transactions = usePage().props.transactions;
const user = usePage().props.user;
const modalStore = useModalStore();
const currentPage = ref(invoices.meta.current_page)
const activeTab = ref(null);


const openPage = (page) => {
    if (route().current('admin.*')) {
        router.visit(route('admin.users.wallet.index', user.id), {
            data: {
                page,
                tab: activeTab.value
            },
        })
    } else {
        router.visit(route('wallet.index'), {
            data: {
                page,
                tab: activeTab.value
            },
        })
    }
}

onMounted(() => {
    let urlParams = new URLSearchParams(window.location.search);
    activeTab.value = urlParams.get('tab') ?? 'invoices'
})

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <Head title="Финансы"/>

    <div>
        <div v-if="route().current('admin.*')" class="mb-3">
            <GoBackButton
                @click="router.visit(route('admin.users.index'))"
            ></GoBackButton>
        </div>

        <h2 v-if="route().current('admin.*')" class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl mb-4">Балансы пользователя: <span class="text-blue-500">{{user.email}}</span></h2>
        <h2 v-else class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl mb-4">Балансы</h2>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 md:grid-cols-2 mb-6">
            <div class="grow sm:mt-8 lg:mt-0">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div>
                        <div class="flex justify-between">
                            <div class="text-xl text-gray-900 dark:text-gray-200">Траст</div>
                            <div v-if="route().current('admin.*')">
                                <button
                                    @click.prevent="modalStore.openWithdrawalModal({user})"
                                    type="button"
                                    class="px-2 py-1 text-xs font-medium text-center inline-flex items-center text-gray-900 bg-white border border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                >
                                    <svg class="w-4 h-4 mr-1 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                                    </svg>
                                    Снять
                                </button>
                                <button
                                    @click.prevent="modalStore.openDepositModal({user})"
                                    type="button"
                                    class="ml-1.5 px-2 py-1 text-xs font-medium text-center inline-flex items-center text-gray-900 bg-white border border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                >
                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                                    </svg>
                                    Пополнить
                                </button>
                            </div>
                            <div v-else>
                                <button
                                    @click.prevent="false"
                                    type="button"
                                    class="px-2 py-1 text-xs font-medium text-center inline-flex items-center text-gray-900 bg-white border border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                    data-tooltip-target="tooltip-default"
                                >
                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                                    </svg>
                                    Пополнить
                                </button>
                                <div id="tooltip-default" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    В данный момент пополнение возможно только через администратора
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-5 inline-block align-middle">
                            <span class="text-xl font-bold text-gray-900 dark:text-gray-200">{{ wallet.trast_balance }} USDT</span>
                            <span class="ml-3 inline-flex bg-gray-100 text-gray-800 text-sm font-medium me-2 px-3 py-1.5 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z"/>
                                 </svg>
                                {{ reserve_balance }} USDT
                            </span>
                        </div>

                        <div class="inline-flex pt-3">
                            <div class="text-sm text-gray-900 dark:text-gray-400">
                                Зарезервировано
                            </div>
                            <div class="text-sm dark:text-gray-200 ml-1.5">
                                {{ wallet.reserve_balance }} USDT
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl mb-3">История операций</h2>

        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
            <li class="me-2">
                <a
                    @click.prevent="activeTab = 'invoices'; openPage(1)"
                    href="#"
                    :class="activeTab === 'invoices' ? 'inline-block px-4 py-3 text-white bg-blue-600 rounded-lg active' : 'inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'"
                    aria-current="page"
                >
                    Депозит/Вывод
                </a>
            </li>
            <li class="me-2">
                <a
                    @click.prevent="activeTab = 'transactions'; openPage(1)"
                    href="#"
                    :class="activeTab === 'transactions' ? 'inline-block px-4 py-3 text-white bg-blue-600 rounded-lg active' : 'inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'"
                    aria-current="page"
                >
                    Все операции
                </a>
            </li>
        </ul>

        <div v-if="activeTab === 'invoices'">
            <div class="mx-auto space-y-2">
                <EmptyTable v-if="!invoices.data.length"/>
                <template v-else>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border-separate border-spacing-y-3 rounded">
                        <tbody>
                        <tr
                            v-for="invoice in invoices.data"
                            class="bg-white dark:bg-gray-800 rounded"
                        >
                            <th
                                scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-gray-200 rounded-l-xl  border border-r-0 dark:border-gray-700"
                            >
                                <div class="flex items-center">
                                    <div class="mr-3">
                                    <span v-if="invoice.type === 'deposit'" class="inline-flex px-2.5 py-2.5 rounded-2xl dark:bg-green-800/50 dark:text-green-300">
                                        <svg class="w-5 h-5 text-gray-800 dark:text-green-200/80" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </span>
                                        <span v-if="invoice.type === 'withdrawal'" class="inline-flex px-2.5 py-2.5 rounded-2xl dark:bg-red-800/50 dark:text-red-300">
                                        <svg class="w-5 h-5 text-gray-800 dark:text-red-200/80" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </span>
                                        <!--                                    <span v-if="invoice.status === 'success'" class="inline-flex px-2.5 py-2.5 rounded-2xl dark:bg-green-800/50 dark:text-green-300">
                                                                                <svg class="w-5 h-5 text-gray-800 dark:text-green-200/80" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                                                                                </svg>
                                                                            </span>
                                                                            <span v-if="invoice.status === 'fail'" class="inline-flex px-2.5 py-2.5 rounded-2xl dark:bg-red-800/50 dark:text-red-300">
                                                                                <svg class="w-5 h-5 text-gray-800 dark:text-red-200/80" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                                                                                </svg>
                                                                            </span>
                                                                            <span v-if="invoice.status === 'pending'" class="inline-flex px-2.5 py-2.5 rounded-2xl dark:bg-yellow-700/50 dark:text-yellow-300">
                                                                                <svg class="w-5 h-5 text-gray-800 dark:text-yellow-200/80" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                                                                                </svg>
                                                                            </span>-->
                                    </div>
                                    <div class="dark:text-gray-400">#{{ invoice.id }}</div>
                                </div>
                            </th>
                            <!--                        <td class="p-3 border border-x-0 dark:border-gray-700">
                                                        <div class="text-nowrap dark:text-gray-400 text-center">
                                                            <template v-if="invoice.type === 'deposit'">Пополнение</template>
                                                            <template v-if="invoice.type === 'withdrawal'">Вывод</template>
                                                        </div>
                                                    </td>-->
                            <td class="p-3 border border-x-0 dark:border-gray-700">
                                <div class="text-nowrap dark:text-gray-400 text-center">
                                    <template v-if="invoice.type === 'deposit'">+</template>
                                    <template v-if="invoice.type === 'withdrawal'">-</template>
                                    {{ invoice.amount }} {{ invoice.currency.toUpperCase() }}
                                </div>
                            </td>
                            <td class="p-3 border border-x-0 dark:border-gray-700">
                                <div class="flex justify-center gap-2 dark:text-gray-400">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z" />
                                    </svg>
                                    <p class="font-medium inline-block align-middle">{{ invoice.created_at }}</p>
                                </div>
                            </td>
                            <td class="p-3 rounded-r-xl border border-l-0 dark:border-gray-700">
                                <div class="flex justify-end">
                                <span v-if="invoice.type === 'deposit'" class="inline-flex mr-2 px-4 py-2.5 rounded-lg dark:bg-green-800/50 dark:text-green-200/80">
                                    Зачисление
                                </span>
                                    <span v-if="invoice.type === 'withdrawal'" class="inline-flex mr-2 px-4 py-2.5 rounded-lg dark:bg-red-800/50 dark:text-red-200/80">
                                    Снятие
                                </span>
                                    <!--                                <span v-if="invoice.status === 'success'" class="inline-flex mr-2 px-4 py-2.5 rounded-lg dark:bg-green-800/50 dark:text-green-200/80">
                                                                        Завершен
                                                                    </span>
                                                                    <span v-if="invoice.status === 'fail'" class="inline-flex mr-2 px-4 py-2.5 rounded-lg dark:bg-red-800/50 dark:text-red-200/80">
                                                                        Отменен
                                                                    </span>
                                                                    <span v-if="invoice.status === 'pending'" class="inline-flex mr-2 px-4 py-2.5 rounded-lg dark:bg-yellow-700/50 dark:text-yellow-200/80">
                                                                        Ожидает
                                                                    </span>-->
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <fwb-pagination
                        v-model="currentPage"
                        :total-items="invoices.meta.total"
                        previous-label="Назад" next-label="Вперед"
                        @page-changed="openPage"
                        :per-page="invoices.meta.per_page"
                    ></fwb-pagination>
                </template>
            </div>
        </div>

        <div v-if="activeTab === 'transactions'">
            <div class="mx-auto space-y-2">
                <EmptyTable v-if="!transactions.data.length"/>
                <template v-else>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border-separate border-spacing-y-3 rounded">
                        <tbody>
                        <tr
                            v-for="transaction in transactions.data"
                            class="bg-white dark:bg-gray-800 rounded"
                        >
                            <th
                                scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-gray-200 rounded-l-xl  border border-r-0 dark:border-gray-700"
                            >
                                <div class="flex items-center">
                                    <div class="mr-3">
                                    <span v-if="transaction.direction === 'in'" class="inline-flex px-2.5 py-2.5 rounded-2xl dark:bg-green-800/50 dark:text-green-300">
                                        <svg class="w-5 h-5 text-gray-800 dark:text-green-200/80" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </span>
                                        <span v-if="transaction.direction === 'out'" class="inline-flex px-2.5 py-2.5 rounded-2xl dark:bg-red-800/50 dark:text-red-300">
                                        <svg class="w-5 h-5 text-gray-800 dark:text-red-200/80" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </span>
                                    </div>
                                    <div class="dark:text-gray-400">#{{ transaction.id }}</div>
                                </div>
                            </th>
                            <td class="p-3 border border-x-0 dark:border-gray-700">
                                <div class="text-nowrap dark:text-gray-400 text-center">
                                    <template v-if="transaction.direction === 'in'">+</template>
                                    <template v-if="transaction.direction === 'out'">-</template>
                                    {{ transaction.amount }} {{ transaction.currency.toUpperCase() }}
                                </div>
                            </td>
                            <td class="p-3 border border-x-0 dark:border-gray-700">
                                <div class="flex justify-center gap-2 dark:text-gray-400">
                                    <p class="font-medium">{{ transaction.type_name }}</p>
                                </div>
                            </td>
                            <td class="p-3 border border-x-0 dark:border-gray-700">
                                <div class="flex justify-center gap-2 dark:text-gray-400">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z" />
                                    </svg>
                                    <p class="font-medium inline-block align-middle">{{ transaction.created_at }}</p>
                                </div>
                            </td>
                            <td class="p-3 rounded-r-xl border border-l-0 dark:border-gray-700">
                                <div class="flex justify-end">
                                <span v-if="transaction.direction === 'in'" class="inline-flex mr-2 px-4 py-2.5 rounded-lg dark:bg-green-800/50 dark:text-green-200/80">
                                    Зачисление
                                </span>
                                    <span v-if="transaction.direction === 'out'" class="inline-flex mr-2 px-4 py-2.5 rounded-lg dark:bg-red-800/50 dark:text-red-200/80">
                                    Снятие
                                </span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <fwb-pagination
                        v-model="currentPage"
                        :total-items="transactions.meta.total"
                        previous-label="Назад" next-label="Вперед"
                        @page-changed="openPage"
                        :per-page="transactions.meta.per_page"
                    ></fwb-pagination>
                </template>
            </div>
        </div>
        <DepositModal/>
        <WithdrawalModal/>
    </div>
</template>

