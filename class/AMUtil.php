<?php
/**
* AMUtil - вспомогательные функциии alexusMailer
*/
class AMUtil
{
	public static function linesInFile($file_path) {
		if(!file_exists($file_path) || !is_readable($file_path))
			return "0";
		else {
			$file_data=explode("\n", file_get_contents($file_path));
			return count($file_data);
		}
	}
	public static function randomString($int=8) {
		$str='';
		$arr='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for($i=0;$i<$int;$i++) $str.=$arr{rand(0,61)};
		return $str;
	}
	//BasicAuth "login:password"
	public static function sendPostRequest($url, $post, $timeout=10, $basicAuth=false, $headers="") {
		$opts = array('http' =>
			array(
				'method'    => 'POST',
				'proxy'     => (defined('PROXY'))?('tcp://' . PROXY):null,
				'header'    => 	'Content-type: application/x-www-form-urlencoded'.
				($basicAuth !==false?PHP_EOL.'Authorization: Basic '.base64_encode($basicAuth):"").
				$headers,
				'timeout'   => $timeout,
				'content'   => $post
			)
		);
		$context  = stream_context_create($opts);
		return @file_get_contents($url, false, $context);
	}
	public static function sfile_get_contents($url, $use_include_path, $context, $offset=null, $maxlen=null) {
		
	}
}
?>