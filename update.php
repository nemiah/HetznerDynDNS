<?php
error_reporting(E_ALL);

$token = "";

$username = filter_input(INPUT_GET, "username");
if(isset($_SERVER['PHP_AUTH_USER']))
	$username = $_SERVER['PHP_AUTH_USER'];

$password = filter_input(INPUT_GET, "password");
if(isset($_SERVER['PHP_AUTH_PW']))
	$password = $_SERVER['PHP_AUTH_PW'];

$domain = filter_input(INPUT_GET, "domain");
if(filter_input(INPUT_GET, "hostname"))
	$domain = filter_input(INPUT_GET, "hostname");

$ip = filter_input(INPUT_GET, "ip");
if(filter_input(INPUT_GET, "myip"))
	$ip = filter_input(INPUT_GET, "myip");

$c = new mysqli("localhost", "phpddns", "tkwu0xrrF0JqMShc", "phpddns");

$q = $c->query("SELECT * FROM 
		NSUser
	WHERE 
		NSUserName = '".$c->real_escape_string($username)."'
		AND NSUserPassword = '".$c->real_escape_string(sha1($password))."' 
		AND NSUserDomain = '".$c->real_escape_string($domain)."'");

$t = $q->fetch_object();

if(!$t){
	header("HTTP/1.0 401 Unauthorized");
	die();
}

$ex = explode(".", $domain);
$sub = $ex[0];
unset($ex[0]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://dns.hetzner.com/api/v1/zones?name='.implode(".", $ex));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Auth-API-Token: '.$token,
]);


$response = curl_exec($ch);
if (!$response) {
	header("HTTP/1.0 401 Unauthorized");
	die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($status != 200){
	header("HTTP/1.0 401 Unauthorized");
	die('Error: status code != 200 ('.$status.')');
}


$zone = json_decode($response);
curl_close($ch);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://dns.hetzner.com/api/v1/records?zone_id='.$zone->zones[0]->id);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Auth-API-Token: '.$token,
]);


$response = curl_exec($ch);
if (!$response)  {
	header("HTTP/1.0 401 Unauthorized");
	die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($status != 200){
	header("HTTP/1.0 401 Unauthorized");
	die('Error: status code != 200 ('.$status.')');
}

$id = null;
$records = json_decode($response);
foreach($records->records AS $record){
	if($record->name == $sub)
		$id = $record->id;
}
curl_close($ch);

if(!$id){
	header("HTTP/1.0 401 Unauthorized");
	die();
}
	
	
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://dns.hetzner.com/api/v1/records/'.$id);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Auth-API-Token: '.$token,
]);

$json_array = [
  'value' => $ip,
  'ttl' => 60,
  'type' => 'A',
  'name' => $sub,
  'zone_id' => $zone->zones[0]->id
]; 
$body = json_encode($json_array);


curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

$response = curl_exec($ch);

if (!$response)  {
	header("HTTP/1.0 401 Unauthorized");
	die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($status != 200){
	header("HTTP/1.0 401 Unauthorized");
	die('Error: status code != 200 ('.$status.')');
}

curl_close($ch);
	
echo "good ".$ip;
