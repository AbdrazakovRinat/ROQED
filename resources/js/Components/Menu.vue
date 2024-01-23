<template>
    <div class="flex justify-center gap-4 my-4">
        <Link
            :href="route('index')"
            class="px-4 py-2 text-black border rounded hover:bg-gray-600 transition duration-300"
            :class="{'bg-green-500 text-white': route().current() == 'index'}"
        >
            Страница со списком
        </Link>
        <Link
            :href="route('addFile')"
            class="px-4 py-2 text-black border rounded hover:bg-gray-600 transition duration-300"
            :class="{'bg-green-500 text-white': route().current() == 'addFile'}"
        >
            Страница создания
        </Link>
        <Link
            :href="route('editFile')"
            :class="{'block bg-green-500 text-white': route().current() == 'editFile', 'hidden': route().current() != 'editFile'}"
            class="px-4 py-2 text-black border rounded hover:bg-gray-600 transition duration-300"
        >
            Страница редактирования
        </Link>
    </div>
</template>




<script setup>

import {Link} from "@inertiajs/vue3";
</script>


<style scoped>

</style>
