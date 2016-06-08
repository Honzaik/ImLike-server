<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Authentication extends Model{

	public $userId;
	public $username;
	public $token;
	public $aesKey;

}