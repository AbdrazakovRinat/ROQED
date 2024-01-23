<template>
    <Head title="Список файлов" />
        <div class="py-12">
            <Menu></Menu>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-col">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                                <div class="overflow-hidden">
                                    <div class="overflow-hidden">
                                        <form>
                                            <FileUploadComponent ref="fileUploadComponent" @file-selected="handleFileUploaded" />

                                            <div v-if="form.newFile" class="flex items-center space-x-2">
                                                <span class="ml-2 font-medium">Наименование:</span>
                                                <NewNameOfFileComponent v-model:newNameOfFile="form.filename" />
                                            </div>
                                            <div v-if="form.newFile" class="flex items-center space-x-2">
                                                <span class="ml-2 font-medium">Размер:</span>
                                                <span class="mx-2">{{ form.size }}</span>
                                            </div>
                                            <div v-if="form.newFile" class="flex items-center space-x-2">
                                                <span class="ml-2 font-medium">Расширение:</span>
                                                <span class="mx-2">{{ form.extension }}</span>
                                            </div>
                                        </form>
                                            <button v-if="form.newFile" @click="uploadFile" class="mx-2 px-4 py-2 text-black border border-green-600 rounded hover:bg-green-300 transition duration-300">Сохранить на сервер</button>
                                            <button v-if="form.newFile" @click="clearSelection" class="mx-2 px-4 py-2 text-black border border-red-600 rounded hover:bg-red-300 transition duration-300">
                                                Очистить
                                            </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import NewNameOfFileComponent from "@/Components/NewNameOfFileComponent.vue";
import Menu from "@/Components/Menu.vue";
import Swal from "sweetalert2";
import FileUploadComponent from "@/Components/FileUploadComponent.vue";
const fileUploadComponent = ref(null);
const form = useForm({
    filename: '',
    newFile: '',
    size: '',
    extension: '',
    progress: null
});

const handleFileUploaded = (file) => {
    console.log('handleFileUploaded'+file);
    form.newFile = file; // Обновление нового файла в объекте формы
    form.size = file.size; // Обновление нового файла в объекте формы
    const fileParts = file.name.split('.');
    form.extension = fileParts[fileParts.length - 1];
    const fileNameWithoutExtension = file.name.split('.').slice(0, -1).join('.');
    form.filename = fileNameWithoutExtension;
};
const uploadFile = () => {
    if (form.newFile) {

        Swal.fire({
            title: "Действительно хотите загрузить файл на сервер?",
            text: "Файл будет загружен в базу данных и хранилище!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Отмена",
            confirmButtonText: "Да, загрузить!"
        }).then((result) => {
            if (result.isConfirmed) {
                form.post(route('addFilePost'));
                Swal.fire({
                    title: "Загружен!",
                    text: "Файл успешно загружен.",
                    icon: "success"
                });
                clearSelection();
            }
        });
    }
};
const clearSelection = () => {
    form.filename = '';
    form.newFile = '';
    form.size = '';
    form.extension = '';
    if (fileUploadComponent.value) {
        fileUploadComponent.value.reset();
    }
};
</script>


