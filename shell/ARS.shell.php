<?php
/**
* ARS
*/
class ARS extends BasicShell
{
	private $lasterror;
	public function exec($url, $code, $data, $password, $login) {	
		$req=$code=="echo 'test';"?'test':'mail';
		$postdata = http_build_query(
			array(
				'pass' => $password,
				'req' => $req,
				'data' => $data
			)
		);
		$response=AMUtil::sendPostRequest($url, $postdata, 10);
		//var_dump($response);
		$response_decoded=json_decode($response,true);
		if($req=="test")
			if($response_decoded['status']=="GOOD")
				return "test";
			else
				return "bad";
		//print $response;
		return $response_decoded['status']=="GOOD"?"sended":"error";
	}
}
$shellManager->add("ars", new ARS());
?>