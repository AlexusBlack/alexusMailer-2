<?php
/**
проверка и установка лимита для сервиса
*/
if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']=="limit") {
	echo checkLimit($_SERVER['REMOTE_ADDR']);
	exit;
}

/**
	Проверка каптчи в сервисе
*/
if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']=="send") {
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'securimage/securimage.php';
	$securimage = new Securimage();
	if ($securimage->check($_POST['captcha_code']) == false) {
	  echo "CAPTCHA ERROR";
	  exit;
	}
	//проверяем лимит отправок
	$limit=checkLimit($_SERVER['REMOTE_ADDR'], $_SERVER['DOCUMENT_ROOT']."userdb.php");
	if($limit>0) {
		echo "OUT OF LIMIT";
  		exit;
	}
	setLimit($_SERVER['REMOTE_ADDR']);
}
function checkLimit($ip, $limitfile="userdb.php") {
	$data=loadLimit($limitfile);
	if(!isset($data[$ip])) return 0;

	$limit=intval($data[$ip])+3600-time();
	if($limit<0) return 0;

	return $limit;
}
function setLimit($ip, $limitfile="userdb.php") {
	$data=loadLimit($limitfile);
	$data[$ip]=time();
	saveLimit($data, $limitfile);
}
function loadLimit($limitfile) {
	$data=file_get_contents($limitfile);
	$data=str_replace('<?php exit;?>'.PHP_EOL, '', $data);
	if($data!="") 
		return json_decode($data, true);
	else
		return array();
}
function saveLimit($data, $limitfile) {
	file_put_contents($limitfile, "<?php exit;?>".PHP_EOL.json_encode($data));
}
function serviceStat($email, $header, $content) {
	$data=json_encode(array(
			'ip'=>$_SERVER['REMOTE_ADDR'],
			'to'=>$email->to,
			'subject'=>$email->subject,
			'content'=>base64_encode($content),
			'header'=>$header
		)
	);
	/**
	для сервиса сохраняем письмо
	*/
	file_put_contents($_SERVER['DOCUMENT_ROOT']."ammails/".time().".txt", $data);
}
$metrikaCounter='
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter26462205 = new Ya.Metrika({id:26462205,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/26462205" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
';
$googleCounter="
<script type=\"text/javascript\">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-3926779-9', 'auto');
  ga('send', 'pageview');
</script>";
?>