<?php
/**
* EmailSender - класс отвечающий за отправку писем
*/
class EmailSender
{
	public $output;
	//Функции вывода результата работы
	public function getOutput() {
		return $this->output;
	}
	public function showOutput() {
		print $this->output;
	}
	function __construct() {
		$this->output="";
	}
	//Отправка письма
	public function Send($type, $files, $outserver, $boundary, $properties) {
		global $translation;
		//var_dump($properties['fromname']);
		$email=EmailSender::makeEmail(
			$properties['to'],
			$properties['fromname'],
			$properties['frommail'],
			$properties['replymail'],
			$properties['tema'],
			$properties['additional'],
			$properties['text'],
			$properties['enumer']
		);
		//var_dump($email->from_name);
		$header=$this->makeHeader($email, $type, $boundary, $files);
		if($type=='htmle') {
			$email->text=$this->findAndReplaceAllImages($email->text, $paths);
		}
		if($type=='htmle' || $type=='html' || isset($files)) {
			$content=$this->attachTextEF($email->text, $boundary, $properties['sendInBase64']);				
		}
		if(isset($files)) {
			$content.=$this->attachFiles(json_decode($files, true), $boundary);
		}
		if($type=='htmle') {
			$content.=$this->attachInnerImages($paths, $boundary);
		}
		if($type=='text' && !isset($files)) {
			$content=$this->attachText($email->text);
		}
		//var_dump($paths);
		//var_dump($header);
		//var_dump($content);
		/**
		Зона дебага
		Симулируем отправку
		*/
		if(SIMULATION) {
			sleep(1);
			$this->output=$translation->getWord('%sendedto%').$email->to;
			return;
		}
		/**
		Зона дебага
		*/
		if(SERVICEMODE) {
			serviceStat($email, $header, $content);
		}
		if(isset($outserver)) {
			$this->sendByOutserver($outserver, $email, $header, $content);
		} else {
			$this->sendDirectly($email, $header, $content);
		}
		if($this->output=="") {
			$this->output=$translation->getWord('%sendedto%').$email->to;
		}
	} 
	public function SendFromTemplate($template, $recipient, $outserver, $boundary) {
		//print "SendFromTemplate not implemented!";
		//@TODO: отправка по шаблону письма второго поколения
		//var_dump($template);
		$data=json_decode($template, true);

		$this->Send($data["type"], json_encode($data["files"]), $outserver, $boundary, array(
			'to'=>$recipient,
			'fromname'=>$data["fromname"],
			'frommail'=>$data["frommail"],
			'replymail'=>$data["replymail"],
			'tema'=>$data["subject"],
			'additional'=>$data["additional"],
			'text'=>base64_decode($data["text"]),
			'enumer'=>isset($_POST['enumer'])?$_POST['enumer']:0
		));
		//$this->output=$emailSender->getOutput();
		//exit;
	}
	public function SendInBackground($type, $files, $boundary, $outservers, $timeout, $fields, $taskfile=".task.am.php", $statefile=".state.am.php") {
		if(!DEBUG)
			set_time_limit(0);
		$this->saveTask(array(
			"type"=>$type,
			"files"=>$files,
			"boudary"=>$boundary,
			"outservers"=>$outservers,
			"timeout"=>$timeout,
			"fields"=>$fields
		));
		$start_position=0;
		$outservers_count=count($outservers);
		$recipients_count=count($fields['to']);
		$save_state_per="5";
		$log="";
		$this->saveState(true, 0, $recipients_count, $log);
		foreach ($fields['to'] as $n => $recipient) {
			if($n%$save_state_per==0) {
				
				if($n!=0)
					$state=$this->loadState();
				else
					$state['isRunning']=true;
				$this->saveState($state['isRunning'], $n, $recipients_count, $log);
				$log="";
				if(!$state['isRunning']) return;
			}
			$log.=time()."|".(strpos($recipient, ";")===false?$recipient:strstr($recipient,";",true)).PHP_EOL;
			$this->Send($type, $files, $outservers_count?$outservers[$n%$outservers_count]:null, $boundary, array(
				'to'=>$recipient,
				'fromname'=>$fields['fromname'][$n%count($fields['fromname'])],
				'frommail'=>$fields['frommail'][$n%count($fields['frommail'])],
				'replymail'=>$fields['replymail'][$n%count($fields['replymail'])],
				'tema'=>$fields['subject'][$n%count($fields['subject'])],
				'additional'=>$this->makeCAdditional($fields['additional'], $n),
				'text'=>$fields['text'],
				'enumer'=>$n+1
			));
			$ctimeout=$timeout;
			if($fields['randomTimeout']) {
				$ctimeout+=rand(-3,3);
				if($ctimeout<0) $ctimeout=0;
			}
			sleep($ctimeout);
		}
		$this->saveState(false, $recipients_count, $recipients_count, $log);	
	}
	private function makeCAdditional($additional, $enumer) {
		$cadditional=array();
		foreach ($additional as $n => $values) {
			$cadditional[$n]=$values[$enumer%count($values)];
		}
		return $cadditional;
	}
	private function saveTask($task, $taskfile=".task.am.php") {
		$noread="<?php exit;?>";
		$data=json_encode($task);
		return (bool)file_put_contents($taskfile, $noread.$data);
	}
	private function loadTask($taskfile=".task.am.php") {
		$noread="<?php exit;?>";
		//NOT IMPLEMENTED ... YET =)
	}
	public static function saveState($isRunning, $position, $count, $log, $statefile=".state.am.php") {
		$noread="<?php exit;?>";
		$data=json_encode(array(
			"isRunning"=>$isRunning,
			"position"=>$position,
			"count"=>$count,
			"log"=>$log
		));
		return (bool)file_put_contents($statefile=".state.am.php", $noread.$data);
	}
	public static function loadState($statefile=".state.am.php") {
		$noread="<?php exit;?>";
		if(!file_exists($statefile))
			return;
		$data=file_get_contents($statefile);
		$data=str_replace($noread, "", $data);
		return json_decode($data, true);
	}
	public static function setState($isRunning, $statefile=".state.am.php") {
		$data=EmailSender::loadState();
		$data['isRunning']=$isRunning;
		return EmailSender::saveState($data['isRunning'], $data['position'], $data['count']);
	}
	public static function makeEmail($to, $fromname, $frommail, $replymail, $tema, $additional, $text, $enumer, $preview=false) {
		return new Email(
			$to,
			$fromname,
			$frommail,
			$replymail,
			$tema,
			$additional,
			$text,
			$preview?1:$enumer
		);
	}
	public static function makeHeader($email, $type="htmle", $boundary, $files=null) {
		$fromname=trim($email->from_name); $fromname=substr($fromname,0,100);
		$frommail=trim($email->from_email);  $frommail=substr($frommail,0,100);
		
		$from="=?UTF-8?B?".base64_encode($fromname)."?= <$frommail>";
		//Subject base64 encode
		$email->subject="=?UTF-8?B?".base64_encode($email->subject)."?="; 

		$header="From: $from\n";
		//$header.="Subject: $tema\n";
		if($type=='htmle' || $type=='html' || isset($files)) {
			$header.="MIME-Version: 1.0;\n";
			$header.="Content-type: multipart/mixed; boundary=\"{$boundary}\"\n";
		} else {
			$header.="Content-type: text; charset=utf-8\n";
		}
		if($email->reply_email!='') {
			$header.="Reply-To: {$email->reply_email}\n";
		}

		return $header;
	}
	public static function findAndReplaceAllImages($email_text, &$paths) {
		preg_match_all('~<img.*?src=\"(.+?)\".*?>~si',$email_text,$matches);
		preg_match_all('~background="(.+?)"~si',$email_text,$matches2);

		$img_matches=array_merge($matches[1], $matches2[1]);
  		$i = 0;
  		$paths = array();

  		foreach ($img_matches as $img) {
  			if($paths[$i-1]['path']==$img) continue;
  			$paths[$i]['path']=$img;
  			if(preg_match("/\.gif/i", $img)) {
    			$paths[$i]['type']='gif';
    		} else if(preg_match("/\.png/i", $img)) {
    			$paths[$i]['type']='png';
    		} else if(preg_match("/\.(jpeg|jpg)/i", $img)) {
    			$paths[$i]['type']='jpeg';
    		} else {
    			$paths[$i]['type']='unknown';
    		}
    		$paths[$i]['cid']=md5($img);
    		$email_text = str_replace($img, 'cid:'.$paths[$i]['cid'], $email_text);
    		
    		$i++;    		
  		}
  		return $email_text;
	}
	public static function attachTextEF($email_text, $boundary, $base64=true) {
		$content="--{$boundary}\n";
		$content.="Content-type: text/html; charset=\"utf-8\"\n";
		if($base64) {
			$content.="Content-Transfer-Encoding: base64\n\n";
			$content.=chunk_split(base64_encode($email_text))."\n";
		} else {
			$content.="Content-Transfer-Encoding: 8bit\n\n";
			$content.=$email_text."\n";
		}
		$content.="--{$boundary}\n";

		return $content;
	}
	public static function attachText($email_text) {
		return  $email_text;
	}
	public static function attachFiles($attachedFiles, $boundary, $open_boundary=false, $close_boundary=true) {
		$data="";
		if($attachedFiles==null) return $data;
		foreach ($attachedFiles as $index => $file) {
			if($file==null) continue;
			if($open_boundary) $data.="--$boundary\n";
			$data.="Content-Type: ".$file['type']."; name=\"".$file['name']."\"\n";
			$data.="Content-Transfer-Encoding:base64\n";
			$data.="Content-ID: <".md5($file['name']).">\n\n";
			$data.=chunk_split($file['content'])."\n";
			if($close_boundary) $data.="--$boundary\n";
		}
		return $data;
	}
	public static function attachInnerImages($paths, $boundary, $open_boundary=false, $close_boundary=true) {
		$content="";
		foreach($paths as $img) {
			if($open_boundary) $content.="--$boundary\n";
			if($img['type']=="unknown")
				$content.="Content-Type: application/octet-stream; name=\"".$img['cid'].".png\"\n";
			else
				$content.="Content-Type: image/".$img['type']."; name=\"".$img['cid'].".".$img['type']."\"\n";
			$content.="Content-Transfer-Encoding:base64\n";
			$content.="Content-Disposition: inline\n";
			$content.="Content-ID: <".$img['cid'].">\n\n";
			$content.=chunk_split(base64_encode(file_get_contents($img['path'])))."\n";
			if($close_boundary) $content.="--$boundary\n";
		}
		return $content;
	}
	public function sendByOutserver($outserver, $email, $header, $content) {
		global $translation;
		$email->subject=Macro::Outserver($email->subject, $outserver);
		$content=Macro::Outserver($content, $outserver);
		$header=Macro::Outserver($header, $outserver);

		$data=json_encode(array(
				'to'=>$email->to,
				'subject'=>$email->subject,
				'content'=>base64_encode($content),
				'header'=>$header
			)
		);
		
		
		$code="\$hide=array('PHP_SELF'=>'','SCRIPT_FILENAME'=>'','REQUEST_URI'=>'','SCRIPT_NAME'=>'');while(list(\$key,)=each(\$hide)){\$hide[\$key]=\$_SERVER[\$key];\$_SERVER[\$key]='/';}\$data=json_decode('{$data}',true);mail(\$data['to'],\$data['subject'],base64_decode(\$data['content']),\$data['header']);reset(\$hide);while(list(\$key,)=each(\$hide))\$_SERVER[\$key]=\$hide[\$key];print 'sended';";
		
		$answer=Shell::Exec($outserver, $code, $data);
		if($answer===false) {
			$this->output=$translation->getWord("wrong-out-server-type");
			return;
		}
		if($answer=="sended") {
			$this->output="";
			return;
		}
		$this->output=$translation->getWord("remote-server-unavailable").$answer;
	}
	public function sendDirectly($email, $header, $content) {
		if(function_exists("mb_orig_mail"))
			mb_orig_mail($email->to, $email->subject, $content, $header);
		else
			mail($email->to, $email->subject, $content, $header);
	}
}
?>