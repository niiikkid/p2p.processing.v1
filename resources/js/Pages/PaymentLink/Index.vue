<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PaymentLayout from "@/Layouts/PaymentLayout.vue";
import CopyText from "@/Components/CopyText.vue";
import CopyPaymentText from "@/Components/CopyPaymentText.vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

defineOptions({ layout: PaymentLayout })
</script>

<template>
    <div>
        <Head title="Платеж" />

        <div>
            <div class="flex justify-between">
                <div>
                    <div class="text-gray-900 dark:text-gray-200 text-2xl">15.500&#8381;</div>
                    <div class="text-gray-400 dark:text-gray-500">Сумма для оплаты</div>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-gray-200 text-2xl">09:35</div>
                    <div class="text-gray-400 dark:text-gray-500">Время на оплату</div>
                </div>
            </div>
            <div class="mt-3 w-full p-3 text-center border border-gray-300 dark:border-gray-600 text-sm text-gray-800 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300">
                ID: c4271bdd-dffc-4fdb-85ce-25715fb9727d
            </div>

            <div class="my-5 text-base space-y-2">
                <div class="flex justify-between items-center border border-gray-300 dark:border-gray-600 rounded-lg p-3">
                    <div class="flex text-gray-900 dark:text-gray-200">
                        <svg class="mr-2 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 14h2m3 0h5M3 7v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Z"/>
                        </svg>
                        Номер карты
                    </div>
                    <div class="text-gray-200">
                        <CopyPaymentText text="4000 5000 3000 2000"></CopyPaymentText>
                    </div>
                </div>
                <div class="flex justify-between items-center border border-gray-300 dark:border-gray-600 rounded-lg p-3">
                    <div class="flex text-gray-900 dark:text-gray-200">
                        <svg class="mr-2 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        Держатель карты
                    </div>
                    <div class="text-gray-200">
                        <CopyPaymentText text="IVAN IVANOVICH"></CopyPaymentText>
                    </div>
                </div>
                <div class="flex justify-between items-center border border-gray-300 dark:border-gray-600 rounded-lg p-3">
                    <div class="flex text-gray-900 dark:text-gray-200">
                        <svg class="mr-2 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                        </svg>
                        Сумма перевода
                    </div>
                    <div class="text-gray-200">
                        <CopyPaymentText text="15.500&#8381;"></CopyPaymentText>
                    </div>
                </div>
            </div>

            <div class="flex items-center p-3 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-yellow-500/20 dark:text-yellow-300 dark:border-yellow-800">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div>
                    Переводите точную сумму, иначе средства не поступят!
                </div>
            </div>

            <div class="mt-5">
                <button
                    type="button"
                    class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    data-modal-target="default-modal"
                    data-modal-toggle="default-modal"
                >
                    Инструкция к оплате
                </button>
            </div>
        </div>


        <!-- Main modal -->
        <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Инструкция к оплате
                        </h3>
                    </div>
                    <!-- Modal body -->
                    <div class="p-6 pt-0">
                        <ul class="w-full space-y-1 text-gray-500 list-inside dark:text-gray-400">
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <svg class="flex-shrink-0 w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                </svg>
                                <span>Зайдите в свое банковское приложение</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <svg class="flex-shrink-0 w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                </svg>
                                <span>Скопируйте номер карты для перевода <b class="text-nowrap">4000 5000 3000 2000</b></span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <svg class="flex-shrink-0 w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                </svg>
                                <span>В банковском приложении выберите перевод по карте</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <svg class="flex-shrink-0 w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                </svg>
                                <span>Сделайте перевод точной суммы <b class="text-nowrap">257₽</b></span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <svg class="flex-shrink-0 w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                </svg>
                                <span>Дождитесь зачисления средств. Не закрывайте страницу до подтверждения
                                успешной оплаты.</span>
                            </li>
                        </ul>
                        <div class="p-4 mt-5 text-sm text-gray-900 dark:text-gray-400 rounded-lg bg-red-50 dark:bg-gray-800" role="alert">
                            <span class="font-medium text-red-800 dark:text-red-400">Запрещено:</span> Оплачивать заявку несколькими переводами. В случае
                            несоблюдений рекомендаций заявка будет отменена, а средства будут утеряны
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-6 pt-0 rounded-b dark:border-gray-600">
                        <button data-modal-hide="default-modal" type="button" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
