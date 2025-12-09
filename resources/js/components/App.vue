<template>
    <div class="container">
        <h1>¡Hola FisQ!</h1>
        <p>Si ves esto, Vue y Laravel ya son amigos.</p>
        
        <button @click="contador++">
            Has hecho click {{ contador }} veces
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';
const contador = ref(0);
</script>

<style scoped>
.container {
    text-align: center;
    font-family: sans-serif;
    margin-top: 50px;
    color: #333;
}
button {
    background-color: #42b883;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 1.2rem;
    cursor: pointer;
}
</style>