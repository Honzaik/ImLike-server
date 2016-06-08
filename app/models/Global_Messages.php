<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Global_Messages extends Model{

	public $messageId;
	public $content;
	public $createdAt;

	 public function initialize()
    {
        $this->setSource("global_messages");
        $this->skipAttributes(array('createdAt'));
    }
}