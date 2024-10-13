
<template>
    <div class="text-white flex justify-center items-center h-screen">
        <div
            class="bg-red-200 h-80 w-96 border py-3 border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <form @submit.prevent="submit">
                <p class="text-center mt-2 text-2xl uppercase mb-10">LOGIN Friends</p>

                <div class="flex flex-col justify-center mx-2 space-y-5 h-full">

                    <div class="flex flex-row items-center flex-wrap justify-center">
                        <label for="email" class="w-1/4 text-start text-slate-300">email :</label>
                        <input type="text" name="email" id="email" v-model="form.email" placeholder="email or user name"
                               class="w-3/4 rounded-md py-2 bg-gray-600 focus:bg-gray-300 text-black">
                        <span v-if="errors.email" class="text-red-800 self-center" v-text="errors.email"></span>
                    </div>
                    <div class="flex flex-row items-center flex-wrap justify-center">
                        <label for="password" class="w-1/4">Password :</label>
                        <input type="text" name="password" id="password" v-model="form.password" placeholder="password"
                               class="w-3/4 rounded-md py-2 bg-gray-600 focus:bg-gray-300 text-black">
                        <span v-if="errors.password" class="text-red-900" v-text="errors.password"></span>
                    </div>
                    <div class="text-center space-x-2">
                        <button class="bg-slate-600 rounded-full py-2 px-4" type="submit">login</button>
                        <button class="bg-slate-600 rounded-full py-2 px-4">register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { reactive } from 'vue'

import { router } from '@inertiajs/vue3'

const form = reactive({
    password: null,
    email: null,
})


function submit() {
    router.post('/login', form)
}

defineProps({
    errors: Object
})

</script>
<style scoped>

</style>
