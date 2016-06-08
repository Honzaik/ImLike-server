<?php
namespace ImLike\Controllers;

use ImLike\Models\Posts;
use ImLike\Models\Likes;
use ImLike\Models\Comments;
use ImLike\Api\ApiMethods;

class PostController extends ControllerBase{

	public function beforeExecuteRoute($dispatcher){
		$params = $dispatcher->getParams();
		if(count($params) == 0){
			die("no params");
		}
	}

	public function indexAction($postCode){
		$post = Posts::findFirstByPostCode($postCode);
		if($post){
			$postCode = $post->postCode;
      $likesCount = Likes::count("postCode = '$postCode'");
      $commentsCount = Comments::count("postCode = '$postCode'");
      
			$finalPost = new \stdClass();
			$finalPost->username = $this->escaper->escapeHtml($post->username);
			$finalPost->userId = $post->userId;
			$finalPost->postCode = $postCode;
      $finalPost->hasLiked = ApiMethods::hasLikedByUsername($this->view->loggedInUsername, $finalPost->postCode);
			$finalPost->imageUrl = $post->imageUrl;
			$finalPost->caption = $this->escaper->escapeHtml($post->caption);
      $finalPost->likesCount = $likesCount;
      $finalPost->commentsCount = $commentsCount;
			$finalPost->createdAt = ApiMethods::processCreatedAt($post->createdAt);
  		$this->view->post = $finalPost;

  		$comments = ApiMethods::getCommentsForPost($postCode, 10, 0);
  		$this->view->comments = $comments;

  		if($this->view->loggedInUsername === $finalPost->username) $this->view->showDelete = true;
  		else $this->view->showDelete = false;
  		
		}else{
			die("Post doesnt exists.");
		}
	}

}