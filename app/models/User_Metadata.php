<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class User_Metadata extends Model{

	public $userId;
	public $username;
	public $profileImageUrl;
	public $userDesc;

	 public function initialize()
    {
        $this->setSource("user_metadata");
    }

}