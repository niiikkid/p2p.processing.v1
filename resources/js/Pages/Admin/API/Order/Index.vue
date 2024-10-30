<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import NumberInput from "@/Components/NumberInput.vue";
import InputHelper from "@/Components/InputHelper.vue";
import TextInput from "@/Components/TextInput.vue";
import Select from "@/Components/Select.vue";
import {ref} from "vue";

const payment_gateways = usePage().props.paymentGateways;
const currencies = usePage().props.currencies;
const merchants = usePage().props.merchants;

const form = useForm({
    external_id: null,
    amount: null,
    callback_url: null,
    payment_gateway: 0,
    currency: 0,
    merchant_id: 0,
});

const error = ref(null);

const submit = () => {
    form
        .transform((data) => {
            if (data.payment_gateway === 0) {
                if (data.currency) {
                    data.currency = data.currency.toLowerCase()
                }
                delete data.payment_gateway;
            }
            if (data.currency === 0) {
                delete data.currency;
            }

            return data;
        })
        .post(route('admin.api.orders.store'), {
            preserveScroll: true,
            onSuccess: (data) => {
                form.reset();

                error.value = data.props.flash.message;
            },
        });
};

const gateway_mode = ref('payment_gateway');

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <Head title="Новая сделка" />

    <div>
        <div>
            <div class="mx-auto space-y-6">
                <div class="flex justify-between">
                    <h2 class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl">Создать сделку</h2>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Новая сделка</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Здесь вы можете, используя удобный интерфейс, создать сделку через API.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <div>
                                <InputLabel
                                    for="external_id"
                                    value="Внешний ID"
                                    :error="!!form.errors.external_id"
                                />

                                <TextInput
                                    id="external_id"
                                    v-model="form.external_id"
                                    class="mt-1 block w-full"
                                    placeholder="shop_1001"
                                    :error="!!form.errors.external_id"
                                    @input="form.clearErrors('external_id')"
                                />

                                <InputError :message="form.errors.external_id" class="mt-2" />
                                <InputHelper v-if="! form.errors.external_id" model-value="Это ID сделки во внешней системе, которая использует API для создания сделок."></InputHelper>
                            </div>
                            <div>
                                <InputLabel
                                    for="amount"
                                    value="Сумма"
                                    :error="!!form.errors.amount"
                                />

                                <NumberInput
                                    id="amount"
                                    v-model="form.amount"
                                    class="mt-1 block w-full"
                                    placeholder="1000"
                                    :error="!!form.errors.amount"
                                    @input="form.clearErrors('amount')"
                                />

                                <InputError :message="form.errors.amount" class="mt-2" />
                                <InputHelper v-if="! form.errors.amount" model-value="Является целым числом, без копеек (запятых)."></InputHelper>
                            </div>
                            <div>
                                <InputLabel
                                    for="callback_url"
                                    value="Ссылка для уведомлений"
                                    :error="!!form.errors.callback_url"
                                />

                                <TextInput
                                    id="callback_url"
                                    v-model="form.callback_url"
                                    class="mt-1 block w-full"
                                    placeholder="https://www.example.com/callback_url"
                                    :error="!!form.errors.callback_url"
                                    @input="form.clearErrors('callback_url')"
                                />

                                <InputError :message="form.errors.callback_url" class="mt-2" />
                                <InputHelper v-if="! form.errors.callback_url" model-value="Ссылка, куда будет отправлена информация о платеже при изменении его статуса."></InputHelper>
                            </div>

                            <div>
                                <div class="mb-4">
                                    <ul class="hidden text-sm border border-gray-700 font-medium text-center text-gray-500 rounded-lg shadow sm:flex dark:divide-gray-700 dark:text-gray-400">
                                        <li class="w-full focus-within:z-10">
                                            <a
                                                @click.prevent="gateway_mode = 'payment_gateway'; form.currency = 0"
                                                href="#"
                                                class="inline-block w-full p-2 border-r-0 border-gray-200 dark:border-gray-700 rounded-l-lg hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:hover:text-white dark:hover:bg-gray-700"
                                                :class="gateway_mode === 'payment_gateway' ? 'text-gray-900 bg-gray-100 border-r rounded-s-lg dark:bg-gray-700 dark:text-white' : 'bg-white dark:bg-gray-800'"
                                            >
                                                Метод
                                            </a>
                                        </li>
                                        <li class="w-full focus-within:z-10">
                                            <a @click.prevent="gateway_mode = 'currency'; form.payment_gateway = 0"
                                               href="#"
                                               class="inline-block w-full p-2 border-s-0 border-gray-200 dark:border-gray-700 rounded-e-lg hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:hover:text-white dark:hover:bg-gray-700"
                                               :class="gateway_mode === 'currency' ? 'text-gray-900 bg-gray-100 border-r rounded-r-lg dark:bg-gray-700 dark:text-white' : 'bg-white dark:bg-gray-800'"
                                            >
                                                Валюта
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div v-show="gateway_mode === 'payment_gateway'">
                                    <InputLabel
                                        for="payment_gateway"
                                        value="Метод"
                                        :error="!!form.errors.sub_payment_gateway_id"
                                        class="mb-1"
                                    />
                                    <Select
                                        id="payment_gateway"
                                        v-model="form.payment_gateway"
                                        :error="!!form.errors.payment_gateway"
                                        :items="payment_gateways"
                                        key="code"
                                        value="code"
                                        name="name"
                                        default_title="Выберите метод"
                                        @change="form.clearErrors('payment_gateway')"
                                    ></Select>

                                    <InputError :message="form.errors.payment_gateway" class="mt-2" />
                                    <InputHelper v-if="! form.errors.payment_gateway" model-value="Сделка будет создана только в рамках выбранного платежного метода."></InputHelper>
                                </div>

                                <div v-show="gateway_mode === 'currency'">
                                    <InputLabel
                                        for="currency"
                                        value="Валюта"
                                        :error="!!form.errors.currency"
                                        class="mb-1"
                                    />
                                    <Select
                                        id="currency"
                                        v-model="form.currency"
                                        :error="!!form.errors.currency"
                                        :items="currencies"
                                        key="code"
                                        value="code"
                                        name="code"
                                        default_title="Выберите валюту"
                                        @change="form.clearErrors('currency')"
                                    ></Select>

                                    <InputError :message="form.errors.currency" class="mt-2" />
                                    <InputHelper v-if="! form.errors.currency" model-value="Будет использован любой платежный метод в рамках выбранной валюты."></InputHelper>
                                </div>
                            </div>

                            <div>
                                <InputLabel
                                    for="merchant_id"
                                    value="Мерчант"
                                    :error="!!form.errors.merchant_id"
                                    class="mb-1"
                                />
                                <Select
                                    id="merchant_id"
                                    v-model="form.merchant_id"
                                    :error="!!form.errors.merchant_id"
                                    :items="merchants"
                                    key="id"
                                    value="id"
                                    name="name"
                                    default_title="Выберите мерчант"
                                    @change="form.clearErrors('merchant_id')"
                                ></Select>

                                <InputError :message="form.errors.merchant_id" class="mt-2" />
                                <InputHelper v-if="! form.errors.merchant_id" model-value="Список содержит только ваши мерчанты."></InputHelper>
                            </div>

                            <div v-if="error" class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                </svg>
                                <span class="sr-only">Ошибка</span>
                                <div>
                                    <span class="font-medium">Ошибка!</span> {{ error }}
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Сохранить</PrimaryButton>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Сохранено.</p>
                                </Transition>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
