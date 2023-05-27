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

        $response = Http::withHeaders($headers)->post($url, $data);

        if ($response->json('error')) {
            // エラー
            return $response->json('error')['message'];
        }

        return $response->json('choices')[0]['message']['content'];
    }

}
