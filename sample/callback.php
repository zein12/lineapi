<?php
/*
* Line Api の Callback URL に指定する、サンプルプログラムです。
* 詳しいことは、説明しませんが、
* SSL 通信が 必須で 自己証明書ではなく正規の証明書を利用する必要があります。
*/

//line.php の クラスファイルが読み込めるように適時パス変更をしてください。
require_once(__DIR__.'/../../lineapi/line.php');

$line = new line();

//message 受信
$receiveData = $line->messageReceive();

//message返信
$toArray[] = $receiveData['from'];
$line->postMessage($toArray,"こんにちは！");


