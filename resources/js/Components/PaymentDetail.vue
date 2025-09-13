<script setup>
import {computed} from "vue";
import CopyableText from '@/Components/CopyableText.vue'

const props = defineProps({
    detail: {
        type: String,
    },
    type: {
        type: String,
    },
    copyable: {
        type: Boolean,
        default: true
    },
});

const phone = computed(() => {
    if (props.type !== 'phone') {
        return null;
    }

    let x = props.detail.replace(/\D/g, '').match(/(\d{1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);

    return  !x[2] ? x[1] : '+' + x[1] + ' (' + x[2] + ') ' + x[3] + '-' + x[4] + '-' + x[5];
})

const display = computed(() => {
    if (props.type === 'card') {
        return props.detail.match(/.{1,4}/g).join(' ')
    }
    if (props.type === 'phone') {
        return phone.value
    }
    if (props.type === 'account_number') {
        return props.detail
    }
    return props.detail
})
</script>

<template>
    <template v-if="copyable">
        <CopyableText :text="detail" :display="display" />
    </template>
    <template v-else>
        <span class="text-nowrap">
            {{ display }}
        </span>
    </template>
</template>

<style scoped>

</style>
