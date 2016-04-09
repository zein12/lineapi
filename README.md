

# Line Api wrapper

## 概要
Line Api を操作するためのスクリプトです。かなり雑に書いているのであくまで参考程度に。
メッセージを送信した相手に特定のメッセージを返信するサンプルプログラムを同封しています。

## 準備

1. レポジトリをclone します。

`git clone https://github.com/iiyuda7/lineapi`

1. 事前にLine 側で登録して払いだされた APIの認証情報 を `config/config.php` に 入力します。

1. `sample/callback.php` を公開ディレクトリに置きます。（LineAPIの管理画面で登録した、Callback URLの位置になります。

```

## 参考
[LINE BOT API Trialでできる全ての事を試してみた](http://qiita.com/betchi/items/8e5417dbf20a62f2239d)
