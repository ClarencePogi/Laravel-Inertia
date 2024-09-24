<script setup>
import { Link } from '@inertiajs/vue3';

defineProps(
    {
        active: Boolean
    });
</script>

<template>
        <Link class="text-blue-500" :class="{'font-bold inderline' : active}"><slot /></Link>
</template>