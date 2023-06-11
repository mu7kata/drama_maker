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
                <input type="text" id="theme" name="theme" v-model="theme">
                <p>{{ errorMessage }}</p>
                <div v-for="(cast, index) in castList" :key="index">
                    <label :for="'position' + index">登場人物{{ index + 1 }}</label>
                    <div class="character">
                        <input type="text" :id="'position' + index" v-model="cast.position" placeholder="ポジション"
                               required>
                        <input type="text" :id="'name' + index" v-model="cast.name" placeholder="名前" required>
                    </div>
                </div>

                <button type="submit" :disabled="isLoading">送信</button>
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
        <div>
            <div v-for="episode in episodeList" :key="episode.title">
                <h2>{{ episode.title }}</h2>
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
</template>

<script setup>
import {ref, reactive} from 'vue';
import axios from 'axios';
import {useField} from 'vee-validate';
import {required} from '@vee-validate/rules';


//TODO:バリデーション整理する。テスト的に実装
const {value: theme, errorMessage} = useField('theme', required);
// const testModel = ref('');
const episodeList = ref([]);
const castList = reactive({
    0: {position: '', name: ''},
    1: {position: '', name: ''},
    2: {position: '', name: ''},
    3: {position: '', name: ''},
});
const isLoading = ref(false);
const errorMsg = ref('');


async function submitForm() {
    //不正クリック対策
    if (isLoading.value === true) {
        return;
    }
    isLoading.value = true;
    const postData = {
        theme: theme.value,
        castList: castList,
    };

    await axios.post('/api/create-episode-list', postData)
        .then(response => {
            episodeList.value = response.data.episodeList;
        })
        .catch(error => {
            console.log(error);
            errorMsg.value = 'エラーが発生しました、再度実行してください'
            // TODO:エラーが発生した場合の処理かく
        })
        .finally(() => {
            isLoading.value = false; // Set loading flag to false whether the request succeeded or failed
        });
}


</script>

