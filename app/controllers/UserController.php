<?php
namespace ImLike\Controllers;

use ImLike\Models\Posts;
use ImLike\Models\Likes;
use ImLike\Models\Following;
use ImLike\Models\User_Metadata;
use ImLike\Models\Comments;
use ImLike\Api\ApiMethods;

use \stdClass;

use Phalcon\Escaper;

class UserController extends ControllerBase{

	public function beforeExecuteRoute($dispatcher){
		$params = $dispatcher->getParams();
		if(count($params) == 0){
			die("no params");
		}
	}

	public function indexAction($username){
		$this->escaper = new Escaper();
		if(trim($username) == ""){
			die("No argument");
		}

    if(!ApiMethods::usernameExists($username)) die("User doesnt exist"); // replace for custom view;

    $userMetadata = User_Metadata::findFirstByUsername($username);
    if(!$userMetadata) die("No data about user in database"); 
		$postObjects = array();
		$userId = $userMetadata->userId;
		$posts = Posts::query()->where("username = :username:")->bind(array("username" => $username))->orderBy("createdAt DESC")->execute();
		foreach ($posts as $post) {
      $postCode = $post->postCode;
      $likesCount = Likes::count("postCode = '$postCode'");
      $commentsCount = Comments::count("postCode = '$postCode'");

			$finalPost = new stdClass();
			$finalPost->username = $this->escaper->escapeHtml($post->username);
      $finalPost->userId = $post->userId;
			$finalPost->postCode = $postCode;
      $finalPost->hasLiked = ApiMethods::hasLikedByUsername($this->view->loggedInUsername, $finalPost->postCode);
			$finalPost->imageUrl = $post->imageUrl;
			$finalPost->caption = $this->escaper->escapeHtml($post->caption);
      $finalPost->likesCount = $likesCount;
      $finalPost->commentsCount = $commentsCount;
			$finalPost->createdAt = ApiMethods::processCreatedAt($post->createdAt);
			array_push($postObjects, $finalPost);
		}
  	$followingCount = Following::count(array("conditions" => "follower = $userId"));
  	$followersCount = Following::count(array("conditions" => "follows = $userId"));
    
    $isFollowing = ApiMethods::isFollowing($this->view->loggedInUserId, $userId);
    $this->view->isFollowing = $isFollowing;
  	$this->view->followingCount = $followingCount;
  	$this->view->followersCount = $followersCount;
  	$this->view->userId = $userId;
  	$this->view->postsCount = count($posts);
  	$this->view->profileUsername = $username;
    $this->view->profileImageUrl = $userMetadata->profileImageUrl;
    $this->view->userDesc = $this->escaper->escapeHtml($userMetadata->userDesc);
  	$this->view->posts = $postObjects;
  	$this->view->hasPosts = (count($postObjects) == 0) ? false : true;
	}

}