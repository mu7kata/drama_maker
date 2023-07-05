#!/bin/bash

echo "--------------------------------------------------------------------------------------------"
echo "🔻 本番化用のPRを作成します"
echo ""
# gh pr createの出力を変数に保存
result_create_pr=$(gh pr create --base deployment/production --head main --title "Your PR title" --body "Your PR description")
echo ""
echo "成功しました"
echo ""
echo "↓作成したPRのリンク"
echo "$result_create_pr"
if [[ -z "$result_create_pr" ]]; then
    echo "↑エラーログ"
    echo ""
    echo "gh pr create コマンドが失敗しました:"
    echo ""
    exit 1
fi
echo ""

echo "--------------------------------------------------------------------------------------------"
echo "🔻 本番化対象の作業履歴(commit)です。意図しない差分が含まれていないか確認してください"
echo ""

# output変数からPRのURLを抽出し、そのURLの最後の部分（PR番号）を取得
pr_number=$(echo "$result_create_pr" | grep -o 'https://github.com/.*/pull/[0-9]*' | awk -F '/' '{print $NF}')

result_show_commit_log=$(gh pr view "$pr_number" --json commits --jq '.commits[] | "ユーザー：\(.authors[].name) | \(.messageHeadline) - "') >/dev/null 2>&1

echo "$result_show_commit_log"
if [[ -z "$result_show_commit_log" ]]; then
    echo ""
    echo "↑エラーログ"
    echo "gh pr view コマンドが失敗しました:"
    echo ""
    exit 1
fi
echo ""

echo "--------------------------------------------------------------------------------------------"
echo "🔻 作業履歴に意図しない差分が含まれていないか確認後、次の処理を選択してください"
echo "1. mergeして本番化作業を開始する 2. 処理を終了する"
read selectNumber
echo ""

if [[ $selectNumber -eq 1 ]]; then
    echo "--------------------------------------------------------------------------------------------"
    echo "「1」が選択されました、マージ処理を実行します。"
    echo ""
    echo "$result_show_commit_log"

    result_pr_merge=$(gh pr merge "$pr_number" --merge  --auto) >/dev/null 2>&1

    echo "$result_pr_merge"
    if [[ -z "$result_pr_merge" ]]; then
        echo "↑エラーログ"
        echo ""
        echo "gh pr merge コマンドが失敗しました:"
        echo ""
        exit 1
    fi
fi

if [[ $selectNumber -eq 2 ]]; then
    echo "--------------------------------------------------------------------------------------------"
    echo "「2」が選択されました、処理を終了します"
fi
