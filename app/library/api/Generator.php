<?php
namespace ImLike\Api;

use ImLike\Models\Authentication;

class Generator{

	public static function generateAuthToken(){
		$tokenLenght = 64;
		$isSecure = true;
		$token = bin2hex(openssl_random_pseudo_bytes($tokenLenght, $isSecure));
		$auth = Authentication::findFirstByToken($token);
		if($auth) return $this->generateAuthToken(); // generate new token - recursion, tokens are 128 chars long. so max number of combinations is 16^128 ? so theoretically it could take forever but this number is over 150 zeros long so.. i guess not
		else return $token;
	}

	public static function generateEmailConfirmationToken(){
		$tokenLenght = 25;
		$isSecure = true;
		$token = bin2hex(openssl_random_pseudo_bytes($tokenLenght, $isSecure));
		return $token;
	}

	public static function generateAESKey(){
		$keyLenght = 32; // 256-bit
		$isSecure = true;
		$aesKey = bin2hex(openssl_random_pseudo_bytes($keyLenght, $isSecure));
		return $aesKey;
	}

}