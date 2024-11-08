<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputHelper from '@/Components/InputHelper.vue';
import {Head, router, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Select from "@/Components/Select.vue";
import NumberInput from "@/Components/NumberInput.vue";
import SaveButton from "@/Components/Form/SaveButton.vue";
import SecondaryPageSection from "@/Wrappers/SecondaryPageSection.vue";
import {useViewStore} from "@/store/view.js";
import TextInputBlock from "@/Components/Form/TextInputBlock.vue";
import NumberInputBlock from "@/Components/Form/NumberInputBlock.vue";
import {ref} from "vue";

const viewStore = useViewStore();

const payment_gateways = usePage().props.paymentGateways;
const currencies = usePage().props.currencies;
const merchants = usePage().props.merchants;

const form = useForm({
    amount: null,
    currency: 0,
    payment_gateway: 0,
    payment_detail_type: null,
    merchant_id: 0,
});
const submit = () => {
    form
        .post(route('payments.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                router.visit(route('payments.index'))
            },
        });
};

const gateway_mode = ref('payment_gateway');

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Создание платежа" />

        <SecondaryPageSection
            :back-link="route('payments.index')"
            title="Создание платежа"
            description="Здесь вы можете вручную создать платеж для клиента."
        >
            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <NumberInputBlock
                    v-model="form.amount"
                    :form="form"
                    field="amount"
                    label="Сумма платежа"
                    placeholder="0"
                />

                <div>
                    <div class="mb-4">
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
                                value="Платежный метод"
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
                            <InputHelper v-if="! form.errors.payment_gateway" model-value="Платеж будет создан только в рамках выбранного платежного метода."></InputHelper>
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
                                name="name"
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
                    </div>
                </div>

                <SaveButton
                    :disabled="form.processing"
                    :saved="form.recentlySuccessful"
                ></SaveButton>
            </form>
        </SecondaryPageSection>
    </div>
</template>
