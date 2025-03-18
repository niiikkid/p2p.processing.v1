<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {router, useForm, usePage} from '@inertiajs/vue3';
import InputHelper from "@/Components/InputHelper.vue";
import {ref} from 'vue';

const logo = usePage().props.logo;
const previewUrl = ref(logo ? `/images/logo.svg` : null);

const form = useForm({
    logo: null,
});

const updatePreview = (e) => {
    const file = e.target.files[0];
    if (!file) {
        previewUrl.value = logo ? `/images/logo.svg` : null;
        return;
    }
    
    if (file.type !== 'image/svg+xml') {
        form.errors.logo = 'Файл должен быть в формате SVG';
        previewUrl.value = logo ? `/images/logo.svg` : null;
        return;
    }
    
    previewUrl.value = URL.createObjectURL(file);
    form.logo = file;
    form.clearErrors('logo');
};

const submit = () => {
    form.post(route('admin.settings.update.logo'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Настройка логотипа</h2>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div class="max-w-[24rem]">
                <div>
                    <InputLabel
                        for="logo"
                        value="Загрузить логотип (SVG)"
                        :error="!!form.errors.logo"
                    />

                    <input
                        type="file"
                        id="logo"
                        accept=".svg"
                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        @change="updatePreview"
                    />

                    <InputError class="mt-2" :message="form.errors.logo" />
                    <InputHelper v-if="! form.errors.logo" model-value="Загрузите SVG-файл логотипа, который будет отображаться в навигационной панели."></InputHelper>
                </div>

                <div v-if="previewUrl" class="mt-4">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Предпросмотр:</p>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border dark:border-gray-700 flex justify-center items-center">
                        <img :src="previewUrl" alt="Logo preview" class="max-h-16 max-w-full" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing || !form.logo">Сохранить</PrimaryButton>

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
</template> 