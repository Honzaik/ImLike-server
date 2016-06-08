<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Posts extends Model{

	public $postId;
	public $postCode;
	public $userId;
	public $imageName;
	public $imageUrl;
	public $caption;
	public $createdAt;

	public function initialize(){
  	$this->skipAttributes(array('createdAt'));
	}
}