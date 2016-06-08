<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Users extends Model{

	public $userId;
	public $email;
	public $username;
	public $password;
	public $createdAt;
	
	public function beforeValidation(){
		if($this->createdAt == null || $this->createdAt == "") $this->createdAt = date('Y-m-d H:i:s', time());
	}
}