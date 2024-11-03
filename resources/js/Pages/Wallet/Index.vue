<script setup>
import {Head, router, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GoBackButton from "@/Components/GoBackButton.vue";
import DepositModal from "@/Modals/Wallet/DepositModal.vue";
import WithdrawalModal from "@/Modals/Wallet/WithdrawalModal.vue";
import TraderBalance from "@/Pages/Wallet/Partials/TraderBalance.vue";
import MerchantBalance from "@/Pages/Wallet/Partials/MerchantBalance.vue";
import {useViewStore} from "@/store/view.js";
import OperationsHistory from "@/Pages/Wallet/Partials/OperationsHistory.vue";
import {ref} from "vue";

const user = usePage().props.user;
const viewStore = useViewStore();

const sourceType = ref('trust');

const setSourceType = (type) => {
    sourceType.value = type;
}

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <Head title="Финансы"/>

    <div>
        <div v-if="viewStore.isAdminViewMode" class="mb-3">
            <GoBackButton
                @click="router.visit(route('admin.users.index'))"
            ></GoBackButton>
        </div>

        <h2 class="text-xl font-medium text-gray-900 dark:text-white sm:text-2xl mb-4">
            <template v-if="viewStore.isAdminViewMode">
                Финансы пользователя: <span class="text-blue-500">{{user.email}}</span>
            </template>
            <template v-else>
                Финансы
            </template>
        </h2>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <TraderBalance v-show="viewStore.isTraderViewMode || viewStore.isAdminViewMode" @setSourceType="setSourceType"/>
            <MerchantBalance v-show="viewStore.isMerchantViewMode || viewStore.isAdminViewMode" @setSourceType="setSourceType"/>
        </div>

        <OperationsHistory/>

        <DepositModal :sourceType="sourceType"/>
        <WithdrawalModal :sourceType="sourceType"/>
    </div>
</template>

