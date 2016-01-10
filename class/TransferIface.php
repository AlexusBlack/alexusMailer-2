<?php
/**
* TransferIface - класс отвечающий за загрузку и выгрузку файлов
*/
class TransferIface
{
	public static function uploadFile($file, $fieldname="elist", $query="upload_universal") {
		global $translation;
		if(is_array($file)) {
			if ($file["error"] > 0) {
				//return "Error: " . $file["error"];
				switch ($file["error"]) {
					case UPLOAD_ERR_INI_SIZE:
						return $translation->getWord("UPLOAD_ERR_INI_SIZE");
						break;
					case UPLOAD_ERR_FORM_SIZE:
						return $translation->getWord("UPLOAD_ERR_FORM_SIZE");
						break;
					case UPLOAD_ERR_PARTIAL:
						return $translation->getWord("UPLOAD_ERR_PARTIAL");
						break;
					case UPLOAD_ERR_NO_FILE:
						return $translation->getWord("UPLOAD_ERR_NO_FILE");
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						return $translation->getWord("UPLOAD_ERR_NO_TMP_DIR");
						break;
					case UPLOAD_ERR_CANT_WRITE:
						return $translation->getWord("UPLOAD_ERR_CANT_WRITE");
						break;
					case UPLOAD_ERR_EXTENSION:
						return $translation->getWord("UPLOAD_ERR_EXTENSION");
						break;
					
					default:
						return $translation->getWord("UNKNOWN_ERROR");
						break;
				}
			}
			return "<html>
		   		<body onload='window.parent.uploadFinishedHandler(document.body.textContent||document.body.innerText)'>".
		   		base64_encode(json_encode(array(
		   			"name" => $file["name"],
		   			"type" => $file["type"],
					"size" => $file["size"],
					"content" => base64_encode(file_get_contents($file["tmp_name"]))
		   		))).
		   		"</body></html>";
		} else {
			return "<form action='".$_SERVER['PHP_SELF']."?{$query}' method='post' enctype='multipart/form-data'><input type='file' name='{$fieldname}'>";
		}
	}
	public static function downloadFile($data, $filename) {
		if(isset($data)) {
			header ("Content-Type: application/force-download");
			header ("Accept-Ranges: bytes");
			header ("Content-Length: ".strlen($data)); 
			header ("Content-Disposition: attachment; filename={$filename}");  
			return $data;
		} else {
			return "<form action='".$_SERVER['PHP_SELF']."?savedata' method='post'><textarea name='savedata'></textarea>
			<input type='text' name='filename'><input type='submit' value='Upload'></form>";
		}
	} 
}
?>