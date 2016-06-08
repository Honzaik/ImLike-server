<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Notifications extends Model{

	public $notificationId;
	public $forUserId;
	public $fromUserId;
	public $notificationType;
	public $relatedTo;
	public $createdAt;
	public $checked;

	public function initialize(){
  	$this->skipAttributes(array('createdAt'));
	}
	
}