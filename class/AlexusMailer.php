<?php
/**
* Основной класс йаПосылалки2
*/
class AlexusMailer
{
	private $query;
	private $output;
	private $email;
	private $files;
	private $outserver;
	private $shellManager;
	private $saveInTxtLog;
	public function getOutput() {
		return $this->output;
	}
	public function showOutput() {
		print $this->output;
	}
	function __construct($query, $boundary, $logpass, $shellManager, $type="htmle", $auto=true)
	{
		global $translation;
		$this->query=$query;
		$this->files=isset($_POST['files'])?$_POST['files']:null;
		$this->outserver=isset($_POST['outserver'])?$_POST['outserver']:null;
		$this->saveInTxtLog=isset($_POST['saveLogInTxt'])&&$_POST['saveLogInTxt']=="true"?true:false;
		if(SERVICEMODE) $this->outserver=null;
		$this->output="";
		$this->shellManager=$shellManager;

		if(!$auto) return;

		if($this->query=="preview") {
			if(SERVICEMODE) {
				if($type=="text")
					$_POST['text']=$translation->getWord("service-warning-text").PHP_EOL.$_POST['text'];
				else
					$_POST['text']="<div style='color:red;font-weight:bold;'>".$translation->getWord("service-warning-text")."</div>".$_POST['text'];
			}	
			$email=EmailSender::makeEmail(
				$_POST['to'],
				$_POST['fromname'],
				$_POST['frommail'],
				$_POST['replymail'],
				$_POST['tema'],
				$_POST['additional'],
				$_POST['text'],
				$_POST['enumer'],
				$this->query=="preview"
			);
			$this->output=$email->text;
			return;
		}

		if($this->query=="send") {
			$emailSender=new EmailSender();
			if(SERVICEMODE) {
				if($type=="text")
					$_POST['text']=$translation->getWord("service-warning-text").PHP_EOL.$_POST['text'];
				else
					$_POST['text']="<div style='color:red;font-weight:bold;'>".$translation->getWord("service-warning-text")."</div>".$_POST['text'];
			}
			$emailSender->Send($type, $this->files, $this->outserver, $boundary, array(
				'to'=>$_POST['to'],
				'fromname'=>$_POST['fromname'],
				'frommail'=>$_POST['frommail'],
				'replymail'=>$_POST['replymail'],
				'tema'=>$_POST['tema'],
				'additional'=>$_POST['additional'],
				'text'=>$_POST['text'],
				'sendInBase64'=>($_POST['sendInBase64']=="true"),
				'enumer'=>$_POST['enumer']
			));
			$this->output=$emailSender->getOutput();
			if($this->saveInTxtLog)
				$this->saveToTxtLog($this->output);
			return;
		}

		if($this->query=="sendFromTemplate") {
			if(SERVICEMODE) return;
			$emailSender=new EmailSender();
			$emailSender->SendFromTemplate($_POST['template'], $_POST['to'], $this->outserver, $boundary);
			$this->output=$emailSender->getOutput();
			if($this->saveInTxtLog)
				$this->saveToTxtLog($this->output);
			return;
		}

		if($this->query=="upload_universal") {
			$this->output=TransferIface::uploadFile(isset($_FILES['elist'])?$_FILES['elist']:null);
			return;
		}

		if($this->query=="savedata") {
			$this->output=TransferIface::downloadFile(isset($_POST['savedata'])?$_POST['savedata']:null, $_POST['filename']);
			return;
		}

		if($this->query=="changepass") {
			if(SERVICEMODE) return;
			$this->output=$this->changePass($_POST['login'], $_POST['pass'], $logpass);
			return;
		}

		if($this->query=="pingoutserver") {
			if(SERVICEMODE) return;
			$this->output=Shell::check($_POST['server']);
			return;
		}

		if($this->query=="linesinfile") {
			if(SERVICEMODE) return;
			$this->output=AMUtil::linesInFile($_POST['file_path']);
			return;
		}

		if($this->query=="saveSettings") {
			if(SERVICEMODE) return;
			$result=Settings::Save($_POST['settings']);
			if($result) {
				$this->output=$translation->getWord("settings-saved");
			} else {
				$this->output=$translation->getWord("settings-save-error");
			}
			return;
		}

		if($this->query=="removeSettings") {
			if(SERVICEMODE) return;
			Settings::Remove();
			$this->output=$translation->getWord("settings-removed");
			return;
		}

		if($this->query=="loadSettings") {
			$data=Settings::Load();
			if(!$data) 
				$this->output="";
			else
				$this->output=$data;
			return;
		}

		if($this->query=="sendInBackground") {
			if(SERVICEMODE) return;
			$emailSender=new EmailSender();
			$outservers=isset($_POST['outservers'])?explode("\n", $_POST['outservers']):array();
			$additional=array();
			foreach ($_POST['additional'] as $n => $values) {
				$additional[$n]=explode("\n", $values);
			}
			$emailSender->sendInBackground($_POST['type'], $_POST['files'], $boundary, $outservers, $_POST['timeout'], array(
				'to'=>explode("\n", $_POST['to']),
				'fromname'=>explode("\n", $_POST['fromname']),
				'frommail'=>explode("\n", $_POST['frommail']),
				'replymail'=>explode("\n", $_POST['replymail']),
				'subject'=>explode("\n", $_POST['subject']),
				'additional'=>$additional,
				'text'=>$_POST['text'],
				'sendInBase64'=>$_POST['sendInBase64'],
				'randomTimeout'=>$_POST['randomTimeout'],
				'enumer'=>null
			));
			//NO RETURN ^_^
		}
		if($this->query=="getBackgroundState") {
			if(SERVICEMODE) return;
			$this->output=json_encode(EmailSender::loadState());
			return;
		}
		if($this->query=="setBackgroundState") {
			if(SERVICEMODE) return;
			$this->output=EmailSender::setState($_POST['isRunning']=="true");
			return;
		}
		if($this->query=="selfDiagnostics") {
			//if(SERVICEMODE) return;
			$this->output=$this->selfDiagnostics();
			return;
		}
	}
	public function checkOutServer($outserver) {
		list($url,$type,$password)=explode("|",$outserver);
		if(strpos($password, ":")!=-1)
			list($login, $password)=explode(":", $password);

		$this->shellManager->check($type, $url, $password, $login);
	}
	public function changePass($login, $password, $logpass, $separator="IMAIL") {
		clearstatcache();
		$response=array();
		//Файл не доступен на запись
		if(!is_writable(__FILE__)) {
			$response['result']="error";
		//Пустые логин и пароль ведут к их удалению	
		} elseif($login=="" && $password=="") {
			$data=file_get_contents(__FILE__);
			$data=str_replace($logpass, "", $data);
			file_put_contents(__FILE__, $data);
			$response['result']="ok";
		//Заменяем на новый логин&пароль
		} else {
			$new_logpass=md5($login."IMAIL".$password);
			$data=file_get_contents(__FILE__);
			if($logpass=="") {
				$data=str_replace('$logpass="";', '$logpass="'.$new_logpass.'";', $data);
			} else {
				$data=str_replace($logpass, $new_logpass, $data);
			}
			file_put_contents(__FILE__, $data);
			$response['result']="ok";
		}
		return json_encode($response);
	}
	public function selfDiagnostics() {
		$diagnostics=array(
			"file_is_writable"=>is_writable(__FILE__),
			"dir_is_writable"=>is_writable("."),
			"settings_is_writable"=>file_exists("amsettings.php")?is_writable("amsettings.php"):is_writable("."),
			"bgfiles_is_writable"=>(file_exists(".state.am.php")?is_writable(".state.am.php"):is_writable(".") && file_exists(".task.am.php")?is_writable(".task.am.php"):is_writable(".")),
			"shells_available"=>is_dir("shell"),
			"allow_url_fopen"=>(bool)ini_get('allow_url_fopen'),
			"post_max_size"=>ini_get('post_max_size'),
			"upload_max_filesize"=>ini_get('upload_max_filesize')
		);
		return json_encode($diagnostics);
	}
	public function saveToTxtLog($data, $newline=true, $filename="mailerlog.txt") {
		$date = date('m/d/Y H:i:s', time());
		file_put_contents($filename, $date." ## ".$data.($newline?PHP_EOL:""), FILE_APPEND);
	}
}
?>