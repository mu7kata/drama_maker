<template>
    <div class="container">
        <h1>AIドラマメーカー</h1>
                <div>
                    <form @submit.prevent="submitForm">
                        <div>
                            <p>テーマの例</p>
                            <p>ファンタジー要素のある刑事物語/漫画「鬼滅の刃」の別の世界線の物語/webエンジニアの成長物語</p>
                        </div>
                        <label for="theme">テーマ:</label>
                        <input type="text" id="theme" v-model="theme">

                        <div v-for="(character, index) in characters" :key="index">
                            <label :for="'position' + index">登場人物{{ index + 1 }}</label>
                            <div class="character">
                                <input type="text" :id="'position' + index" v-model="character.position" placeholder="ポジション">
                                <input type="text" :id="'name' + index" v-model="character.name" placeholder="名前">
                            </div>
                        </div>

                        <button type="submit">送信</button>
                    </form>
                </div>
    </div>
</template>

<script setup>
import {ref, reactive} from 'vue';

console.log('test');
const theme = ref('');
const characters = reactive([
    {position: '', name: ''},
    {position: '', name: ''},
    {position: '', name: ''},
    {position: '', name: ''},
]);

const submitForm = () => {
    // ここでフォームの送信処理を実装します
};
</script>

