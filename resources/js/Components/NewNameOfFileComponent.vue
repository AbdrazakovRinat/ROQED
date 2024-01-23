<template>
        <input
            type="text"
            :value="newNameOfFile"
            @input="updateValue($event.target.value)"
            placeholder="Имя файла"
            class="border border-gray-300 p-2 rounded"
            required
        />
</template>


<script setup>
const props = defineProps({
    newNameOfFile: String,
    selectedFile: Object
});

const emit = defineEmits(['update:newNameOfFile']);

const updateValue = (newValue) => {
    emit('update:newNameOfFile', newValue);
};
</script>
