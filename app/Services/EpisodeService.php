<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class EpisodeService
{

    private ApiService $apiService;

    public function __construct(
        ApiService $apiService,
    )
    {
        $this->apiService = $apiService;
    }


    /**
     * ポジションと名前からキャストリストを生成する
     *
     * @param array $positions
     * @param array $names
     * @return array
     */
    public function getCastList(array $positions, array $names): array
    {
        $castList = [];
        foreach ($positions as $index => $position) {
            $name = $names[$index];
            $castList[] = [
                'position' => $position,
                'name' => $name
            ];
        }

        return $castList;
    }

    /**
     * ポストリクエストを処理し、データをセッションに保存する
     *
     * @param $requestData
     * @return array
     * @throws Exception
     */
    public function processPostRequest($requestData): array
    {
        Log::info('------processPostRequest:start------ ');
        // エピソードリストを作成
        $episodeList = $this->createEpisodeList($requestData);
        $imgPromptList = $this->getImgPromptList($requestData['theme'], $episodeList);

        $imgList = [];
        foreach ($imgPromptList as $imgPrompt) {
            $imgList[] = $this->apiService->requestImage($imgPrompt);
        }
        foreach ($episodeList as $key => $episode) {
            $episodeList[$key]['img'] = $imgList[$key];
        }

        Log::info('------processPostRequest:end------ ');
        return $episodeList;
    }

    /**
     * エピソードリストを作成する
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function createEpisodeList(array $data): array
    {
        $prompt = $this->getPrompt($data['theme'], $data['castList']);
        // ChatGPTへのリクエストを行う
        $response = $this->apiService->requestChatGpt($prompt);
        // レスポンスからコンテンツを抽出
        $contents = $this->extractContentsFromResponse($response);

        //$contentsが空だったら例外処理
        if (empty($contents)) {
            throw new Exception('No contents found in the response');
        }

        // コンテンツが存在する場合、JSON文字列をPHPの連想配列に変換
        return json_decode($contents, true);
    }

    /**
     * @param $theme
     * @param $episodeList
     * @return array
     */
    public function getImgPromptList($theme, $episodeList): array
    {
        $prompt = [];
        foreach ($episodeList as $episode) {
            $prompt[] = '「' . $theme . '」、' . '「' . $episode['title'] . '」,Still Life,hyper realistic, 8K';
        }

        return $prompt;
    }

    /**
     * @param string $theme
     * @param array $castList
     * @return string
     */
    public function getPrompt(string $theme, array $castList): string
    {

        $castString = '';

        foreach ($castList as $cast) {
            $castString .= $cast['position'] . '：' . $cast['name'] . "/\n";
        }

        $prompt = "${theme}をテーマにしたTVドラマを作りたいです。
        下記条件をもとに6話分のタイトルと内容を考えて、下記のようなjson形式で返してください
        ```
        [{'title': '1話のタイトル','summary': '1話の内容'},{ 'title': '2話のタイトル', 'summary': '2話の内容'}]
        ```
        ### 条件1
        下記の登場人物を登場させてください
        $castString

        ###条件2
        タイトルは
        エピソードn：「タイトル」という形にしてください
        ### 条件3
        各話の内容は150文字〜200文字で生成してください
        ### 条件4
        6話目が最終回となるよう、ドラマの全体的なストーリーを考慮しつつ、展開を構成してください";

        return $prompt;
    }

    /**
     * レスポンスからコンテンツを抽出する。
     * JSON形式かチェックし、そうでない場合は特定のパターンで探す
     *
     * @param string $response
     * @return string
     */
    private function extractContentsFromResponse(string $response): string
    {
        Log::info('extractContentsFromResponse:start');
        // レスポンスがJSON形式の場合、そのまま返す
        if ($this->isJson($response)) {
            return $response;
        }

        // レスポンスが特定のパターンを持つ場合、それを抽出
        preg_match('/```json(.*)```/s', $response, $matches);
        if (!isset($matches[1])) {
            preg_match('/```(.*)```/s', $response, $matches);
        }
        Log::info('extractContentsFromResponse:end');
        // マッチしたものがなければ空文字を返す
        return $matches[1] ?? '';
    }


    /**
     * @param $string
     * @return bool
     */
    function isJson($string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


}
