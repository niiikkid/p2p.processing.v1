<script setup>
import {Head, router} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainTableSection from '@/Wrappers/MainTableSection.vue';
import {ref} from 'vue';
import CopyableText from '@/Components/CopyableText.vue'

const props = defineProps({
    devices: Array,
})

const form = ref({ name: '' })
const processing = ref(false)

const createToken = () => {
    processing.value = true
    router.post(route('trader.devices.store'), form.value, {
        onFinish: () => {
            processing.value = false
            form.value.name = ''
            router.reload({ only: ['devices'] })
        }
    })
}

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Устройства"/>

        <MainTableSection
            title="Устройства"
            :data="props.devices"
            :paginate="false"
        >
            <template v-slot:table-header>
                <section class="flex items-center mb-5">
                    <div class="mx-auto w-full">
                        <div class="relative bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="Название устройства"
                                    >
                                    <button
                                        :disabled="processing"
                                        @click="createToken"
                                        type="button"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 h-[42px] dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 disabled:opacity-60"
                                    >
                                        Создать токен
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>

            <template v-slot:body>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Имя</th>
                            <th scope="col" class="px-6 py-3">Токен</th>
                            <th scope="col" class="px-6 py-3">Android ID</th>
                            <th scope="col" class="px-6 py-3">Подключено</th>
                            <th scope="col" class="px-6 py-3">Создано</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="d in props.devices" :key="d.id" class="bg-white border-b last:border-none dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-3 text-gray-900 dark:text-gray-200">{{ d.name }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <CopyableText :text="d.token_full" :display="d.token" />
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <span v-if="d.android_id" class="text-green-600">{{ d.android_id }}</span>
                                <span v-else class="text-gray-400">не подключено</span>
                            </td>
                            <td class="px-6 py-3">{{ d.connected_at ?? '-' }}</td>
                            <td class="px-6 py-3">{{ d.created_at }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </template>
        </MainTableSection>
    </div>
</template>


