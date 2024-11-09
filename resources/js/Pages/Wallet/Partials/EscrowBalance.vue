<script setup>
import {useModalStore} from "@/store/modal.js";
import {usePage} from "@inertiajs/vue3";
import {useViewStore} from "@/store/view.js";

const viewStore = useViewStore();
const modalStore = useModalStore();
const wallet = usePage().props.wallet;
const user = usePage().props.user;
const escrow_balance = usePage().props.escrow_balance;
const escrow_balance_rub = usePage().props.escrow_balance_rub;
const orders_count = usePage().props.orders_count;
</script>

<template>
    <div>
        <div class="grow sm:mt-8 lg:mt-0">
            <div class="rounded-lg border border-gray-300 bg-white shadow p-4 dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <div class="flex justify-between">
                        <div class="text-xl text-gray-900 dark:text-gray-200">Эскроу-счет (проводится сделка)</div>
                    </div>

                    <div class="pt-5 inline-block align-middle">
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-200">
                           {{ escrow_balance }} USDT
                        </span>
                    </div>

                    <div class="mt-2">
                        <div class="inline-flex">
                            <div class="text-base text-gray-500 dark:text-gray-400">
                                {{ escrow_balance_rub }} RUB - Сделок - {{ orders_count }}
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
