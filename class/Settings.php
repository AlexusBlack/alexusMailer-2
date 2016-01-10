<?php
/**
* Settings - сохранение, загрузка и удаление настроек
*/
class Settings
{
	public static function Save($data, $path="amsettings.php") {
		$noread="<?php exit;?>";
		return (bool)file_put_contents($path, $noread.$data);
	}
	public static function Remove($path="amsettings.php") {
		unlink($path);
	}
	public static function Load($path="amsettings.php") {
		$noread="<?php exit;?>";
		$data=file_get_contents($path);
		if($data===false) return false;
		$data=str_replace($noread, "", $data);
		return $data;
	}
}

?>