<?php $logpass=""; //FORMAT: md5(loginIMAILpassword); ?>
<?php
define('DEBUG', FALSE);
define('SIMULATION', TRUE);
define('SERVICEMODE', TRUE);

if(DEBUG) {
	ini_set('display_errors',1);//1
	ini_set('display_startup_errors',1);//1
	error_reporting(-1);//-1
} else {
	ini_set('display_errors',0);//1
	ini_set('display_startup_errors',0);//1
	error_reporting(0);//-1
}

header('Content-type: text/html; charset=utf-8;'); 
#Alexus(240980845) - http://www.a-l-e-x-u-s.ru/
#CREATED AT 15.12.2011
#UPD 02.04.2012 v 1.1
#UPD 10.04.2012 v 1.2
#UPD 30.05.2012 v 1.3
#UPD 02.06.2012 v 1.3.1
#UPD 20.10.2012 v 1.4
#UPD 16.02.2013 v 1.5
#UPD 15.04.2013 v 1.5.1
#UPD 01.06.2013 v 1.6
#UPD 15.09.2013 v 1.6.5
#UOD 30.04.2014 v 1.7
#UPD 11.08.2014 v 1.7.1
#UPD 15.09.2014 v 2.0b
#UPD 22.09.2014 v 2.0
#UPD 24.09.2014 v 2.0.1
#UPD 06.10.2014 v 2.0.2
#UPD 20.10.2014 v 2.0.3
define("VERSION", "2.0");
define("FULLVERSION", "2.0.8");
define("RELEASEDATE", "08-12-2014");
$boundary="--".AMUtil::randomString(10);
$timezone='Europe/Moscow';
/**
Запрос авторизации
*/
if($logpass!="") {
	if(!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="IMAIL"');
	    header('HTTP/1.0 401 Unauthorized');
	    print "Authentification required!";
	    exit;
	} else {
		if(md5($_SERVER['PHP_AUTH_USER']."IMAIL".$_SERVER['PHP_AUTH_PW'])!=$logpass) {
			header('WWW-Authenticate: Basic realm="IMAIL"');
	    header('HTTP/1.0 401 Unauthorized');
			print 'Wrong login or password!';
			exit;
		}
	}
}
if(isset($_COOKIE['timezone']) && $_COOKIE['timezone']!="")
	$timezone=$_COOKIE['timezone'];
date_default_timezone_set($timezone);

//Определяем язык пользователя
$translation=new Translation("ru", !SERVICEMODE);
$shellManager=new ShellManager();
$filesContainer=new FilesContainer();
/*$lang="ru";
if (isset($_COOKIE['translation'])) 
	$lang=$_COOKIE['translation'];*/
if(isset($_POST['PROXY'])) {
	$proxy_server=parse_url($_POST['PROXY']);
	define("PROXY",$proxy_server['host'].":".$proxy_server['port']);
}

//Фильтруем переданные параметры
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
?>