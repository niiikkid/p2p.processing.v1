<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Head, router, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from "@/Components/TextInput.vue";
import GoBackButton from "@/Components/GoBackButton.vue";
import Select from "@/Components/Select.vue";
import {computed, onMounted, ref} from "vue";

const user = usePage().props.user;
const roles = usePage().props.roles;
const merchants = usePage().props.merchants;
const payoutGateways = usePage().props.payoutGateways;

const form = useForm({
    name: user.name,
    email: user.email,
    role_id: user.role.id,
    banned: user.banned_at ? true : false,
    personal_merchants: user.personal_merchants ?? [],
    exchange_markup_rates: user.exchange_markup_rates ?? [],
});

const selectedMerchant = ref(0);
const selectedGateway = ref(0);

const submit = () => {
    form
        .patch(route('admin.users.update', user.id), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
};

const addMerchant = (merchant) => {
    if (merchant === 0 || merchant === '0' || !merchant) {
        return;
    }

    form.clearErrors('personal_merchants');

    form.personal_merchants = form.personal_merchants.filter(m => {
        return m !== merchant;
    });
    form.personal_merchants.push(merchant);
}

const removeMerchant = (merchant) => {
    form.personal_merchants = form.personal_merchants.filter(m => {
        return m !== merchant.id;
    });
}

const selectedMerchants = computed(() => {
    return merchants.filter(m => {
        return form.personal_merchants.includes(m.id);
    })
});

const addGateway = (gateway) => {
    if (gateway === 0 || gateway === '0' || !gateway) {
        return;
    }

    form.clearErrors('exchange_markup_rates');

    form.exchange_markup_rates = form.exchange_markup_rates.filter(g => {
        return g.id !== gateway;
    });
    var g = payoutGateways.filter(g => {
        return g.id === gateway;
    })[0]

    form.exchange_markup_rates.push({
        'id': g.id,
        'markup_rate': g.markup_rate,
    });
}

const removeGateway = (gateway) => {
    form.exchange_markup_rates = form.exchange_markup_rates.filter(g => {
        return g.id !== gateway;
    });
}

const selectedGateways = computed(() => {
    return payoutGateways.filter(g => {
        return form.exchange_markup_rates.map(g => {
            return g.id;
        }).includes(g.id);
    })
});

onMounted(() => {
    form.personal_merchants.map((m) => {
        addMerchant(m.id)
    })
})

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <Head title="Редактирование пользователя" />

    <div>
        <div class="mx-auto space-y-4">
            <div class="mb-3">
                <GoBackButton
                    @click="router.visit(route('admin.users.index'))"
                ></GoBackButton>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium dark:text-gray-100 text-gray-900">Редактирование пользователя - {{ user.email }}</h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Здесь вы можете отредактировать пользователя
                        </p>
                    </header>

                    <form @submit.prevent="submit" class="mt-6 space-y-6">
                        <div>
                            <InputLabel
                                for="name"
                                value="Имя"
                                :error="!!form.errors.name"
                            />

                            <TextInput
                                id="name"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                :error="!!form.errors.name"
                                @input="form.clearErrors('name')"
                            />

                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel
                                for="email"
                                value="Почта"
                                :error="!!form.errors.email"
                            />

                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                :error="!!form.errors.email"
                                @input="form.clearErrors('email')"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div v-if="user.id !== 1">
                            <InputLabel
                                for="roles"
                                value="Роль"
                                :error="!!form.errors.role_id"
                                class="mb-1"
                            />

                            <Select
                                v-model="form.role_id"
                                :error="!!form.errors.role_id"
                                :items="roles"
                                key="id"
                                value="id"
                                name="name"
                                default_title="Выберите роль"
                                @change="form.clearErrors('role_id')"
                            ></Select>

                            <InputError class="mt-2" :message="form.errors.role_id" />
                        </div>

                        <div>
                            <label class="inline-flex items-center mb-3 mt-3 cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer" v-model="form.banned">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Заблокирован</span>
                            </label>
                        </div>

                        <div v-if="user.role.name === 'Trader' || user.role.name === 'Super Admin'">
                            <InputLabel
                                for="personal_merchants"
                                value="Мерчанты"
                                :error="!!form.errors.personal_merchants"
                                class="mb-1"
                            />

                            <div class="flex justify-between gap-3">
                                <Select
                                    v-model="selectedMerchant"
                                    :error="!!form.errors.personal_merchants"
                                    :items="merchants"
                                    key="id"
                                    value="id"
                                    name="name"
                                    default_title="Выберите мерчант"
                                    @change="form.clearErrors('personal_merchants')"
                                ></Select>
                                <button
                                    @click.prevent="addMerchant(selectedMerchant)"
                                    type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                >
                                    Добавить
                                </button>
                            </div>

                            <InputError class="mt-2" :message="form.errors.personal_merchants" />
                            <div class="flex flex-wrap gap-3 mt-3">
                                <span
                                    v-for="merchant in selectedMerchants"
                                    class="inline-flex items-center bg-indigo-100 text-indigo-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-lg dark:bg-indigo-900 dark:text-indigo-300"
                                >
                                    {{ merchant.name }}
                                    <svg @click="removeMerchant(merchant)" class="w-2.5 h-2.5 ml-1.5 cursor-pointer" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div v-if="user.role.name === 'Trader' || user.role.name === 'Super Admin'">
                            <InputLabel
                                for="exchange_markup_rates"
                                value="Персональная комиссия трейдера %"
                                :error="!!form.errors.exchange_markup_rates"
                                class="mb-1"
                            />

                            <div class="flex justify-between gap-3">
                                <Select
                                    v-model="selectedGateway"
                                    :error="!!form.errors.exchange_markup_rates"
                                    :items="payoutGateways"
                                    key="id"
                                    value="id"
                                    name="name"
                                    default_title="Выберите метод"
                                    @change="form.clearErrors('exchange_markup_rates')"
                                ></Select>

                                <button
                                    @click.prevent="addGateway(selectedGateway)"
                                    type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                >
                                    Добавить
                                </button>
                            </div>

                            <InputError class="mt-2" :message="form.errors.exchange_markup_rates" />
                            <div class="flex flex-wrap gap-3 mt-3">
                                <div class="grid md:grid-cols-2 grid-cols-1 gap-4 w-full">
                                    <div
                                        v-for="(gateway, index) in selectedGateways"
                                    >
                                        <label :for="`gateway-${gateway.id}`" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ gateway.name }}</label>
                                        <div class="flex justify-between gap-3">
                                            <input
                                                :type="`gateway-${gateway.id}`"
                                                id="small-input"
                                                v-model="form.exchange_markup_rates[index].markup_rate"
                                                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            >
                                            <button
                                                @click.prevent="removeGateway(gateway.id)"
                                                type="button"
                                                class="px-3 py-2 text-xs font-medium focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 rounded-lg dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                            >
                                                <svg class="w-3 h-3 cursor-pointer" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors[`exchange_markup_rates.${index}.markup_rate`]" />
                                    </div>
                                </div>
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
</template>
