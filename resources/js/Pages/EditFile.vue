<template>
    <Head title="Редактирование файла" />
    <div class="py-12">
        <Menu></Menu>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex flex-col">
                    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                            <div class="overflow-hidden">
                                <form @submit.prevent="submit" class="grid grid-cols-2 gap-4">
                                    <label for="filename" class="mb-2 col-span-1 text-right mr-4">Наименование:</label>
                                    <NewNameOfFileComponent v-model:newNameOfFile="form.filename" />

                                    <label for="newFile" class="mb-2 col-span-1 text-right mr-4">Новый файл:</label>
                                    <FileUploadComponent @file-selected="handleFileUploaded" class="col-span-1 mb-4" />

                                    <div v-if="form.newFile" class="col-span-1 text-right mr-4 text-red-500">Загружен новый файл</div>
                                    <span v-if="form.newFile" class="col-span-1 text-red-500">
                                        Описание
                                    </span>
                                    <div class="col-span-2 text-center w-full">
                                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                            {{ form.progress.percentage.toFixed(0) }}%
                                        </progress>
                                    </div>

                                    <div class="col-span-1 text-right mr-4">Размер:</div>
                                    <span class="col-span-1">{{ sizeInMB(form.size) }}</span>

                                    <div class="col-span-1 text-right mr-4">Расширение:</div>
                                    <span class="col-span-1">{{ form.extension }}</span>
                                    <div class="col-span-2 flex justify-center space-x-4">
                                        <button type="submit" class="mx-2 px-4 py-2 text-black border border-green-600 rounded hover:bg-green-300 transition duration-300">
                                            Сохранить изменения
                                        </button>
                                        <button type="button" @click="handleDelete(props.file.id)" class="mx-2 px-4 py-2 text-black border border-red-600 rounded hover:bg-red-300 transition duration-300">
                                            Удалить
                                        </button>
                                    </div>
                                </form>
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
import {Head, router, useForm} from '@inertiajs/vue3';
import Menu from "@/Components/Menu.vue";
import FileUploadComponent from "@/Components/FileUploadComponent.vue";
import Swal from "sweetalert2";
import NewNameOfFileComponent from "@/Components/NewNameOfFileComponent.vue";

const props = defineProps({
    file: Object
});
const sizeInMB = (sizeInBytes) => {
    return (sizeInBytes / (1024 * 1024)).toFixed(2) + ' MB';
};
const form = useForm({
    id: props.file.id,
    filename: props.file.filename,
    newFile: '',
    size: props.file.size,
    extension: props.file.fileExtension,
    progress: null
});

const submit = () => {
    Swal.fire({
        title: "Действительно хотите обновить файл?",
        text: "Файл будет обновлен в базе данных и хранилище!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Отмена",
        confirmButtonText: "Да, обновить!"
    }).then((result) => {
        if (result.isConfirmed) {
            // setTimeout(() => {
                form.post(route('editFilePost'));
                Swal.fire({
                    title: "Обновлен!",
                    text: "Файл успешно обновлен.",
                    icon: "success"
                });
            // }, 2000); // Задержка в 2 секунды для теста прогресса
        }
    });
};

const handleFileUploaded = (file) => {
    form.newFile = file;
    form.size = file.size;
    const fileParts = file.name.split('.');
    form.extension = fileParts[fileParts.length - 1];
    form.fileUploaded = true;
};

const handleDelete = (fileId) => {
    Swal.fire({
        title: "Действительно хотите удалить файл?",
        text: "Файл будет удален из базы данных!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Отмена",
        confirmButtonText: "Да, удалить!"
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Удалено!",
                text: "Файл успешно удален из базы данных.",
                icon: "success"
            });
            router.post(route('deleteFile'), { fileId: fileId } , {preserveState: true, replace: true, preserveScroll: true});
        }
    });
};


</script>

<style>
</style>
