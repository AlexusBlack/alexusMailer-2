<?php
/**
* Shell - класс для работы с шеллами
*/
class Shell
{
	//Параметр data передает в шелл данные письма не обернутыми в команду, 
	//для тех случаев когда шелл обрабатывает письмо сам
	public static function exec($outserver, $code, $data=null) {
		global $shellManager;
		list($url,$type,$pass)=explode("|",$outserver);
		if(strpos($pass, ":"))
			list($login, $pass)=explode(":", $pass);

		if(!$shellManager->canUse($type)) return false;

		return $shellManager->exec($type, $url, $code, $data, $pass, isset($login)?$login:null);
	}
	public static function check($outserver) {
		global $shellManager;
		global $translation;
		$testcode="echo 'test';";
		list($url,$type,$pass)=explode("|",$outserver);
		if(strpos($pass, ":"))
			list($login, $pass)=explode(":", $pass);
		//если не указан тип
		if($type=="")
			return json_encode(array(
				"status"=>"BAD",
				"server"=>$outserver,
				"error"=>$translation->getWord("shell-check-no-correct-type-definition")
			));
		//сначала проверим что не 404
		if(!ShellManager::ping($url)) 
			return json_encode(array(
				"status"=>"BAD",
				"server"=>$outserver,
				"error"=>$translation->getWord("shell-check-404-not-found")
			));
		//Известный тип шелла
		if(!$shellManager->canUse($type))
			return json_encode(array(
				"status"=>"BAD",
				"server"=>$outserver,
				"error"=>$translation->getWord("shell-check-unknown-shell-type")." \"".$type."\""
			));

		//иначе проводим тест авторизации и выполнения кода
		$result=false;
		$answer=$shellManager->exec($type, $url, $testcode, $data, $pass, isset($login)?$login:null);
		if($answer=='test')
			$result=true;
		if($result)
			return json_encode(array(
				"status"=>"GOOD",
				"server"=>$outserver
			));
		else
			return json_encode(array(
				"status"=>"BAD",
				"server"=>$outserver,
				"error"=>$translation->getWord("shell-sheck-test-command-execution-failed")
			));
	}
}
/**
* ShellManager - менеджер шеллов
*/
class ShellManager
{
	private $shells;
	private $types;
	private $timeout;
	function __construct($timeout=10)
	{
		$this->timeout=$timeout;
		$this->shells=array();
		$this->types=array();
	}
	public function loadShells() {
		global $shellManager;
		if(!is_readable("shell")) return;

		$handle = opendir('shell');
		while (false !== ($entry = readdir($handle))) {
	        if(strpos($entry, ".shell.php")!==false) {
	        	require_once 'shell/'.$entry;
	        }
	    }
	    closedir($handle);
	}
	public function add($name, $object) {
		$this->shells[$name]=$object;
		$this->types=array_keys($this->shells);
	} 
	public function remove($name) {
		unset($this->shells[$name]);
	}
	public function canUse($type) {
		return in_array($type, $this->types);
	}
	public function exec($type, $url, $code, $data, $password="", $login="") {
		return $this->shells[$type]->exec($url, $code, $data, $password, $login);
	} 
	public static function ping($url) {
		$headers = get_headers($url, 1);
		return $headers[0]!="HTTP/1.1 404 Not Found";
	}
}
/**
* Shell - базовый класс шелла
*/
class BasicShell
{
	public function exec($url, $code, $data, $password, $login) {}
}
?>