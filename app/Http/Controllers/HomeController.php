<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     *
     */
    public function index(Request $request): View
    {

        $contents = '';
        if ($request->isMethod('post')) {
            $prompt = $request->input('prompt');
            $contents = $this->requestChatGpt($prompt);
        }

        // JSON文字列をPHPの連想配列に変換します
        $episodeList = json_decode($contents, true);
        var_dump($episodeList);
        var_dump('-----');
        echo $episodeList[0]['title'];


        return view('home', ['episodeList' => $episodeList]);
    }

    /**
     * ChatGPT API呼び出し
     * Laravel HTTP
     */
    function requestChatGpt(string $prompt,$replay='')
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
        if (!empty($replay)) {
            // 配列に新しい要素を一度に追加します
            $data["messages"] = array_merge($data["messages"], [
                [
                    "role" => "assistant",
                    "content" => $replay
                ],
                [
                    "role" => "user",
                    "content" => '一つのコードブロックで返してください'
                ],
            ]);
        }
        var_dump($data["messages"]);
        $response = Http::withHeaders($headers)->timeout(500)->post($url, $data);

        if ($response->json('error')) {
            // エラー
            return $response->json('error')['message'];
        }

        return $response->json('choices')[0]['message']['content'];
    }
//webエンジニアのTVドラマを作りたいです。
//下記条件をもとに6話分のタイトルと内容（30文字程度）で考えてください
//
//
//### 条件1
//下記のようなjson形式で返してください
//```
//[{"title": "1話のタイトル","summary": "1話の内容"},{ "title": "2話のタイトル", "summary": "2話の内容"}]
//```
//### 条件2
//下記の登場人物を登場させてください
//PM：鈴木
//デザイナー：枝松
//エンジニア：上柿元
//エンジニア：宗像
//
//###条件3
//タイトルは
//エピソードn：「タイトル」という形にしてください
}


