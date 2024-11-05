<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PaymentLayout from "@/Layouts/PaymentLayout.vue";

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

            <div class="mt-3 flex items-center p-3 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-yellow-500/20 dark:text-yellow-300 dark:border-yellow-800">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div>
                    Переводите точную сумму, иначе средства не поступят!
                </div>
            </div>
        </div>
    </div>
</template>
