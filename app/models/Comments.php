<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Comments extends Model{

	public $commentId;
	public $userId;
	public $username;
	public $postCode;
	public $commentCode;
	public $content;
	public $createdAt;

	public function initialize(){
  	$this->skipAttributes(array('createdAt'));
	}
	
}