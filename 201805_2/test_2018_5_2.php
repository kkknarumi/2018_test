<?php
class Crawle_class{

	private $domain;
	private $urlList = array();
	private $regex = '@<a[^>]*?(?<!\.)href="([^"]*+)"[^>]*+>(.*?)@si';
	private $result;
	
	function execute(){
		$this->domain = "no1s.biz/";
		$url = "https://" . $this->domain;
		
		//���ꂩ���͂���y�[�W��z��ɓ���Ă���
		$this->urlList[] = $url;
		
		self::crawle($url);
		return $this->result;
	}

	/**
	 * �w�肵���T�C�g�̃y�[�W��HTML���擾���A
	 * �擾����HTML���瓯��h���C����URL���擾���A
	 * �ċA�I��crawle�����s����
	 */
	private function crawle($url){
		$html = file_get_contents($url);

		//HTML���擾�ł��Ȃ���ΏI��
		if(!isset($html)) return;

		//HTML�̉�͂��s���B
		self::scrape($url,$html);

		//���̃y�[�W��URL���擾
		preg_match_all($this->regex, $html, $matches, PREG_SET_ORDER);
		
		//URL�̋L�q��������ΏI��
		if(!count($matches)) return;

		foreach($matches as $match){
			//$match[1]�Ƀ����N�ɋL�ڂ���Ă���URL���i�[����Ă���
			if(!isset($match[1])) continue;

			//����h���C���łȂ����̂͒e��
			if(!strpos($match[1], $this->domain)) continue;
			
			//http�̂��̂͒e��
			if(strpos($match[1], "http:") !== false) continue;

			//���łɉ�͂���URL�͒e��
			if(in_array($match[1], $this->urlList)) continue;
			
			$check_url = $match[1];
			if(substr($check_url,-1) === "/"){
				//�Ō�ɃX���b�V��������URL�͏����čēx�`�F�b�N
				$check_url = substr($check_url, 0, -1);
				if(in_array($check_url, $this->urlList)) continue;
			}else{
				//�Ō�ɃX���b�V��������URL�͒ǉ����čēx�`�F�b�N
				$check_url .= "/";
				if(in_array($check_url, $this->urlList)) continue;
			}

			//���ꂩ���͂���y�[�W��z��ɓ���Ă���
			$this->urlList[] = $match[1];

			//��͂��ċA�I�Ɏ��s
			self::crawle($match[1]);
		}
	}

	private function scrape($url,$html){
		$title = array();
		$pattern = "/<title>\n(.*?)<\/title>/";
		$pattern2 = "/<title>(.*?)<\/title>/";
		
		//title�^�O���̕����𒊏o���āA�\����ʂ�URL��title��\��
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
