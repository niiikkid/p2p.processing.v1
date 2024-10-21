<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import InputHelper from "@/Components/InputHelper.vue";

const props = defineProps({
    form: {
        type: Object,
    },
    field: {
        type: String,
    },
    label: {
        type: String
    },
    helper: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div>
        <InputLabel
            :for="field"
            :value="label"
            :error="!!form.errors[field]"
        />

        <div class="mt-1">
            <slot/>
        </div>

        <InputError :message="form.errors[field]" class="mt-2" />
        <InputHelper v-if="! form.errors[field] && helper" :model-value="helper"></InputHelper>
    </div>
</template>

<style scoped>

</style>
