<?php
namespace ImLike\Controllers;

use ImLike\Controllers\ControllerBase;
use ImLike\Api\ApiMethods;

use Phalcon\Http\Response;
use Phalcon\Escaper;

class HomeController extends ControllerBase{

	private static $newest = 1;
	private static $postLimit = 20;
	private static $offset = 0;

	public function beforeExecuteRoute($dispatcher){
		parent::beforeExecuteRoute($dispatcher);
		if(!$this->session->has("userId")){
			$response = new Response();
			$response->redirect("gate");
			$response->send();
		}
	}

	public function indexAction(){
		$userId = $this->view->loggedInUserId;
		$escaper = new Escaper();
		//$this->view->disable();
		$posts = ApiMethods::getFollowsPosts((int) $userId, self::$newest, self::$postLimit, self::$offset);
		$this->view->posts = $posts;
		$this->view->hasPosts = (count($posts) == 0) ? false : true;
	}
}

