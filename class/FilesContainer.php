<?php
/**
* FilesContainer - интегрированное хранилище файлов
*/
class FilesContainer
{
	private $files;
	function __construct()
	{
		$this->files=array();
	}
	public function add($name, $content, $base64encoded=true) {
		if($base64encoded)
			$this->files[$name]=$content;
		else
			$this->files[$name]=base64_encode($content);
	}
	public function get($name) {
		$qtime = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])? $_SERVER['HTTP_IF_MODIFIED_SINCE']:'' ;
		$if_modified_since=strtotime($qtime);
		$last_modification=strtotime(RELEASEDATE);

		$modified_gmt = substr(gmdate('r', $last_modification), 0, -5).'GMT';

		if($last_modification<=$if_modified_since)
			header("HTTP/1.1 304 Not Modified");

		header("Last-Modified: $modified_gmt");
	
		print base64_decode($this->files[$name]);
	}
}
?>