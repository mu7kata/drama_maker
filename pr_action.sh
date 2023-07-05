#!/bin/bash

echo "--------------------------------------------------------------------------------------------"
echo "🔻 本番化用のPRを作成します";

# gh pr createの出力を変数に保存
output=$(gh pr create --base deployment/production --head main --title "Your PR title" --body "Your PR description")
echo ""

# エラーチェック
if [[ $? -ne 0 ]]; then
    echo "🚨🚨 本番化作業に失敗しました 🚨🚨"
    echo ""
    echo "↓エラー内容"
    echo "gh pr create コマンドが失敗しました:"

    echo "$output"
    exit 1
fi

# output変数からPRのURLを抽出し、そのURLの最後の部分（PR番号）を取得
pr_number=$(echo "$output" | grep -o 'https://github.com/.*/pull/[0-9]*' | awk -F '/' '{print $NF}')
echo ""
echo "--------------------------------------------------------------------------------------------"
echo "🔻 本番化対象の作業履歴(commit)です。意図しない差分が含まれていないか確認してください";
echo "";
if gh pr view "$pr_number" --json commits --jq '.commit[] | "ユーザー：\(.authors[].name) | \(.messageHeadline) - "' >/dev/null 2>&1 ; then
      echo "succeeded";
else
      echo "failed";
      echo "gh pr view コマンドが失敗しました:"
      exit 1
fi



echo "--------------------------------------------------------------------------------------------"
echo 処理を選択してください
echo 1. mergeして本番化作業を開始する 2. 処理を終了する
read selectNumber
echo ""
if [[ selectNumber -eq 1 ]]; then
echo "--------------------------------------------------------------------------------------------"
echo "「1」が選択されました、マージ処理を実行します。"

fi


if [[ selectNumber -eq 2 ]]; then
echo "--------------------------------------------------------------------------------------------"
echo "「2」が選択されました、処理を終了します"
fi