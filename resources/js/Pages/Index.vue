<template>
    <Head title="Список файлов" />
        <div class="py-12">
            <Menu></Menu>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-col">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                                <input type="text" v-model="searchQuery" @input="updateSearch" placeholder="Поиск..." class="rounded-xl">
                                <div class="overflow-hidden">
                                    <table class="min-w-full text-left text-sm font-light">
                                        <thead class="border-b font-medium">
                                            <tr>
                                                <th scope="col" class="px-6 py-4">#</th>
                                                <th scope="col" class="px-6 py-4">Название</th>
                                                <th scope="col" class="px-6 py-4">Размер</th>
                                                <th scope="col" class="px-6 py-4">Расширение</th>
<!--                                                <th scope="col" class="px-6 py-4">Пользователь</th>-->
                                                <th scope="col" class="px-6 py-4">Просмотреть/Скачать</th>
                                                <th scope="col" class="px-6 py-4">Редактировать</th>
                                                <th scope="col" class="px-6 py-4">Удалить</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="file in filteredFiles" :key="file.id" class="border-b hover:bg-red-100">
                                                <td class="whitespace-nowrap px-6 py-4 font-medium">{{ file?.id }}</td>
                                                <td class="whitespace-nowrap px-6 py-4">{{ file?.filename }}</td>
                                                <td class="whitespace-nowrap px-6 py-4">{{ sizeInMB(file?.size) }}</td>
                                                <td class="whitespace-nowrap px-6 py-4">{{ file?.fileExtension }}</td>
<!--                                                <td class="whitespace-nowrap px-6 py-4">{{ file?.user_id }}</td>-->
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    <div v-if="isImage(file.fileExtension)" class="w-24 h-24 overflow-hidden">
                                                        <a :href="`files/${file.path}`" target="_blank">
                                                            <img :src="`files/${file.path}`" alt="Изображение" class="object-cover w-full h-full border border-black hover:border-2 hover:border-red-600">
                                                        </a>
                                                    </div>
                                                    <a v-else :href="`files/${file.path}`" target="_blank" class="underline hover:no-underline">
                                                        Скачать
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap ">
                                                    <Link
                                                        :href="route('editFile',{fileId: file.id})"
                                                        class="inline-block rounded-full border-2 border-yellow-600 px-6 py-2 text-xs font-medium uppercase transition duration-150 ease-in-out hover:bg-amber-400"
                                                    >
                                                        Edit
                                                    </Link>
                                                </td>
                                                <td class="whitespace-nowrap">
                                                    <button @click="deleteFile(file?.id)"
                                                        type="button"
                                                        class="inline-block rounded-full border-2 border-red-600 px-6 py-2 text-xs font-medium uppercase transition duration-150 ease-in-out hover:bg-red-300">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center space-x-1">
                            <button v-for="page in props.files.last_page" :key="page" @click="loadPage(page)"
                                    class="px-4 py-2 border rounded-md hover:bg-blue-500 hover:text-white">
                                {{ page }}
                            </button>
                        </div>

                        <div class="pagination-info">
                            <p>Всего записей: <span class="font-bold text-red-500">{{ props.files.total }}</span></p>
                            <p>Записей на странице: <span class="font-bold text-red-500">{{ currentCountOnPage }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref,computed, onMounted } from 'vue';
import {Head, Link, router} from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import Menu from "@/Components/Menu.vue";


const props = defineProps({
    files: Object
});
const searchQuery = ref('');
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        searchQuery.value = searchParam;
        updateSearch(searchParam);
    }
});
const filteredFiles = computed(() => {
    return props.files.data.filter(file => {
        return file.filename.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});
const updateSearch = () => {
    router.get(route('index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true
    });
};
const currentCountOnPage = computed(() => {
    return props.files.data.length;
});
const sizeInMB = (sizeInBytes) => {
    return (sizeInBytes / (1024 * 1024)).toFixed(2) + ' MB';
};
const isImage = (fileExtension) => {
    return ['jpg','JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF','webp', 'pdf', 'PDF'].includes(fileExtension.toLowerCase());
};
const loadPage = (page) => {
    router.visit(route('index', { page: page }));
};
const deleteFile = (fileId) => {
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
}
</script>


