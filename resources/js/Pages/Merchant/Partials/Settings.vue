<script setup>
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SaveButton from "@/Components/Form/SaveButton.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import CopyText from "@/Components/CopyText.vue";

const merchant = usePage().props.merchant;
const paymentGateways = usePage().props.paymentGateways;
const commissionSettings = ref(usePage().props.commissionSettings);

const formCallback = useForm({
    callback_url: merchant.callback_url,
});
const formCommission = useForm({
    commission_settings: null,
});

const commissionEditMode = ref(false);

const groupedGateways = ref(null);

const submitCallback = () => {
    formCallback.patch(route('merchants.callback.update', merchant.id), {
        preserveScroll: true,
    });
};

const submitCommission = () => {
    formCommission
        .transform((data) => {
            data.commission_settings = commissionSettings.value;

            return data;
        })
        .patch(route('merchants.commission.update', merchant.id), {
            preserveScroll: true,
        });
};


onMounted(() => {
    groupedGateways.value = Object.groupBy(paymentGateways.data, ({ currency }) => currency);
})
</script>

<template>
    <div class="grid grid-cols-5 gap-8">
        <div class="col-span-2">
            <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">Магазин</h3>

            <ul class="text-sm font-medium shadow text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                <li class="w-full px-4 py-3 border-b border-gray-200 rounded-t-lg dark:border-gray-700 flex justify-between">
                    <span class="text-gray-900 dark:text-gray-200">Название</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ merchant.name }}</span>
                </li>
                <li class="w-full px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between">
                    <span class="text-gray-900 dark:text-gray-200">Описание</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ merchant.description }}</span>
                </li>
                <li class="w-full px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between">
                    <span class="text-gray-900 dark:text-gray-200">Домен</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ merchant.domain }}</span>
                </li>
                <li class="w-full px-4 py-3 border-b border-gray-200 rounded-t-lg dark:border-gray-700 flex justify-between">
                    <span class="dark:text-gray-200">Статус</span>
                    <span>
                        <span v-if="merchant.active" class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Активен</span>
                        <span v-else class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Остановлен</span>
                    </span>
                </li>
                <li class="w-full px-4 py-3 rounded-b-lg flex justify-between">
                    <span class="text-gray-900 dark:text-gray-200">Secret Key</span>
                    <span class="text-gray-500 dark:text-gray-400">
                        <CopyText class="blur blur-xs hover:blur-none" :text="merchant.token"></CopyText>
                    </span>
                </li>
            </ul>
            <div class="my-8">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-3">Обработчик платежей</h3>
                <div class="p-6 bg-white shadow border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                    <p class="mb-5 text-sm font-medium text-gray-500 dark:text-gray-300">
                        Установите ссылку на Ваш обработчик для получения уведомлений. По ней мы будем отправлять POST запросы о статусах платежей.
                    </p>
                    <form class="space-y-4" @submit.prevent="submitCallback">
                        <div>
                            <InputLabel
                                for="callback_url"
                                value="Укажите ссылку"
                                :error="!!formCallback.errors.callback_url"
                            />

                            <TextInput
                                id="callback_url"
                                v-model="formCallback.callback_url"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="https://example.com/webhook"
                                :error="!!formCallback.errors.callback_url"
                                @input="formCallback.clearErrors('callback_url')"
                            />

                            <InputError :message="formCallback.errors.callback_url" class="mt-2" />
                        </div>

                        <SaveButton
                            :disabled="formCallback.processing"
                            :saved="formCallback.recentlySuccessful"
                        ></SaveButton>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-span-3">
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">Комиссии</h3>
                    <div class="flex items-center">
                        <span class="flex text-xs text-gray-900 dark:text-gray-200 mr-3">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-200 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z"/>
                            </svg>
                            Комиссия магазина
                        </span>
                        <span class="flex text-xs text-gray-900 dark:text-gray-200 mr-3">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-200 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            Комиссия клиента
                        </span>
                        <button v-if="commissionEditMode === false" @click.prevent="commissionEditMode = true" type="button" class="px-2 py-1 text-xs shadow font-medium text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                            Изменить
                        </button>
                        <button v-else @click.prevent="submitCommission(); commissionEditMode = false" type="button" class="px-2 py-1 text-xs shadow font-medium text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                            Сохранить
                        </button>
                    </div>
                </div>
                <div
                    class="mb-5"
                    v-for="(gateways, currency) in groupedGateways"
                >
                    <div>
                        <span class="bg-white text-xs shadow font-semibold py-1 px-3 border dark:text-gray-200 border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                            {{ currency.toUpperCase() }}
                        </span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div
                            class="bg-white shadow text-sm font-semibold py-2 px-3 border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700"
                            v-for="gateway in gateways"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-gray-900 dark:text-gray-200">{{ gateway.original_name }}</div>
                                    <div class="text-xs flex">
                                        <div class="flex items-center mr-2 text-gray-500 dark:text-gray-400">
                                            <svg class="w-3 h-3 mr-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z"/>
                                            </svg>
                                            {{commissionSettings[gateway.id]}}%
                                        </div>
                                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                                            <svg class="w-3 h-3 mr-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                            {{parseFloat(gateway.service_commission_rate - commissionSettings[gateway.id]).toFixed(1)}}%
                                        </div>
                                    </div>
                                </div>
                                <div class="text-gray-900 dark:text-gray-200 text-xl">
                                    {{gateway.service_commission_rate}}%
                                </div>
                            </div>
                            <div v-if="commissionEditMode === true" class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z"/>
                                </svg>
                                <input style="rotate: 180deg" type="range" v-model="commissionSettings[gateway.id]" min="0" :max="gateway.service_commission_rate" step="0.1" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer range-sm dark:bg-gray-700">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
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
