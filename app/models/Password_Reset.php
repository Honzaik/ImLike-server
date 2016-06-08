<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Password_Reset extends Model{

	public $userId;
	public $token;

	 public function initialize()
    {
        $this->setSource("password_reset");
    }

}