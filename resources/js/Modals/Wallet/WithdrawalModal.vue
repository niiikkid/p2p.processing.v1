<script setup>
import ModalFooter from "@/Components/Modals/Components/ModalFooter.vue";
import ModalBody from "@/Components/Modals/Components/ModalBody.vue";
import Modal from "@/Components/Modals/Modal.vue";
import ModalHeader from "@/Components/Modals/Components/ModalHeader.vue";

import { storeToRefs } from 'pinia'
import { useModalStore } from "@/store/modal.js";
import InputHelper from "@/Components/InputHelper.vue";
import NumberInput from "@/Components/NumberInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {router, useForm} from "@inertiajs/vue3";

const modalStore = useModalStore();
const { withdrawalModal } = storeToRefs(modalStore);

const close = () => {
    modalStore.closeModal('withdrawal')
};

const form = useForm({
    amount: null,
});

const withdraw = () => {
    form.post(route('admin.users.wallet.withdraw', withdrawalModal.value.params.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('admin.users.wallet.index', withdrawalModal.value.params.user.id));
            modalStore.closeAll()
        },
    });
}
</script>

<template>
    <Modal :show="withdrawalModal.showed" @close="close" maxWidth="md">
        <ModalHeader
            title="Вывод с баланса"
            @close="close"
        />
        <ModalBody>
            <h1 class="text-gray-900 dark:text-gray-200 text-center">Введите сумму которую хотите вывести с баланса в USDT и нажмите «Продолжить»</h1>
            <form action="#" class="mx-auto max-w-screen-xl px-6 2xl:px-0 mt-8 mb-5">
                <div class="mx-auto max-w-3xl">
                    <div>
                        <div>
                            <InputLabel
                                for="amount"
                                value="Сумма вывода в USDT"
                                :error="!!form.errors.amount"
                            />

                            <NumberInput
                                id="amount"
                                class="mt-1 block w-full"
                                v-model="form.amount"
                                required
                                autofocus
                                :error="!!form.errors.amount"
                                @input="form.clearErrors('amount')"
                            />

                            <InputError class="mt-2" :message="form.errors.amount" />
                            <InputHelper v-if="! form.errors.amount" model-value="Сначала средства выводятся с траст баланса, а потом с резерва."></InputHelper>
                        </div>
                    </div>
                </div>
            </form>
        </ModalBody>
        <ModalFooter>
            <div class="flex justify-center">
                <button
                    @click.prevent="withdraw"
                    type="button"
                    class="inline-flex items-center text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
                >
                    Снять
                </button>
            </div>
        </ModalFooter>
    </Modal>
</template>

<style scoped>

</style>
