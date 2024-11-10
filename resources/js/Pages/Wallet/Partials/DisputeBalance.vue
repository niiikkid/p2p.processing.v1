<script setup>
import {useModalStore} from "@/store/modal.js";
import {usePage} from "@inertiajs/vue3";
import {useViewStore} from "@/store/view.js";

const viewStore = useViewStore();
const modalStore = useModalStore();

const wallet = usePage().props.wallet;
const user = usePage().props.user;
const dispute_balance = usePage().props.dispute_balance;
const dispute_balance_rub = usePage().props.dispute_balance_rub;
const disputes_count = usePage().props.disputes_count;
</script>

<template>
    <div>
        <div class="grow sm:mt-8 lg:mt-0">
            <div class="rounded-lg border border-gray-300 bg-white shadow p-4 dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <div class="flex justify-between">
                        <div class="text-xl text-gray-900 dark:text-gray-200">Спорные сделки</div>
                        <div>
                            <svg class="w-5 h-5 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="pt-5 inline-block align-middle">
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-200">
                           {{ dispute_balance }} USDT
                        </span>
                    </div>

                    <div class="mt-2">
                        <div class="inline-flex">
                            <div class="text-base text-gray-500 dark:text-gray-400">
                                {{ dispute_balance_rub }} RUB - Споров - {{ disputes_count }}
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
