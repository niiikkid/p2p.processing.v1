<script setup>
import {Head, router, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    FwbButton,
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
} from 'flowbite-vue'
import { usePage } from '@inertiajs/vue3';
import EditAction from "@/Components/Table/EditAction.vue";
import DeleteAction from "@/Components/Table/DeleteAction.vue";
import {useModalStore} from "@/store/modal.js";
import ConfirmModal from "@/Components/Modals/ConfirmModal.vue";
import MainTableSection from "@/Wrappers/MainTableSection.vue";

const sms_parsers = usePage().props.smsParsers;
const modalStore = useModalStore();

const confirmDeleteParser = (parser) => {
    modalStore.openConfirmModal({
        title: 'Вы уверены что хотите удалить этот парсер?',
        confirm_button_name: 'Удалить парсер',
        confirm: () => {
            useForm({}).delete(route('admin.sms-parsers.destroy', parser), {
                preserveScroll: true,
                onFinish: () => {
                    router.visit(route('admin.sms-parsers.index'), {
                        only: ['smsParsers'],
                    })
                },
            });
        }
    });
};

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Парсеры" />

        <MainTableSection
            title="Парсеры"
            :data="sms_parsers"
        >
            <template v-slot:button>
                <fwb-button @click="router.visit(route('admin.sms-parsers.create'))" color="default">Создать парсер</fwb-button>
            </template>
            <template v-slot:body>
                <fwb-table>
                    <fwb-table-head>
                        <fwb-table-head-cell>ID</fwb-table-head-cell>
                        <fwb-table-head-cell>Формат</fwb-table-head-cell>
                        <fwb-table-head-cell>Regex</fwb-table-head-cell>
                        <fwb-table-head-cell>
                            <span class="sr-only">Редактировать</span>
                        </fwb-table-head-cell>
                    </fwb-table-head>
                    <fwb-table-body>
                        <fwb-table-row v-for="sms_parser in sms_parsers.data">
                            <fwb-table-cell>{{ sms_parser.id }}</fwb-table-cell>
                            <fwb-table-cell>{{ sms_parser.format }}</fwb-table-cell>
                            <fwb-table-cell>{{ sms_parser.regex }}</fwb-table-cell>
                            <fwb-table-cell class="inline-flex">
                                <EditAction class="mr-2" :link="route('admin.sms-parsers.edit', sms_parser.id)"></EditAction>
                                <DeleteAction @click.prevent="confirmDeleteParser(sms_parser)"></DeleteAction>
                            </fwb-table-cell>
                        </fwb-table-row>
                    </fwb-table-body>
                </fwb-table>
            </template>
        </MainTableSection>

        <ConfirmModal/>
    </div>
</template>
