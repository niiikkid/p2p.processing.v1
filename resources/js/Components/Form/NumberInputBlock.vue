<script setup>
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NumberInput from "@/Components/NumberInput.vue";
import InputBlock from "@/Components/Form/InputBlock.vue";

const props = defineProps({
    form: {
        type: Object,
    },
    field: {
        type: String,
    },
    placeholder: {
        type: String,
        default: null
    },
    label: {
        type: String
    }
});

const model = defineModel({
    required: true,
});
</script>

<template>
    <div>
        <InputBlock
            :form="form"
            :field="field"
            :label="label"
        >
            <NumberInput
                :id="field"
                v-model="model"
                class="block w-full"
                :placeholder="placeholder"
                :error="!!form.errors[field]"
                @input="form.clearErrors(field)"
            />
        </InputBlock>
    </div>
</template>

<style scoped>

</style>
