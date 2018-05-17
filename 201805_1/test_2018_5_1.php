<?php
        $OAUTH_CONSUMER_KEY = "";      // API�L�[
        $OAUTH_SECRET = '';   // API�V�[�N���b�g�L�[
        $OAUTH_TOKEN = "";    // �A�N�Z�X�g�[�N��
        $OAUTH_TOKEN_SECRET = '';  // �A�N�Z�X�g�[�N���V�[�N���b�g
    	
        // oauth�F�؂Ŏg�p����p�����[�^
        $OAUTH_VERSION = "1.0";
        $OAUTH_SIGNATURE_METHOD = "HMAC-SHA1";
        
        // Twitter����������API��METHOD�̎w��
        $TWITTER_API_URL = 'https://api.twitter.com/1.1/search/tweets.json';    // ����API
        $REQUEST_COUNT = 100;    // �擾����c�C�[�g��
        $REQUEST_METHOD = 'GET' ;
        //��������L�[���[�h�̐ݒ�(�摜�̃c�C�[�g�̂݁ART�����O)
        $SEARCH_KEYWORD = 'JustinBieber filter:images exclude:retweets';
        
        /***** OAuth1.0�F�؂̏������� *****/
        // �L�[�����̍쐬
        $oauth_signature_key = rawurlencode($OAUTH_SECRET) . '&' . rawurlencode($OAUTH_TOKEN_SECRET) ;
        
        // �p�����[�^�̐����E�ҏW
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
        
        // �f�[�^�����̍쐬
        $oauth_signature_date = rawurlencode($REQUEST_METHOD) . '&' . rawurlencode($TWITTER_API_URL) . '&' . rawurlencode($oauth_signature_param);
        // ��L�̃f�[�^�ƃL�[���g����HMAC-SHA1�����̃n�b�V���l�ɕϊ�
        $oauth_signature_hash = hash_hmac( 'sha1' , $oauth_signature_date , $oauth_signature_key , TRUE ) ;
        // base64�G���R�[�h����OAuth1.0�F�؂̏����쐬
        $oauth_signature = base64_encode( $oauth_signature_hash );
        
        
        /***** Authorization�w�b�_�[�̍쐬 *****/
        $req_oauth_header = array("Authorization: OAuth " . 'count=' . rawurlencode($REQUEST_COUNT) .
        ',oauth_consumer_key=' . rawurlencode($OAUTH_CONSUMER_KEY) .
        ',oauth_nonce='.str_replace(" ","+",$oauth_nonce) .
        ',oauth_signature_method='. rawurlencode($OAUTH_SIGNATURE_METHOD) .
        ',oauth_timestamp=' . rawurlencode($oauth_timestamp) .
        ',oauth_token=' . rawurlencode($OAUTH_TOKEN) .
        ',oauth_version=' . rawurlencode($OAUTH_VERSION) .
        ',q=' . rawurlencode($SEARCH_KEYWORD) .
        ',oauth_signature='.rawurlencode($oauth_signature));
        
        /***** ���N�G�X�gURL�̍쐬 *****/
        $TWITTER_API_URL .= '?tweet_mode=extended&q=' . rawurlencode($SEARCH_KEYWORD) . '&count=' . rawurlencode($REQUEST_COUNT);

        /***** cURL�ɂ�郊�N�G�X�g���s *****/
        // �Z�b�V����������
        $curl = curl_init() ;
        // �I�v�V�����ݒ�
        curl_setopt( $curl , CURLOPT_URL , $TWITTER_API_URL ) ; // ���N�G�X�gURL
        curl_setopt( $curl , CURLOPT_HEADER, false ) ; // �w�b�_���̎�M�Ȃ�
        curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $REQUEST_METHOD ) ;        // ���N�G�X�g���\�b�h�ݒ�
        curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ; // �ؖ������؂Ȃ�
        curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;  // curl_exec�̌��ʂ𕶎���ŕԂ�
        curl_setopt( $curl , CURLOPT_HTTPHEADER , $req_oauth_header ) ; // ���N�G�X�g�w�b�_�[�ݒ�
        curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;    // �^�C���A�E�g�̕b���ݒ�
        
        // �Z�b�V�������s
        $res_str = curl_exec( $curl ) ;
        
        // �Z�b�V�����I��
        curl_close( $curl ) ;
        
        /***** ���N�G�X�g���s���ʎ擾 *****/
        $res_str_arr = json_decode($res_str, ture) ;    // JSON��ϊ�
        
        
        if(isset($res_str_arr['errors']) || $res_str_arr['errors'] != ""){
            echo('�擾�Ɏ��s���܂���');
        }else{
            /***** �������ʕ\�� *****/
            //���s�������t�@�C�����Ɏg�p
        	$date = date("YmdHis");
        	
            for ($i=0;$i<10;$i++){
            	$tweet_key = $i;
            	$twit_result = $res_str_arr['statuses'][$tweet_key];
	            if(isset($twit_result['entities']['media'])){
	            	$key = 0;
	            	$val = $twit_result['entities']['media'][$key];

            		if(isset($val["media_url_https"])){
            			//�摜�̊g���q���擾
	            		$extension = pathinfo($val["media_url_https"], PATHINFO_EXTENSION);

						//�쐬����摜�t�@�C����
						$file_name = $date . "_" . $tweet_key . "_" . "." . $extension;

						//QR�R�[�h�摜���t�@�C���ɂ��ă_�E�����[�h
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