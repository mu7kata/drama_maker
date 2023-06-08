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
            $response = $this->requestChatGpt($prompt);
            echo $response;
            if ($this->isJson($response)) {
                $contents = $response;
            } else {
                preg_match('/```json(.*)```/s', $response, $matches);
                if (!isset($matches[1])) {
                    preg_match('/```(.*)```/s', $response, $matches);
                }
                $contents = $matches[1];
            }

        echo $response;
        }
        $episodeList = [];
        if (!empty($contents)) {
            // JSON文字列をPHPの連想配列に変換します
            $episodeList = json_decode($contents, true);
        }

        return view('home', ['episodeList' => $episodeList]);
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function removeBackticks($string) {
        return str_replace('```', '', $string);
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
                    "content" => '命令に従ってjson形式で返してください'
                ],
            ]);
        }
        $response = Http::withHeaders($headers)->timeout(500)->post($url, $data);

        if ($response->json('error')) {
            // エラー
            return $response->json('error')['message'];
        }

        return $response->json('choices')[0]['message']['content'];
    }
//webエンジニアのTVドラマを作りたいです。
//下記条件をもとに4話分のタイトルと内容を下記のようなjson形式で返してください
//```
//[{"title": "1話のタイトル","summary": "1話の内容"},{ "title": "2話のタイトル", "summary": "2話の内容"}]
//```
//
//### 条件1
//下記の登場人物を登場させてください
//PM：鈴木
//デザイナー：枝松
//エンジニア：上柿元
//エンジニア：宗像
//
//###条件2
//タイトルは
//エピソードn：「タイトル」という形にしてください
//### 条件3
//各話の内容は50文字〜100文字で生成してください
//
// ### 条件4
//ドラマの全体的なストーリーを考慮して構成してください
}


