<template>
    <div class="container">
        <h1><a class="text-decoration-none text-black" href="/">AI あらすじメーカー</a></h1>
        <div class="">
            <div class="mb-2">
                <p>入力した情報に基に、6話分のあらすじがAIにて自動生成されます</p>
            </div>
            <div v-if="episodeList.length" class="mb-5 border">
                <h2 class="px-5 pt-3">テーマ：{{ theme }}</h2>
                <hr>
                <div class="px-5 py-3" v-for="episode in episodeList" :key="episode.title">
                    <h3>{{ episode.title }}</h3>
                    <div style="display: flex;">
                        <div>
                            <img class="episodeImg" width="216" height="170" :src="episode.img" :alt="episode.title">
                        </div>
                        <div style="margin-left: 30px;">
                            <p style="margin: 0">{{ episode.summary }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <form class="bg-light border w-75" @submit.prevent="submitForm">
                <div class="w-75　mb-3">
                    <p>テンプレートから選ぶ （選択するとサンプルが入力欄に挿入されます）</p>
                    <div class="btn-group">
                        <button class="m-1 rounded　btn btn-sm btn-outline-secondary"
                                @click.prevent="setThema('detective')">探偵物
                        </button>
                        <button class="m-1 rounded　btn btn-sm btn-outline-secondary"
                                @click.prevent="setThema('horror')">ホラー系
                        </button>
                        <button class="m-1 rounded　btn btn-sm btn-outline-secondary" @click.prevent="setThema('work')">
                            仕事系
                        </button>
                        <button class="m-1 rounded　btn btn-sm btn-outline-secondary" @click.prevent="setThema('love')">
                            恋愛系
                        </button>
                        <button class="m-1 rounded btn btn-sm btn-outline-secondary" @click.prevent="setThema('manga')">
                            有名アニメの続編
                        </button>
                        <button class="m-1 rounded　btn btn-sm btn-outline-secondary" @click.prevent="setThema('movie')">
                            有名映画の続編
                        </button>
                    </div>
                </div>
                <hr>
                <div class="mb-3 w-75">
                    <label for="theme" class="form-label">テーマ</label>
                    <input type="text" class="form-control" id="theme" name="theme" v-model="theme">
                    <p class="text-danger">{{ errorMessage }}</p>
                </div>

                <div class="w-75" v-for="(cast, index) in castList" :key="index">
                    <label :for="'position' + index" class="form-label">登場人物{{ index + 1 }}</label>
                    <div class="row">
                        <div class="mb-1 col-6">
                            <input type="text" class="form-control" :id="'position' + index" v-model="cast.position"
                                   placeholder="人物の特徴、人柄、役職など" required>
                        </div>
                        <div class="mb-3 col-6">
                            <input type="text" class="form-control" :id="'name' + index" v-model="cast.name"
                                   placeholder="名前" required>
                        </div>
                    </div>
                </div>
                <div class="">
                    <button class="btn btn-primary" type="submit" :disabled="isLoading">あらすじの生成開始</button>
                </div>
            </form>
            <div v-if="errorMsg" class="error">
                <p style="color: orangered">{{ errorMsg }}</p>
            </div>
        </div>
        <div v-if="isLoading" class="loadingOverlay">
            <div class="loadingContent">
                <div style="text-align: center">
                    <p>あらすじ生成中... <br><span style="font-size: 16px">1~2分ほど、かかります</span></p>

                    <img class="loadingImg"
                         src="https://www.pixelimage.jp/blog/images_loading_gif/icon_loader_f_ww_01_s1.gif" alt="">
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import {ref} from 'vue';
import axios from 'axios';
import {useField} from 'vee-validate';
import {required} from '@vee-validate/rules';


//TODO:バリデーション整理する。テスト的に実装
const {value: theme, errorMessage} = useField('theme', required);
// const testModel = ref('');
const episodeList = ref([]);
const castList = ref([
    {position: '', name: ''},
    {position: '', name: ''},
    {position: '', name: ''},
    {position: '', name: ''},
]);

const isLoading = ref(false);
const errorMsg = ref('');


function setThema(genre) {
    const themeWork = [
        {position: '上司', name: '上柿元(男)'},
        {position: '新人', name: '野島(男)'},
        {position: '社長', name: '枝松(女)'},
        {position: '競合他社社員', name: '鈴木(男)'},
    ];
    const themeLove = [
        {position: '彼氏', name: 'のび太（男）'},
        {position: '彼女', name: 'しずか（女）'},
        {position: '親友', name: 'スネ夫（男）'},
        {position: '元恋人', name: 'ジャイ子（女）'},
    ];
    const themeManga = [
        {position: 'ロボット', name: '野島(男)'},
        {position: 'ロボット', name: '枝松(女)'},
        {position: '人間', name: '上柿元(男)'},
        {position: '火星人', name: '鈴木(男)'},
    ];
    const themeDetective = [
        {position: '探偵', name: '枝松(女)'},
        {position: '助手', name: '鈴木(男)'},
        {position: '容疑者', name: '野島(男)'},
        {position: '秘密の情報源', name: '上柿元(男)'},
    ];
    const themeHorror = [
        {position: '主人公', name: '鈴木(男)'},
        {position: '友人', name: '枝松(女)'},
        {position: '呪われた存在', name: '宗像（男）'},
        {position: '犠牲者', name: '野島(男)'},
    ];
    const themeMovie = [
        {position: 'ヴォルデモートの子孫', name: '宗像(男)'},
        {position: 'グリフィンドール生徒', name: '上柿元）'},
        {position: 'グリフィンドール生徒', name: '野島(男)'},
        {position: '謎の人物', name: '枝松(女)'},
    ];
    if (genre === 'work') {
        theme.value = 'IT企業戦士の奮闘物語'
        castList.value = themeWork;
    } else if (genre === 'horror') {
        theme.value = '死者の館　～悪夢の迷宮～'
        castList.value = themeHorror;
    } else if (genre === 'manga') {
        theme.value = '漫画「鉄腕アトム」の続編'
        castList.value = themeManga;
    } else if (genre === 'movie') {
        theme.value = '映画「ハリポッター」の続編'
        castList.value = themeMovie;
    } else if (genre === 'detective') {
        theme.value = '悪の組織に立ち向かう探偵物語'
        castList.value = themeDetective;
    } else if (genre === 'love') {
        theme.value = '愛の迷宮〜出会いと別れの連鎖、交錯する恋物語〜';
        castList.value = themeLove;
    }
}

async function submitForm() {
    //不正クリック対策
    if (isLoading.value === true) {
        return;
    }
    isLoading.value = true;
    const postData = {
        theme: theme.value,
        castList: castList.value,
    };
    errorMsg.value = '';
    await axios.post('/api/create-episode-list', postData)
        .then(response => {
            episodeList.value = response.data.episodeList;
        })
        .catch(error => {
            console.log(error);
            errorMsg.value = '生成に失敗しました、再度実行してください'
            // TODO:エラーが発生した場合の処理かく
        })
        .finally(() => {
            isLoading.value = false; // Set loading flag to false whether the request succeeded or failed
        });
}


</script>

