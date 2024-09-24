<template>
    <Nav/>
    <h1 class="font-bold text-2xl text-center">Future featured</h1>
    <ul class="block ml-auto mr-auto text-center p-5">
        <li v-for="item of future_featured">
            {{ item }}
        </li>
    </ul>
</template>

<script setup>
import Nav from '../Shared/Nav.vue';

defineProps({
    future_featured: Array
})
</script>