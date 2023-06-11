<script setup>
import {ref} from 'vue'

const message = ref('Hello World!')
console.log('test');
</script>

<template>
    <div class="container">
        <div class="header">AIドラマメーカー</div>
        <a href="/home" class="link-start">スタート</a>
    </div>
    <h1>{{ message }}</h1>
</template>
