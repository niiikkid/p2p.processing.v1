<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, router, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Select from "@/Components/Select.vue";
import NumberInput from "@/Components/NumberInput.vue";
import SaveButton from "@/Components/Form/SaveButton.vue";
import SecondaryPageSection from "@/Wrappers/SecondaryPageSection.vue";
import {useViewStore} from "@/store/view.js";

const viewStore = useViewStore();
const payment_gateways = usePage().props.paymentGateways;

const form = useForm({

});
const submit = () => {
    form
        .post(route('payments.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                router.visit(route('payments.index'))
            },
        });
};

defineOptions({ layout: AuthenticatedLayout })
</script>

<template>
    <div>
        <Head title="Создание платежа" />

        <SecondaryPageSection
            :back-link="route('payments.index')"
            title="Создание платежа"
            description="Здесь вы можете вручную создать платеж для клиента."
        >
            <form @submit.prevent="submit" class="mt-6 space-y-6">

                <SaveButton
                    :disabled="form.processing"
                    :saved="form.recentlySuccessful"
                ></SaveButton>
            </form>
        </SecondaryPageSection>
    </div>
</template>
