<?php

Class line{

	//Line Api auth info
        private $account;

        //Line Api アクセス時の curl 設定
	private $connect_timeout = 5;
	private $timeout = 5;
	private $ua = 'Iam Line bot.';

	function __construct() {
		//API認証情報ファイルの読み込み
		require_once(__DIR__.'/config/config.php');
		//API認証情報の読み込み
                $this->account = $config['account'];
		//APIのベースURL
		define('APIURL','https://trialbot-api.line.me');
	}
	
	//LineAPIが送ってくるデータを受信
	public  function messageReceive(){
	        $json_string = file_get_contents('php://input');
	        $result = json_decode($json_string,1);
		$receiveData = $result['result'][0]['content'];
		//ユーザ情報を取得するために１度APIを叩きに行く。（負荷がかかる場合はやめたほうがいい)
		$receiveData['profile'] = $this->getprofiles($receiveData['from']);
		//メッセージとユーザ情報をロギング
		$this->receiveDataLoging($receiveData);

		return $receiveData; //array
       }

	// mid を元にユーザ情報を取得
	public  function getprofiles($mid=null){
		$url    = APIURL . '/v1/profiles?mids='.$mid;
		$resultJson = $this->getCurlContents($url,$this->postHeaders());
		$profiles   = json_decode($resultJson,1);
		return $profiles; // array
	}

	// Line Api 実行時の ヘッダーパラメータ設定（主に認証情報）
	private function postHeaders(){
		$headers = [
			'Content-type: application/json; charset=UTF-8',	
			'X-Line-ChannelID: '    . $this->account['ChannelID'],	
			'X-Line-ChannelSecret: '. $this->account['ChannelSecret'],	
			'X-Line-Trusted-User-With-ACL: '. $this->account['MID'],	
		];
		return $headers;
	}

	//text メッセージ送信時の JSON 生成 // 今は、完全にテキスト送信のみ
	private function postJsonDataCreate($toArray,$text){
		$out['to'] = $toArray;
		$out['toChannel'] = '1383378250'; //fixed value
		$out['eventType'] = '138311608800106203'; //fixed value
		$out['content']['contentType'] = 1;
		$out['content']['toType'] = 1;
		$out['content']['text'] = $text;
		return json_encode($out);
	}


	public function postMessage($toArray,$text) {
		$url    = APIURL . '/v1/events' ;
		$resultJson = $this->getCurlContents($url,$this->postHeaders(),$this->postJsonDataCreate($toArray,$text));
		//TODO: $resultJsonを元に成功失敗を判定する
		return true;
	}


	private function receiveDataLoging($receiveData){
		//受信データ・ロギング
		file_put_contents(__DIR__.'/logs/receive_log',print_r($receiveData,1),FILE_APPEND);

		//ユーザトラッキング
		$displayName = $receiveData['profile']['contacts'][0]['displayName'];
		$mid         = $receiveData['profile']['contacts'][0]['mid'];
		file_put_contents(__DIR__.'/logs/user_log'   ,$displayName."\t".$mid,FILE_APPEND);

		return true; //TODO: ロギングに成功しても失敗しても、成功になっているのでエラー処理する。
	}

	//汎用的なcurlアクセスメソッド
	private function getCurlContents($url, $header = '', $post_data=array()){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// post
		if(!empty($post_data)){
			// TRUE => sending HTTP POST
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS , $post_data);
		}

		// set default user agent
		if(empty($header)){
			$header = array('Content-Type: text/html', 'User-Agent: '.$this->ua);
		}
		// HTTP HEADER ex) array('Content-type: text/plain', 'Content-length: 100')
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		// timeout of connection
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
		// timeout of response
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}
