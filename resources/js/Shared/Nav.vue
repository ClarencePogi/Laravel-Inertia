<template>
    <nav class="w-full h-full grid">
        <ul class="text-2xl flex justify-items-center justify-self-end items-center gap-20 p-5 text-white">
            <li>
                <NavLink href="/" :active="$page.component === 'App'">Home</NavLink>
            </li>
            <li>
                <NavLink href="/features" :active="$page.component === 'Features'">Featured</NavLink>
            </li>
            <li>
                <NavLink href="/about" :active="$page.component === 'About'">About</NavLink>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import NavLink from './NavLink.vue';

</script>