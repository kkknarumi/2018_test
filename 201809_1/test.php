<?php
class Spread_sheet_class{
    private $uri;
    private $api_key;
    private $spread_sheet_id;
    private $sheet_id;
    private $start_cell;
    private $end_cell;
    private $result;

    function execute($start_cell,$end_cell){
        $this->uri = "https://sheets.googleapis.com/v4/spreadsheets/";
        $this->api_key = ""; //ここにAPIキーを設定
        $this->spread_sheet_id = "11BCnspCt2Mut3nhc4WMY6CYTd0zF9C3eCzsk1AEpKLM";
        $this->sheet_id = "sales";
        $this->start_cell = $start_cell;
        $this->end_cell = $end_cell;
        $url = $this->uri.$this->spread_sheet_id."/values/".$this->sheet_id."!".$this->start_cell.":".$this->end_cell."?key=".$this->api_key;

        self::get_cell_data($url);
        $this->result = mb_convert_encoding($this->result, "UTF-8");
        return $this->result;
    }

    private function get_cell_data($url){
        /***** cURLによるリクエスト実行 *****/
        // セッション初期化
        $curl = curl_init() ;
        // オプション設定
        $REQUEST_METHOD = 'GET' ;
        curl_setopt( $curl , CURLOPT_URL , $url ) ; // リクエストURL
        curl_setopt( $curl , CURLOPT_HEADER, false ) ; // ヘッダ情報の受信なし
        curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $REQUEST_METHOD ) ; // リクエストメソッド設定
        curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ; // 証明書検証なし
        curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;  // curl_execの結果を文字列で返す
        curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;    // タイムアウトの秒数設定
        // セッション実行
        $res_str = curl_exec( $curl ) ;
        // セッション終了
        curl_close( $curl ) ;
        /***** リクエスト実行結果取得 *****/
        $res_data_arr = json_decode($res_str, true) ; // JSONを変換
        if($res_data_arr["error"]){
            $this->result = "データが取得出来ませんでした。#1" . PHP_EOL;
            return;
        }
        self::set_result($res_data_arr);
    }

    private function set_result($data_arr){
        if($data_arr["values"]){
            foreach($data_arr["values"] as $val){
                $this->result .= "'".implode("','",$val)."'";
                $this->result .= "," . PHP_EOL;
            }
        }else{
            $this->result = "データが取得出来ませんでした。#2" . PHP_EOL;
        }
    }
}

$start_cell = "A1";
$end_cell = "E6";
$ss_class = new Spread_sheet_class();
$result = $ss_class->execute($start_cell,$end_cell);
echo($result);
?>