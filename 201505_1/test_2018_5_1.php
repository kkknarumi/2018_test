<?php
        $OAUTH_CONSUMER_KEY = "";      // APIキー
        $OAUTH_SECRET = '';   // APIシークレットキー
        $OAUTH_TOKEN = "";    // アクセストークン
        $OAUTH_TOKEN_SECRET = '';  // アクセストークンシークレット
    	
        // oauth認証で使用するパラメータ
        $OAUTH_VERSION = "1.0";
        $OAUTH_SIGNATURE_METHOD = "HMAC-SHA1";
        
        // Twitter検索をするAPIとMETHODの指定
        $TWITTER_API_URL = 'https://api.twitter.com/1.1/search/tweets.json';    // 検索API
        $REQUEST_COUNT = 100;    // 取得するツイート数
        $REQUEST_METHOD = 'GET' ;
        //検索するキーワードの設定(画像のツイートのみ、RTを除外)
        $SEARCH_KEYWORD = 'JustinBieber filter:images exclude:retweets';
        
        /***** OAuth1.0認証の署名生成 *****/
        // キー部分の作成
        $oauth_signature_key = rawurlencode($OAUTH_SECRET) . '&' . rawurlencode($OAUTH_TOKEN_SECRET) ;
        
        // パラメータの生成・編集
        $oauth_nonce = microtime();
        $oauth_timestamp = time();
        $oauth_signature_param = 'count=' . $REQUEST_COUNT .
        '&oauth_consumer_key=' . $OAUTH_CONSUMER_KEY .
        '&oauth_nonce='.rawurlencode($oauth_nonce) .
        '&oauth_signature_method='. $OAUTH_SIGNATURE_METHOD .
        '&oauth_timestamp=' . $oauth_timestamp .
        '&oauth_token=' . $OAUTH_TOKEN .
        '&oauth_version=' . $OAUTH_VERSION .
        '&q=' . rawurlencode($SEARCH_KEYWORD) .
        '&tweet_mode=extended';
        
        // データ部分の作成
        $oauth_signature_date = rawurlencode($REQUEST_METHOD) . '&' . rawurlencode($TWITTER_API_URL) . '&' . rawurlencode($oauth_signature_param);
        // 上記のデータとキーを使ってHMAC-SHA1方式のハッシュ値に変換
        $oauth_signature_hash = hash_hmac( 'sha1' , $oauth_signature_date , $oauth_signature_key , TRUE ) ;
        // base64エンコードしてOAuth1.0認証の署名作成
        $oauth_signature = base64_encode( $oauth_signature_hash );
        
        
        /***** Authorizationヘッダーの作成 *****/
        $req_oauth_header = array("Authorization: OAuth " . 'count=' . rawurlencode($REQUEST_COUNT) .
        ',oauth_consumer_key=' . rawurlencode($OAUTH_CONSUMER_KEY) .
        ',oauth_nonce='.str_replace(" ","+",$oauth_nonce) .
        ',oauth_signature_method='. rawurlencode($OAUTH_SIGNATURE_METHOD) .
        ',oauth_timestamp=' . rawurlencode($oauth_timestamp) .
        ',oauth_token=' . rawurlencode($OAUTH_TOKEN) .
        ',oauth_version=' . rawurlencode($OAUTH_VERSION) .
        ',q=' . rawurlencode($SEARCH_KEYWORD) .
        ',oauth_signature='.rawurlencode($oauth_signature));
        
        /***** リクエストURLの作成 *****/
        $TWITTER_API_URL .= '?tweet_mode=extended&q=' . rawurlencode($SEARCH_KEYWORD) . '&count=' . rawurlencode($REQUEST_COUNT);

        /***** cURLによるリクエスト実行 *****/
        // セッション初期化
        $curl = curl_init() ;
        // オプション設定
        curl_setopt( $curl , CURLOPT_URL , $TWITTER_API_URL ) ; // リクエストURL
        curl_setopt( $curl , CURLOPT_HEADER, false ) ; // ヘッダ情報の受信なし
        curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $REQUEST_METHOD ) ;        // リクエストメソッド設定
        curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ; // 証明書検証なし
        curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;  // curl_execの結果を文字列で返す
        curl_setopt( $curl , CURLOPT_HTTPHEADER , $req_oauth_header ) ; // リクエストヘッダー設定
        curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;    // タイムアウトの秒数設定
        
        // セッション実行
        $res_str = curl_exec( $curl ) ;
        
        // セッション終了
        curl_close( $curl ) ;
        
        /***** リクエスト実行結果取得 *****/
        $res_str_arr = json_decode($res_str, ture) ;    // JSONを変換
        
        
        if(isset($res_str_arr['errors']) || $res_str_arr['errors'] != ""){
            echo('取得に失敗しました');
        }else{
            /***** 検索結果表示 *****/
            //実行日時をファイル名に使用
        	$date = date("YmdHis");
        	
            for ($i=0;$i<10;$i++){
            	$tweet_key = $i;
            	$twit_result = $res_str_arr['statuses'][$tweet_key];
	            if(isset($twit_result['entities']['media'])){
	            	$key = 0;
	            	$val = $twit_result['entities']['media'][$key];

            		if(isset($val["media_url_https"])){
            			//画像の拡張子を取得
	            		$extension = pathinfo($val["media_url_https"], PATHINFO_EXTENSION);

						//作成する画像ファイル名
						$file_name = $date . "_" . $tweet_key . "_" . "." . $extension;

						//QRコード画像をファイルにしてダウンロード
						$twi_image = file_get_contents($val["media_url_https"]);
						file_put_contents('downloads/'.$file_name,$twi_image);
						
						echo('image_'.$tweet_key);
						echo("\r\n");
						
            		}else{
            			continue;
            		}
	            }else{
	            	continue;
	            }
            }
            echo("finished");
        }
?>