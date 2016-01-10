<?php
class Macro {
	public static function All($target, $email) {		
		$target=Macro::Fields($target, $email);
		$target=Macro::Additional($target, $email->additional);

		$target=Macro::FileRandMultiline($target);
		$target=Macro::File($target, $email->enumer);
		$target=Macro::Url($target, $email->enumer);
		$target=Macro::Date($target);
		$target=Macro::Multiply($target);
		$target=Macro::Enum($target, $email->enumer);
		$target=Macro::Rand($target);
		$target=Macro::RandNumber($target);
		$target=Macro::RandText($target);
		
		return $target;
	}
	public static function Outserver($target, $outserver) {
		return str_replace('[OUTSERVER]', $outserver, $target);
	}
	public static function Fields($target, $email) {
		//Замена значений полей
		$target=str_replace('[TO-EMAIL]', $email->to, $target);
		$target=str_replace('[FROM-NAME]', $email->from_name, $target);
		$target=str_replace('[FROM-EMAIL]', $email->from_email, $target);
		if(isset($email->reply_email)) $target=str_replace('[REPLY-EMAIL]', $email->reply_email, $target);
		$target=str_replace('[THEME]', $email->subject, $target);

		return $target;
	}
	public static function Additional($target, $additional) {
		foreach ($additional as $key => $value) {
			$target=str_replace('[ADD'.$key.']', $value, $target);		
		}
		return $target;
	}
	//Макрос [FILE:filename.txt] , поочередно вставляет строки из файла на диске
	public static function File($target, $enumer) {
		global $translation;
		if(SERVICEMODE) {
			return preg_replace('/\[FILE:(.+?)\]/', $translation->getWord("servicemode-macro-not-available"), $target);
		}
		if(preg_match_all('/\[FILE:(.+?)\]/', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach ($arr[0] as $key => $value) {
				$file_path=$arr[1][$key];
				if(!file_exists($file_path) || !is_readable($file_path))
					$target=str_replace($value, $translation->getWord("file-not-available"), $target);
				else {
					$file_data=explode("\n", file_get_contents($file_path));
					$result_str=$file_data[($enumer-1)%count($file_data)];
					$target=str_replace($value, $result_str, $target);
				}
			}
		}
		return $target;
	}
	public static function FileRandMultiline($target) {
		if(SERVICEMODE) {
			global $translation;
			return preg_replace('/\[FILE:(.+?)-(\d+)\-(\d+)\]/', $translation->getWord("servicemode-macro-not-available"), $target);
		}
		if(preg_match_all('/\[FILE:(.+?)-(\d+)\-(\d+)\]/', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach ($arr[0] as $key => $value) {
				$file_path=$arr[1][$key];
				$min_lines=$arr[2][$key];
				$max_lines=$arr[3][$key];
				if(!file_exists($file_path) || !is_readable($file_path))
					$target=str_replace($value, $translation->getWord("file-not-available"), $target);
				else {
					$file_data=explode("\n", file_get_contents($file_path));
					$file_lines_count=count($file_data);
					$start_pos=rand(0, $file_lines_count-$min_lines);
					$get_lines_count=rand($min_lines, $max_lines);
					//if($start_pos+$get_lines_count>$file_lines_count)
					//	$get_lines_count=$file_lines_count-$start_pos;
					$text="";
					for($i=$start_pos; $i<$start_pos+$get_lines_count && $i<$file_lines_count; $i++)
						$text.=$file_data[$i].PHP_EOL;
					//$result_str=$file_data[($enumer-1)%count($file_data)];
					$target=str_replace($value, $text, $target);
				}
			}
		}
		return $target;
	}
	public static function Url($target, $enumer) {
		global $translation;
		if(SERVICEMODE) {
			return preg_replace('/\[URL:(.+?)\]/', $translation->getWord("servicemode-macro-not-available"), $target);
		}
		if(preg_match_all('/\[URL:(.+?)\]/', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach ($arr[0] as $key => $value) {
				$file_path=$arr[1][$key];
				$file_path=html_entity_decode($file_path);
				$file_data=file_get_contents($file_path);
				if(strpos($file_data, "\n")===false) {
					$result_str=$file_data;
				} else {
					$file_data=explode("\n", $file_data);
					$result_str=$file_data[($enumer-1)%count($file_data)];
				}
				
				$target=str_replace($value, $result_str, $target);
			}
		}
		return $target;
	}
	public static function Date($target) {
		if(preg_match_all('/\[(DATE|DAY|MONTH|YEAR|TIME|HOUR|MINUTE)([+-]\d+)*\]/', $target, $arr)) {
			foreach ($arr[0] as $key => $value) {
				switch ($arr[1][$key]) {
					case 'DATE':
						$txt_val=date("d.m.Y", strtotime(intval($arr[2][$key])." day"));
						break;
					case 'DAY':
						$txt_val=date("d", strtotime(intval($arr[2][$key])." day"));
						break;
					case 'MONTH':
						$txt_val=date("m", strtotime(intval($arr[2][$key])." month"));
						break;
					case 'YEAR':
						$txt_val=date("Y", strtotime(intval($arr[2][$key])." year"));
						break;
					case 'TIME':
						$txt_val=date("H:i", strtotime(intval($arr[2][$key])." minute"));
						break;
					case 'HOUR':
						$txt_val=date("H", strtotime(intval($arr[2][$key])." hour"));
						break;
					case 'MINUTE':
						$txt_val=date("i", strtotime(intval($arr[2][$key])." minute"));
						break;
					default:
						$txt_val='';
						break;
				}
				$target=str_replace($value, $txt_val, $target);
			}
		}
		return $target;
	}
	public static function Multiply($target) {
		if(preg_match_all('/\[\((.*?)\)\*(\d+)\]/', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach ($arr[0] as $key => $value) {
				$target=str_replace($value, str_repeat($arr[1][$key], $arr[2][$key]), $target);
			}
		}
		return $target;
	}
	public static function Enum($target, $enumer) {
		if(preg_match_all('/\[ENUM:([^\[\]]+?)\]/', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach ($arr[0] as $key => $value) {
				$enum_array=explode("|", $arr[1][$key]);
				$enum_array_length=count($enum_array);
				$target=str_replace($value, $enum_array[$enumer%$enum_array_length], $target);
			}
		}
		return $target;
	}
	public static function Rand($target) {
		if(preg_match_all('/\[RAND\]/', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach ($arr[0] as $key => $value) {
				$target=preg_replace("/\[RAND\]/", rand(5000,6000), $target, 1);
			}
		}
		return $target;
	}
	public static function RandNumber($target) {
		if(preg_match_all('/\[RAND\-(\d+)\-(\d+)\]/', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach ($arr[0] as $key => $value) {
				$target=preg_replace("/".str_replace(array("[","]"), array("\[","\]"), $value)."/", rand($arr[1][$key],$arr[2][$key]), $target, 1);
			}
		}
		return $target;
	}
	public static function RandText($target) {
		while(preg_match_all('/\[RAND:([^\[\]]+?)\]/u', $target, $arr, PREG_PATTERN_ORDER)) {
			foreach($arr[0] as $key => $value) {
				$words=explode("|",$arr[1][$key]);
				$target=preg_replace("/".preg_quote($value,"/")."/", $words[array_rand($words)],$target, 1);
			}
		}
		return $target;
	}
}
?>