<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{

    /**
     * @param string $prompt
     * @return mixed
     */
    function requestImage(string $prompt): mixed
    {
        Log::info('requestImage:start');
        $url = "https://api.openai.com/v1/images/generations";
        // ChatGPT APIのエンドポイントURL

        // APIキー
        $api_key = env('OPENAI_API_KEY');

        // ヘッダー
        $headers = array(
            "Content-Type" => "application/json",
            "Authorization" => "Bearer $api_key"
        );

        // パラメータ
        $data = [
            "prompt" => $prompt,
            "n" => 1,
            "size" => "256x256"
        ];

        $response = Http::withHeaders($headers)->timeout(500)->post($url, $data);

        if ($response->json('error')) {
            // エラー
            Log::error('requestImage:error' . $response->json('error')['message']);
            return $response->json('error')['message'];
        }
        return $response->json('data')[0]['url'];
    }


    /**
     * ChatGPT API呼び出し
     * Laravel HTTP
     */
    function requestChatGpt(string $prompt)
    {
        Log::info('requestChatGpt:start');
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
                ]
            ]
        ];

        $response = Http::withHeaders($headers)->timeout(500)->post($url, $data);

        if ($response->json('error')) {
            // エラー
            return $response->json('error')['message'];
        }

        return $response->json('choices')[0]['message']['content'];
    }


}
