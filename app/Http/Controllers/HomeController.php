<?php

namespace App\Http\Controllers;

use App\Services\EpisodeService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;


class HomeController extends Controller
{
    private EpisodeService $episodeService;

    function __construct(EpisodeService $episodeService)
    {
        $this->episodeService = $episodeService;
    }

    /**
     * インデックスページを表示する。
     * ポストリクエストがあった場合、ChatGPTへのリクエスト結果をビューに渡す
     *
     * @param Request $request
     * @return View
     */
    public function home(Request $request): View
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
        $request->session()->forget('error');

        if ($request->isMethod('post')) {
            try {
                $requestData = $this->getRequestData($request);
                $episodeList = $this->episodeService->processPostRequest($requestData);
                $this->storeDataInSession($request, ['episodeList' => $episodeList]);
            } catch (Exception $e) {
                Log::error('submitForm:error ' . $e->getMessage());
                $request->session()->put('error', '生成に失敗しました。再度実行してください');
            }
        }

        return redirect('/home');
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

        $castList = $this->episodeService->getCastList($positions, $names);
        $requestData = compact('theme', 'castList');
        $this->storeDataInSession($request, $requestData);
        return $requestData;
    }

}


