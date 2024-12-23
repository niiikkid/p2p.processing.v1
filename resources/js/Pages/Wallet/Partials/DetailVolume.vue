<script setup>
import {useModalStore} from "@/store/modal.js";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {useViewStore} from "@/store/view.js";
import {ref} from "vue";
import ConfirmModal from "@/Components/Modals/ConfirmModal.vue";

const viewStore = useViewStore();
const modalStore = useModalStore();

const user = usePage().props.user;
const turnovers = usePage().props.payment_detail_turnovers;
const selectedTurnover = ref(Object.keys(turnovers)[0]);

const emit = defineEmits(['setSourceType']);

const confirmRestTurnover = () => {
    modalStore.openConfirmModal({
        title: 'Вы уверены что хотите обнулить оборот по выбранной валюте?',
        confirm_button_name: 'Обнулить оборот',
        confirm: () => {
            useForm({currency: selectedTurnover.value}).post(route('admin.users.wallet.turnover.reset', user.id), {
                preserveScroll: true,
                onFinish: () => {
                    router.visit(route('admin.users.wallet.index', user.id), {
                        preserveScroll: true,
                    })
                },
            });
        }
    });
};

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
                        <div class="md:text-xl text-lg text-gray-900 dark:text-gray-200">
                            <span class="md:block hidden">Оборот по реквизитам</span>
                            <span class="md:hidden block">Оборот</span>
                        </div>
                        <div class="flex gap-3">
                            <button
                                v-if="viewStore.isAdminViewMode"
                                @click.prevent="confirmRestTurnover"
                                type="button"
                                class="px-2 py-1 text-xs font-medium text-center inline-flex items-center text-gray-900 bg-white border border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                            >
                                <svg class="w-4 h-4 md:mr-1 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
                                </svg>
                                <span class="md:block hidden">Обнулить</span>
                            </button>
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

        <ConfirmModal/>
    </div>
</template>

<style scoped>

</style>
