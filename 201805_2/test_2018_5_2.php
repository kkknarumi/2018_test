<?php
class Crawle_class{

	private $domain;
	private $urlList = array();
	private $regex = '@<a[^>]*?(?<!\.)href="([^"]*+)"[^>]*+>(.*?)@si';
	private $result;
	
	function execute(){
		$this->domain = "no1s.biz/";
		$url = "https://" . $this->domain;
		
		//これから解析するページを配列に入れておく
		$this->urlList[] = $url;
		
		self::crawle($url);
		return $this->result;
	}

	/**
	 * 指定したサイトのページのHTMLを取得し、
	 * 取得したHTMLから同一ドメインのURLを取得し、
	 * 再帰的にcrawleを実行する
	 */
	private function crawle($url){
		$html = file_get_contents($url);

		//HTMLを取得できなければ終了
		if(!isset($html)) return;

		//HTMLの解析を行う。
		self::scrape($url,$html);

		//次のページのURLを取得
		preg_match_all($this->regex, $html, $matches, PREG_SET_ORDER);
		
		//URLの記述が無ければ終了
		if(!count($matches)) return;

		foreach($matches as $match){
			//$match[1]にリンクに記載されていたURLが格納されている
			if(!isset($match[1])) continue;

			//同一ドメインでないものは弾く
			if(!strpos($match[1], $this->domain)) continue;
			
			//httpのものは弾く
			if(strpos($match[1], "http:") !== false) continue;

			//すでに解析したURLは弾く
			if(in_array($match[1], $this->urlList)) continue;
			
			$check_url = $match[1];
			if(substr($check_url,-1) === "/"){
				//最後にスラッシュがあるURLは除いて再度チェック
				$check_url = substr($check_url, 0, -1);
				if(in_array($check_url, $this->urlList)) continue;
			}else{
				//最後にスラッシュが無いURLは追加して再度チェック
				$check_url .= "/";
				if(in_array($check_url, $this->urlList)) continue;
			}

			//これから解析するページを配列に入れておく
			$this->urlList[] = $match[1];

			//解析を再帰的に実行
			self::crawle($match[1]);
		}
	}

	private function scrape($url,$html){
		$title = array();
		$pattern = "/<title>\n(.*?)<\/title>/";
		$pattern2 = "/<title>(.*?)<\/title>/";
		
		//titleタグ内の文字を抽出して、表示画面のURLとtitleを表示
		if (preg_match($pattern, mb_convert_encoding($html, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS'), $title)) {
//			echo($url);
//			echo("   ".$title[1]);
//			echo("\r\n");
			$this->result[$url] = $title[1];
		} elseif(preg_match($pattern2, mb_convert_encoding($html, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS'), $result)) {
//			echo($url);
//			echo("   ".$title[1]);
//			echo("\r\n");
			$this->result[$url] = $title[1];
		}
	}
}

$test = new Crawle_class();
$test2 = $test->execute();

foreach($test2 as $url => $title){
	echo($url);
	echo("   ".$title);
	echo("\r\n");
}
?>
