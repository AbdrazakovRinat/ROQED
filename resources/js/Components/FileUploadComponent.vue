<template>
    <div
        @drop.prevent="handleDrop"
        @dragover.prevent
        @dragenter.prevent="dragEnter"
        @dragleave.prevent="dragLeave"
        :class="isDragOver ? 'border-green-500 bg-green-100' : 'border-gray-300 bg-white'"
        class="border-2 border-dashed p-4 flex flex-col items-center justify-center"
    >
        <div v-if="!isFileUploaded" class="text-center">
            <div>Перетащите файлы сюда</div>
            или
            <button @click.prevent="triggerFileInput" class="underline hover:no-underline">
                нажмите здесь, чтобы выбрать файл
            </button>
        </div>
        <div v-else class="text-center">
            <div class="text-green-600">Файл успешно загружен!</div>
            <button @click.prevent="triggerFileInput" class="underline hover:no-underline">
                нажмите здесь, или перетащите файлы сюда, чтобы выбрать другой файл
            </button>
        </div>
        <input type="file" ref="fileInput" @change="handleFileUpload" class="hidden" />
    </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';
const emits = defineEmits(['file-selected']);
const isDragOver = ref(false);
const fileInput = ref(null);
const isFileUploaded = ref(null);

const handleDrop = (event) => {
    isDragOver.value = false;
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        emits('file-selected', files[0]);
    }
};

const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    const maxFileSize = 8; // Максимальный размер файла в мегабайтах
    const fileSizeInMB = file.size / 1024 / 1024; // Размер файла в мегабайтах

    if (fileSizeInMB > maxFileSize) {
        alert(`Размер файла не должен превышать ${maxFileSize} МБ.`);
        return; // Прерывание выполнения функции, если размер файла слишком большой
    }else{
        isFileUploaded.value = true;
        emits('file-selected', file);
    }
};

const dragEnter = () => {
    isDragOver.value = true;
};

const dragLeave = () => {
    isDragOver.value = false;
};

const reset = () => {
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};
defineExpose({ reset });
</script>
