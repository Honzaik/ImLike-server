<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Registration_Confirmation extends Model{

	public $userId;
	public $token;
	public $confirmed;
	public function initialize(){
        $this->setSource("registration_confirmation");
  }

}