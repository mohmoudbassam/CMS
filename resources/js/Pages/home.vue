
<template>
    <div class="flex h-screen bg-gray-200 font-roboto">
        <sideBar />

        <div class="flex-1 flex flex-col overflow-hidden">
            <Header />

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container mx-auto px-6 py-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
<script setup>
import sideBar from '../layout/Sidebar.vue';
import Header from '../layout/Header.vue';
</script>

