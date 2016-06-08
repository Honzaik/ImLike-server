<?php
namespace ImLike\Models;

use Phalcon\Mvc\Model;

class Following extends Model{

	public $follower;
	public $followerUsername;
	public $follows;
}