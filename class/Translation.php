<?php
/**
* Translation - обеспечивает многоязычную поддержку йаПосылалки
*/
class Translation
{
	private $lang;
	private $translations;
	function __construct($default_lang="ru", $lang_detect=true)
	{
		$this->translations=array();
		if($lang_detect) 
			$this->lang = $this->detectLang($default_lang);
		else
			$this->lang=$default_lang;
	}
	public function current() {
		return $this->lang;
	}
	private function detectLang($default_lang) {
		//Пока что никакого детектирования нету, но оно нужно

		if(!isset($_COOKIE['translation'])) {
			$accept_language=strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
			
			if(strpos($accept_language, "ru")!==FALSE || strpos($accept_language, "ru-ru")!==FALSE)
				$_COOKIE['translation']="ru";
			else
				$_COOKIE['translation']="en";	
		}
		
		if (isset($_COOKIE['translation'])) 
			return $_COOKIE['translation'];
		else
			return $default_lang;
	}
	private function replaceWords($text, $words) {
		foreach ($words as $key => $value) {
			$text=str_replace("%{$key}%", $value, $text);
		}
		return $text;
	}
	public function add($name, $langArray) {
		$this->translations[$name]=$langArray;
		$this->translations[$name]['PHP_SELF']=$_SERVER['PHP_SELF'];
	}
	public function remove($name) {
		unset($this->translations[$name]);
	}
	public function getWord($key) {
		$key=str_replace("%", "", $key);
		return $this->translations[$this->lang][$key];
	}
	public function Begin() {
		ob_start();
	}
	public function End() {
		$content = ob_get_contents();
	  	ob_end_clean();
	  	print $this->replaceWords($content, $this->translations[$this->lang]);
	}

}
?>