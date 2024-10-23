<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {Head, router, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Select from "@/Components/Select.vue";
import SaveButton from "@/Components/Form/SaveButton.vue";
import SecondaryPageSection from "@/Wrappers/SecondaryPageSection.vue";
import TextInput from "@/Components/TextInput.vue";
import InputHelper from "@/Components/InputHelper.vue";
import GoBackButton from "@/Components/GoBackButton.vue";

const merchant = usePage().props.merchant;

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head :title="'Мерчант - ' + merchant.name"/>

        <div class="mx-auto space-y-4">
            <div v-if="backLink" class="mb-3">
                <GoBackButton @click="router.visit(route('merchants.index'))"/>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Мерчант - {{ merchant.name }}
                        </h2>

                        <p v-if="description" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ description }}
                        </p>
                    </header>

                    <div>
                        <div class="mt-6 space-y-6">
                            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                                <li class="me-2">
                                    <a href="#" class="inline-block px-4 py-3 text-white bg-blue-600 rounded-lg active" aria-current="page">Статистика</a>
                                </li>
                                <li class="me-2">
                                    <a href="#"  class="inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white">Сделки</a>
                                </li>
                                <li class="me-2">
                                    <a href="#"  class="inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white">Настройки</a>
                                </li>
                            </ul>
                            <div>
                                <section>
                                    <div class="mx-auto text-center">
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg grid max-w-full mx-auto text-gray-900 lg:grid-cols-4 sm:grid-cols-3 dark:text-white">
                                            <div class="border-r border-gray-200 dark:border-gray-700 py-5 flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">100  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Доход за сегодня</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 126
                                                </div>
                                            </div>
                                            <div class="border-r border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">200  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Доход за вчера</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 240
                                                </div>
                                            </div>
                                            <div class="border-r border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">140  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Доход за месяц</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 500
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="mb-2 text-3xl md:text-3xl font-extrabold">50  &#x24;</div>
                                                <div class="font-light text-gray-500 dark:text-gray-400">Даход за все время</div>
                                                <div class="flex mt-1 font-light text-xs text-gray-900 dark:text-gray-400 border border-gray-200 rounded-lg p-1 px-2">
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                                                    </svg>
                                                    Платежей 1400
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
