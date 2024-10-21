<script setup>
import {Head, router, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { LoginWidget } from 'vue-tg'
import HeadllesTable from "@/Components/HeadlesTable/HeadllesTable.vue";
import HeadlessTableTh from "@/Components/HeadlesTable/HeadlessTableTh.vue";
import HeadlessTableTd from "@/Components/HeadlesTable/HeadlessTableTd.vue";
import HeadlessTableTr from "@/Components/HeadlesTable/HeadlessTableTr.vue";
import DateTime from "@/Components/DateTime.vue";
import {FwbButton, FwbPagination} from "flowbite-vue";
import {useModalStore} from "@/store/modal.js";
import NotificationModal from "@/Modals/NotificationModal.vue";
import ProgressNumber from "@/Components/ProgressNumber.vue";
import {computed, ref} from "vue";

const modalStore = useModalStore();
const tgBot = usePage().props.tgBot;
const notifications = usePage().props.notifications;

const form = useForm({
    message: '',
});

const submit = () => {
    form
        .post(route('admin.notifications.store'), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
};

const openPage = (page) => {
    router.visit(route(route().current()), { data: {
            page
        } })
}

const currentPage = ref(notifications?.meta?.current_page)

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <Head title="Уведомления" />

    <div>
        <h2 class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl mb-4">Уведомления</h2>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 md:grid-cols-2 mb-6">
            <div class="grow sm:mt-8 lg:mt-0">

                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div>
                        <div class="flex justify-between">
                            <div class="text-xl text-gray-900 dark:text-gray-200">Телеграм</div>
                        </div>

                        <template
                            v-if="! tgBot.user_telegram_id"
                        >
                            <div class="inline-flex py-3">
                                <div class="text-sm text-gray-900 dark:text-gray-400">
                                    Авторизуйтесь через телеграм, чтобы связать аккаунты.
                                </div>
                            </div>
                            <LoginWidget
                                :bot-username="tgBot.username"
                                :redirect-url="tgBot.redirectUrl"
                            />
                        </template>
                        <template v-else>
                            <div class="inline-flex py-3">
                                <div class="text-sm text-gray-900 dark:text-gray-400">
                                    Для получения уведомлений, и управления аккаунтом через телеграм - <a  :href="tgBot.openTelegramBot" target="_blank" class="text-blue-500">запустите телеграм бот</a>.
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="route().current('admin.*')">
            <div class="flex justify-between">
                <h2 class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl">Отправленные уведомления</h2>
                <div>
                    <fwb-button @click="modalStore.openNotificationModal({})" color="default">Новое уведомление</fwb-button>
                </div>
            </div>
            <HeadllesTable>
                <HeadlessTableTr v-for="notification in notifications.data">
                    <HeadlessTableTh>#{{ notification.id }}</HeadlessTableTh>
                    <HeadlessTableTd>
                        {{ notification.message }}
                    </HeadlessTableTd>
                    <HeadlessTableTd style="width: 150px">
                        <ProgressNumber :current="notification.delivered_count" :total="notification.recipients_count"></ProgressNumber>
                    </HeadlessTableTd>
                    <HeadlessTableTd>
                        <DateTime class="justify-center text-nowrap" :data="notification.created_at"/>
                    </HeadlessTableTd>
                </HeadlessTableTr>
            </HeadllesTable>
            <fwb-pagination
                v-model="currentPage"
                :total-items="notifications.meta.total"
                previous-label="Назад" next-label="Вперед"
                @page-changed="openPage"
                :per-page="notifications.meta.per_page"
            ></fwb-pagination>
        </div>
        <NotificationModal/>
    </div>
</template>
