<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import InputHelper from "@/Components/InputHelper.vue";
import InputError from "@/Components/InputError.vue";
import Select from "@/Components/Select.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { FwbFileInput } from 'flowbite-vue'
import {ref} from "vue";

const orders = usePage().props.orders;
const form = useForm({
    order: 0,
    receipt: null
});
const error = ref(null);

const submit = () => {
    form
        .post(route('admin.api.disputes.store'), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: (data) => {
                form.reset();

                error.value = data.props.flash.message;
            },
        });
}

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <Head title="Новый спор" />

    <div>
        <div>
            <div class="mx-auto space-y-6">
                <div class="flex justify-between">
                    <h2 class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl">Открыть спор</h2>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Новый спор</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Здесь вы можете, используя удобный интерфейс, создать спор через API.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <div>
                                <fwb-file-input v-model="form.receipt"/>

                                <InputError :message="form.errors.receipt" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel
                                    for="order"
                                    value="Сделка"
                                    :error="!!form.errors.order"
                                    class="mb-1"
                                />
                                <Select
                                    id="order"
                                    v-model="form.order"
                                    :error="!!form.errors.order"
                                    :items="orders"
                                    key="id"
                                    value="id"
                                    name="name"
                                    default_title="Выберите сделку"
                                    @change="form.clearErrors('order')"
                                ></Select>

                                <InputError :message="form.errors.order" class="mt-2" />
                                <InputHelper v-if="! form.errors.order" model-value="Список содержит только не оплаченные сделки без споров за последние сутки."></InputHelper>
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
