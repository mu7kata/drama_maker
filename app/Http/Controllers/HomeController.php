<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{

    /**
     * インデックスページを表示する。
     * ポストリクエストがあった場合、ChatGPTへのリクエスト結果をビューに渡す
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $episodeList = $request->session()->get('episodeList', []);

        // ホームビューをエピソードリストと共に返す
        return view('home', ['episodeList' => $episodeList]);
    }

    /**
     * @param Request $request
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     */
    public function submitForm(Request $request): \Illuminate\Foundation\Application|Redirector|RedirectResponse|Application
    {
        if ($request->isMethod('post')) {
            $this->processPostRequest($request);
        }

        return redirect('/home');
    }

    /**
     * ポストリクエストを処理し、データをセッションに保存する
     *
     * @param Request $request
     */
    protected function processPostRequest(Request $request)
    {
        // リクエストからデータを取得
        $data = $this->getRequestData($request);
        // エピソードリストを作成
        $episodeList = $this->createEpisodeList($data);
        // データをセッションに保存
        $this->storeDataInSession($request, $data + ['episodeList' => $episodeList]);
    }

    /**
     * リクエストからデータを取得する
     *
     * @param Request $request
     * @return array
     */
    protected function getRequestData(Request $request): array
    {
        $theme = $request->input('theme');
        $positions = $request->input('positions');
        $names = $request->input('names');

        $castList = $this->getCastList($positions, $names);

        return compact('theme', 'castList');
    }

    /**
     * ポジションと名前からキャストリストを生成する
     *
     * @param array $positions
     * @param array $names
     * @return array
     */
    protected function getCastList(array $positions, array $names): array
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
     * エピソードリストを作成する
     *
     * @param array $data
     * @return array
     */
    protected function createEpisodeList(array $data): array
    {
        $prompt = $this->getPrompt($data['theme'], $data['castList']);
        // ChatGPTへのリクエストを行う
        $response = $this->requestChatGpt($prompt);
        // レスポンスからコンテンツを抽出
        $contents = $this->extractContentsFromResponse($response);
        // コンテンツが存在する場合、JSON文字列をPHPの連想配列に変換
        return empty($contents) ? [] : json_decode($contents, true);
    }

    /**
     * データをセッションに保存する
     *
     * @param Request $request
     * @param array $data
     */
    protected function storeDataInSession(Request $request, array $data): void
    {
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }
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
        // レスポンスがJSON形式の場合、そのまま返す
        if ($this->isJson($response)) {
            return $response;
        }

        // レスポンスが特定のパターンを持つ場合、それを抽出
        preg_match('/```json(.*)```/s', $response, $matches);
        if (!isset($matches[1])) {
            preg_match('/```(.*)```/s', $response, $matches);
        }

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


    /**
     * ChatGPT API呼び出し
     * Laravel HTTP
     */
    function requestChatGpt(string $prompt, $replay = '')
    {
        // ChatGPT APIのエンドポイントURL
        $url = "https://api.openai.com/v1/chat/completions";

        // APIキー
        $api_key = env('OPENAI_API_KEY');

        // ヘッダー
        $headers = array(
            "Content-Type" => "application/json",
            "Authorization" => "Bearer $api_key"
        );

        // パラメータ
        $data = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "日本語で対応してください"
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ],
                [
                    "role" => "assistant",
                    "content" => $replay
                ]
            ]
        ];

        //エラー処理？
//        if (!empty($replay)) {
//            // 配列に新しい要素を一度に追加します
//            $data["messages"] = array_merge($data["messages"], [
//                [
//                    "role" => "assistant",
//                    "content" => $replay
//                ],
//                [
//                    "role" => "user",
//                    "content" => '命令に従ってjson形式で返してください'
//                ],
//            ]);
//        }
        $response = Http::withHeaders($headers)->timeout(500)->post($url, $data);

        if ($response->json('error')) {
            // エラー
            return $response->json('error')['message'];
        }

        return $response->json('choices')[0]['message']['content'];
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

        $prompt = "${theme}を作りたいです。
        下記条件をもとに6話分のタイトルと内容を下記のようなjson形式で返してください
        ```
        [{'title': '1話のタイトル','summary': '1話の内容'},{ 'title': '2話のタイトル', 'summary': '2話の内容'}]
        ```
        ### 条件1
        下記の登場人物を登場させてください
        ${castString}

        ###条件2
        タイトルは
        エピソードn：「タイトル」という形にしてください
        ### 条件3
        各話の内容は150文字〜200文字で生成してください
        ### 条件4
        ドラマの全体的なストーリーを考慮しつつ、シュールな展開になるように構成してください";

        return $prompt;
    }
}


