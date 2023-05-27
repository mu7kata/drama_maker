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
        $start = strpos($contents, '$episodeList =');
        $end = strpos($contents, ';');

        if ($start !== false && $end !== false) {
            $episodeListStr = substr($contents, $start, $end - $start + 1);
            echo $episodeListStr;
        } else {
            echo "Cannot find start or end.";
        }

        return view('home', ['contents'=>$contents]);
    }

    /**
     * ChatGPT API呼び出し
     * Laravel HTTP
     */
    function requestChatGpt(string $prompt)
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
        $data = array(
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "日本語で対応してください"
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ]
        );

        $response = Http::withHeaders($headers)->timeout(500)->post($url, $data);

        if ($response->json('error')) {
            // エラー
            return $response->json('error')['message'];
        }

        return $response->json('choices')[0]['message']['content'];
    }
//webエンジニアのTVドラマを作るならどんなタイトルを6話分考えてください
//そしてそれを、phpで扱えるように、各エピソードごとの配列にして,返してください
//イメージです
//```
//$episordList = [['title' => 'タイトル1', 'summary' => '内容1'], ['title' => 'タイトル2', 'summary' => '内容2']];
//```
//
//登場人物は下記です
//PM：鈴木
//デザイナー：枝松
//エンジニア：上柿元
//エンジニア：宗像
//
//#制約条件・エピソードn：「タイトル」という形にしてください・概要：{概要本文}#の形にしてください・一つのコードブロックで返してください

}
