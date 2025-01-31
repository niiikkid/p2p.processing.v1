<script setup>
import {Head, router, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3';
import MainTableSection from "@/Wrappers/MainTableSection.vue";
import {useViewStore} from "@/store/view.js";
import {computed, onMounted, ref} from "vue";
import {Datepicker} from "flowbite-datepicker";
import Select from "@/Components/Select.vue";
import ConfirmModal from "@/Components/Modals/ConfirmModal.vue";

const viewStore = useViewStore();
const sms_logs = usePage().props.sms_logs;
const smsLogsTotalCount = usePage().props.smsLogsTotalCount;
const senderStopList = usePage().props.senderStopList;
const currentTab = ref('logs');
const users = ref(usePage().props.users);
const currentFilters = ref(usePage().props.currentFilters);
import {useModalStore} from "@/store/modal.js";

onMounted(() => {
    const startDateDatepickerElement = document.getElementById('start-date-datepicker')
    const endDateDatepickerElement = document.getElementById('end-date-datepicker')

    startDateDatepickerElement.addEventListener('changeDate', (e) => {
        currentFilters.value.startDate = e.target.value;
    });
    endDateDatepickerElement.addEventListener('changeDate', (e) => {
        currentFilters.value.endDate = e.target.value;
    });

    Datepicker.locales.ru = {
        days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
        daysShort: ["Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
        daysMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
        months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
        monthsShort: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
        today: "Сегодня",
        clear: "Очистить",
        format: "dd.mm.yyyy",
        weekStart: 1,
        monthsTitle: 'Месяцы'
    };

    new Datepicker(startDateDatepickerElement, {
        language: 'ru',
        format: 'dd/mm/yyyy',
    })
    new Datepicker(endDateDatepickerElement, {
        language: 'ru',
        format: 'dd/mm/yyyy',
    })
})

const filters = computed(() => {
    return {
        user: currentFilters.value.user,
        start_date: currentFilters.value.startDate,
        end_date: currentFilters.value.endDate,
    }
})
const modalStore = useModalStore();

const applyFilters = () => {
    router.visit(route(route().current()), {
        data: {
            filters: filters.value,
            page: 1
        },
        preserveScroll: true
    })
}

const confirmAddSenderToStopLost = (smsLog) => {

    modalStore.openConfirmModal({
        title: `Добавить отправителя ${smsLog.sender} в стоп лист?`,
        body: `Все сообщения отправителя ${smsLog.sender} будут удалены, а новые сообщения будут игнорироваться.`,
        confirm_button_name: 'Подтвердить',
        confirm: () => {
            useForm({}).post(route('admin.sender-stop-list.store', smsLog.id), {
                preserveScroll: true,
                onFinish: () => {
                    router.visit(route('admin.sms-logs.index'))
                },
            });
        }
    });
};

const deleteSenderFromStopList = (senderStopList) => {
    useForm({}).delete(route('admin.sender-stop-list.destroy', senderStopList.id), {
        preserveScroll: true,
        onFinish: () => {
            router.visit(route('admin.sms-logs.index'), {
                data: {
                    tab: currentTab.value
                },
            })
        },
    });
}

const openPage = (tab) => {
    currentTab.value = tab;

    let data = {
        tab: tab,
        page: 1
    };

    router.visit(route(route().current()), {
        preserveScroll: true,
        data: data,
    })
}

onMounted(() => {
    let urlParams = new URLSearchParams(window.location.search);
    currentTab.value = urlParams.get('tab') ?? 'logs'
})

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Сообщения" />

        <MainTableSection
            title="Сообщения"
            :data="sms_logs"
            :display-pagination="currentTab === 'logs'"
            :query-date="{filters}"
        >
            <template v-slot:table-header>
                <ul v-if="viewStore.isAdminViewMode" class="mb-3 flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                    <li class="me-2">
                        <a @click.prevent="openPage('logs')" href="#" :class="currentTab === 'logs' ? 'shadow inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-lg active' : 'border border-gray-200 dark:border-gray-700 inline-flex items-center px-4 py-2 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'" aria-current="page">
                            <svg class="w-4 h-4 sm:mr-2 mr-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z"/>
                            </svg>
                            <span class="sm:block hidden">Сообщения</span>
                        </a>
                    </li>
                    <li class="me-2">
                        <a @click.prevent="openPage('stop-list')" href="#" :class="currentTab === 'stop-list' ? 'shadow inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-lg active' : 'border border-gray-200 dark:border-gray-700 inline-flex items-center px-4 py-2 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'" aria-current="page">
                            <svg class="w-4 h-4 sm:mr-2 mr-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m6 6 12 12m3-6a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <span class="sm:block hidden">Стоп-лист</span>
                        </a>
                    </li>
                </ul>
                <section v-if="currentTab === 'logs'" class="flex items-center mb-5">
                    <div class="mx-auto w-full">
                        <div class="relative bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                            <div class="flex flex-col xl:items-center justify-between p-2 space-y-3 lg:flex-row lg:space-y-0 lg:space-x-4">
                                <div class="xl:flex items-center gap-4 xl:space-y-0 space-y-3">
                                    <div class="flex items-center w-full space-x-3 lg:w-auto">
                                        <select
                                            class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            v-model="currentFilters.user"
                                        >
                                            <option :value="null">Трейдер</option>
                                            <option v-for="user in users" :value="user.id">{{ user.email }}</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="relative lg:max-w-sm w-full">
                                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                </svg>
                                            </div>
                                            <input
                                                datepicker
                                                id="start-date-datepicker"
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Начальная дата"
                                                :value="currentFilters.startDate"
                                            >
                                        </div>
                                        <span class="hidden lg:block mx-4 text-gray-500">до</span>
                                        <div class="relative lg:max-w-sm w-full">
                                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                </svg>
                                            </div>
                                            <input
                                                datepicker
                                                id="end-date-datepicker"
                                                type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Конечная дата"
                                                :value="currentFilters.endDate"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <button
                                        type="button"
                                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 h-[38px] dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                        @click.prevent="applyFilters"
                                    >
                                        Применить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>
            <template v-slot:body>
                <template v-if="currentTab === 'logs'">
                    <div v-if="viewStore.isAdminViewMode" class="flex gap-5">
                        <div class="text-base text-gray-500 dark:text-gray-400 mb-3">
                            Всего логов:
                            <span class="font-semibold text-gray-900 dark:text-gray-200 mr-1">
                                {{ smsLogsTotalCount }}
                            </span>
                        </div>
                    </div>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Отправитель
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Сообщение
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Парсинг
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Тип
                                </th>
                                <th scope="col" class="px-6 py-3" v-if="viewStore.isAdminViewMode">
                                    Трейдер
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Создан
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="sms_log in sms_logs.data" class="bg-white border-b last:border-none dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-3 font-medium whitespace-nowrap text-gray-900 dark:text-gray-200">
                                    {{ sms_log.id }}
                                </th>
                                <td class="px-6 py-3">
                                    <div class="flex justify-between items-center gap-2">
                                        <div :class="{'text-green-500': sms_log.sender_exists}">
                                            {{ sms_log.sender }}
                                        </div>
                                        <div v-if="!sms_log.sender_exists">
                                            <button
                                                @click.prevent="confirmAddSenderToStopLost(sms_log)"
                                                class="px-0 py-0 text-red-500 hover:text-red-600 flex items-center hover:underline"
                                            >
                                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div style="min-width: 200px;">{{ sms_log.message }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <div v-if="sms_log.parsing_results">
                                        <div v-if="sms_log.parsing_results.amount" class="flex gap-1">
                                            <div class="text-gray-900 dark:text-gray-200">Сумма:</div>
                                            <div>{{sms_log.parsing_results.amount}}</div>
                                        </div>
                                        <div v-if="sms_log.parsing_results.digits" class="flex gap-1">
                                            <div class="text-gray-900 dark:text-gray-200">Карта:</div>
                                            <div>*{{sms_log.parsing_results.digits}}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    {{ sms_log.type }}
                                </td>
                                <td class="px-6 py-3" v-if="viewStore.isAdminViewMode">
                                    {{ sms_log.user.email }}
                                </td>
                                <td class="px-6 py-3 text-nowrap">
                                    {{ sms_log.created_at }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
                <template v-else-if="currentTab === 'stop-list'">
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(item, key) in senderStopList" :id="`sender-stop-list-${key}`" class="inline-flex items-center px-2 py-1 me-2 text-sm font-medium text-blue-800 bg-blue-100 rounded-lg dark:bg-blue-900 dark:text-blue-300">
                            {{ item.sender }}
                            <button @click.prevent="deleteSenderFromStopList(item)" type="button" class="inline-flex items-center p-1 text-sm text-blue-400 bg-transparent rounded-md hover:bg-blue-200 hover:text-blue-900 dark:hover:bg-blue-800 dark:hover:text-blue-300" :data-dismiss-target="`#sender-stop-list-${key}`" aria-label="Remove">
                                <svg class="w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Удалить</span>
                            </button>
                        </span>
                    </div>
                </template>
            </template>
        </MainTableSection>

        <ConfirmModal/>
    </div>
</template>
