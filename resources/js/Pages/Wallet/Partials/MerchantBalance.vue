<script setup>
import {useModalStore} from "@/store/modal.js";
import {usePage} from "@inertiajs/vue3";

const modalStore = useModalStore();
const wallet = usePage().props.wallet;
const reserve_balance = usePage().props.reserve_balance;
const user = usePage().props.user;

</script>

<template>
    <div>
        <div class="grow sm:mt-8 lg:mt-0">
            <div class="rounded-lg border border-gray-300 bg-white shadow p-4 dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <div class="flex justify-between">
                        <div class="text-xl text-gray-900 dark:text-gray-200">Баланс мерчанта</div>
                        <div>
                            <button
                                @click.prevent="modalStore.openWithdrawalModal({user})"
                                type="button"
                                class="px-2 py-1 text-xs font-medium text-center inline-flex items-center text-gray-900 bg-white border border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                            >
                                <svg class="w-4 h-4 mr-1 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                                </svg>
                                Вывести
                            </button>
                        </div>
                    </div>

                    <div class="pt-5 inline-block align-middle">
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-200">
                            {{ wallet.trust_balance }} USDT
                        </span>
                    </div>

                    <div class="mt-2">
                        <div class="inline-flex">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Ожидает вывода
                            </div>
                            <div class="text-sm text-gray-900 dark:text-gray-200 ml-1.5">
                                {{ wallet.reserve_balance }} USDT
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
