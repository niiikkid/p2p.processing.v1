<script setup>
import {useModalStore} from "@/store/modal.js";
import {usePage} from "@inertiajs/vue3";
import {useViewStore} from "@/store/view.js";
import {ref} from "vue";

const viewStore = useViewStore();
const modalStore = useModalStore();
const wallet = usePage().props.wallet;
const reserve_balance = usePage().props.reserve_balance;
const trust_locked_for_withdrawal = usePage().props.trust_locked_for_withdrawal;
const user = usePage().props.user;
const turnovers = usePage().props.payment_detail_turnovers;

const selectedTurnover = ref(Object.keys(turnovers)[0]);

const emit = defineEmits(['setSourceType']);

const setSourceType = (type) => {
    emit('setSourceType', type);
};
</script>

<template>
    <div>
        <div class="grow lg:mt-0">
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <div class="flex justify-between">
                        <div class="md:text-xl text-lg text-gray-900 dark:text-gray-200">Оборот по реквизитам</div>
                        <div>
                            <select
                                id="volumeCurrency"
                                class="block py-0 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer"
                                v-model="selectedTurnover"
                            >
                                <option v-for="(turnover, index) in turnovers" :value="index">{{ index.toUpperCase() }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="md:pt-5 pt-1 flex items-center align-middle">
                        <span class="md:text-xl text-lg font-bold text-gray-900 dark:text-gray-200">{{ turnovers[selectedTurnover].secondary }} {{ turnovers[selectedTurnover].secondary_currency.toUpperCase() }}</span>
                    </div>
                    <div class="md:mt-2 mt-0">
                        <div class="inline-flex">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span>В базовой валюте</span>
                            </div>
                            <div class="text-sm text-gray-900 dark:text-gray-200 ml-1.5">
                                {{ turnovers[selectedTurnover].primary }} {{ turnovers[selectedTurnover].primary_currency.toUpperCase() }}
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
