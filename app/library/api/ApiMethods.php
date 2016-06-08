<?php
namespace ImLike\Api;

use Aws\S3\S3Client;

use PHPMailer;

use ImLike\Models\Posts;
use ImLike\Models\Users;
use ImLike\Models\Likes;
use ImLike\Models\Authentication;
use ImLike\Models\Notifications;
use ImLike\Models\Following;
use ImLike\Models\Comments;
use ImLike\Models\Registration_Confirmation;
use ImLike\Models\User_Metadata;
use ImLike\Models\Global_Messages;
use \DateTime;

use ImLike\Api\Generator;

use ImLike\Localization;

use Phalcon\Escaper;

class ApiMethods{

	private static $limitMax = 50;
	private $controllerObject;
	private $language = "en";

	const DEFAULT_IMAGE_URL = 'http://imlike.in/profileImages/userDefault.jpg';

	public static function getUserPosts($userId, $newest, $limit, $offset){

		if(!(is_int($userId) && is_int($newest) && is_int($limit) && is_int($offset))) return array("error" => "Wrong parameters.");
		if($limit > self::$limitMax) $limit = self::$limitMax;

		$rawPosts = Posts::query()
			->where("userId = :userId:")
			->orderBy($newest ? "createdAt DESC" : "createdAt ASC")
			->limit($limit, $offset)
			->bind(array("userId" => $userId))
			->execute();

		if($rawPosts){
			$posts = array();
			if(count($rawPosts) > 0){
	  		for($i = 0; $i < count($rawPosts); $i++){
	  			$postCode = $rawPosts[$i]->postCode;
	  			$likesCount = Likes::count("postCode = '$postCode'");
	  			$commentsCount = Comments::count("postCode = '$postCode'");

	  			$post = new \stdClass();
	  			$post->username = $rawPosts[$i]->username;
	  			$post->userId = $rawPosts[$i]->userId;
	  			$post->postCode = $postCode;
	  			$post->imageUrl = $rawPosts[$i]->imageUrl;
	  			$post->caption = $rawPosts[$i]->caption;
	  			$post->likesCount = $likesCount;
	  			$post->commentsCount = $commentsCount;
	  			$post->hasLiked = self::hasLikedByUserId($userId, $postCode);
	  			$post->createdAt = self::processCreatedAt($rawPosts[$i]->createdAt);
	  			$post->timestamp = strtotime($rawPosts[$i]->createdAt);
	 				array_push($posts, $post);
	  		}
  		}else{
  			//return array("error" => "No post by a user with this id with this range.");
  			return $posts;
  		}
		}else{
			return $posts;
			//return array("error" => "Database error.");
		}

		return $posts;
	}

	public static function getFollowsPosts($userId, $newest, $limit, $offset){
		$posts = array();

		if(!(is_int($userId) && is_int($newest) && is_int($limit) && is_int($offset))) return array("error" => "Wrong parameters.");
		if($limit > self::$limitMax) $limit = self::$limitMax;

		$following = Following::find(array(
			"columns" 		=> "follows",
			"conditions" 	=> "follower = :userId:",
			"bind"				=> array("userId" => $userId)
		));
		$followList = array();
		array_push($followList, $userId); // show usermade posts as well
		if($following){
			if(count($following) > 0){
				foreach($following as $follow){
					array_push($followList, $follow->follows);
				}
			}else{
				return $posts;
			}
		}else{
			return $posts;
		}

		$rawPosts = Posts::query()
			->inWhere("userId", $followList)
			->orderBy($newest ? "createdAt DESC" : "createdAt ASC")
			->limit($limit, $offset)
			->execute();


		if($rawPosts){
			if(count($rawPosts) > 0){
			for($i = 0; $i < count($rawPosts); $i++){
  				$postCode = $rawPosts[$i]->postCode;
	  			$likesCount = Likes::count("postCode = '$postCode'");
	  			$commentsCount = Comments::count("postCode = '$postCode'");

	  			$post = new \stdClass();
	  			$post->username = $rawPosts[$i]->username;
	  			$post->userId = $rawPosts[$i]->userId;
	  			$post->postCode = $postCode;
	  			$post->imageUrl = $rawPosts[$i]->imageUrl;
	  			$post->caption = $rawPosts[$i]->caption;
	  			$post->likesCount = $likesCount;
	  			$post->commentsCount = $commentsCount;
	  			$post->hasLiked = self::hasLikedByUserId($userId, $postCode);
	  			$post->createdAt = self::processCreatedAt($rawPosts[$i]->createdAt);
	  			$post->timestamp = strtotime($rawPosts[$i]->createdAt);
	 				array_push($posts, $post);
	  		}
			}else{
				//return array("error" => "No records found.");
				return $posts;
			}
		}else{
			//return array("error" => "Database error.");
			return $posts;
		}

		return $posts;
	}

	public static function getPostInfo($postCode, $userId){
		$post = Posts::findFirstByPostCode($postCode);
		$posts = array();
		if($post){

			$postCode = $post->postCode;

			$likesCount = Likes::count("postCode = '$postCode'");
	  	$commentsCount = Comments::count("postCode = '$postCode'");

	  	$newPost = new \stdClass();
	  	$newPost->username = $post->username;
			$newPost->userId = $post->userId;
			$newPost->postCode = $postCode;
			$newPost->imageUrl = $post->imageUrl;
			$newPost->caption = $post->caption;
			$newPost->likesCount = $likesCount;
			$newPost->commentsCount = $commentsCount;
			$newPost->hasLiked = self::hasLikedByUserId($userId, $postCode);
			$newPost->createdAt = self::processCreatedAt($post->createdAt);
			$newPost->timestamp = strtotime($post->createdAt);
			array_push($posts, $newPost);
			return $posts;
		}else{
			return false;
		}
	}

	public static function userExists($userId){
		$user = Users::findFirstByUserId($userId);
		if($user) return true;
		else return false;
	}

	public static function usernameExists($username){
		$user = Users::findFirstByUsername($username);
		if($user) return true;
		else return false;
	}

	public static function usernameHasId($username, $userId){
		$user = Users::findFirstByUsername($username);
		if($user){
			if($user->userId === $userId) return true;
			else return false;
		}else return false;
	}


	public static function processCreatedAt($createdAt){
		$timestamp = strtotime($createdAt);
		$currentTimestamp = time();
		$gap = $currentTimestamp - $timestamp;

		$dtF = new DateTime("@0");
    $dtT = new DateTime("@$gap");
		if($gap > 60*60){ // over 1 hour
			if($gap > 60*60*24){ // over 1 day
				//$processed = date("j", $gap) . "d";
				$processed = $dtF->diff($dtT)->format('%ad');
			}else{
				//$processed = date("G", $gap) . "h";
				$processed = $dtF->diff($dtT)->format('%hh');
			}
		}else{ // less than hour ago
			//$processed = intval(date("i", $gap)) . "m";
			$processed = $dtF->diff($dtT)->format('%im');
		}
		return $processed;
	}

	public static function isFollowing($followerId, $followsId){
		if(self::userExists($followerId) && self::userExists($followsId)){
			$following = Following::findFirst(array(
				"conditions"	=> "follower = :followerId: AND follows = :followsId:",
				"bind"				=> array("followerId" => $followerId, "followsId" => $followsId)
			));
			if($following) return true;
			else return false;
		}else{
			return false;
		}
	}
	
	public static function follow($followerId, $followsId){
		$followerUsername = self::getUsernameFromUserId($followerId);
		$followsUsername = self::getUsernameFromUserId($followsId);
		$newFollow = new Following();
		$newFollow->follower = $followerId;
		$newFollow->followerUsername = $followerUsername;
		$newFollow->follows = $followsId;
		$newFollow->followsUsername = $followsUsername;
		return($newFollow->save());
	}

	public static function unfollow($followerId, $followsId){
		$following = Following::findFirst(array(
			"conditions"	=> "follower = :followerId: AND follows = :followsId:",
			"bind"				=> array("followerId" => $followerId, "followsId" => $followsId)
		));
		return($following->delete());
	}

	public static function uploadToS3($type, $filename, $filepath, $username){
		$imageUrl = "";

		$s3Client = S3Client::factory(array(
		    'key'    => 'AKIAJIZCEFFLSDZQ5CTQ',
		    'secret' => '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',
		));
		$result = $s3Client->listBuckets();

		$bucket = $result['Buckets'][0]['Name'];

		if($type == "post") $directory = "posts/";
		else if($type == "profileImage") $directory = "profileImages/";

		$result = $s3Client->putObject(array(
	    'Bucket'     => $bucket,
	    'Key'        => $directory.$filename,
	    'SourceFile' => $filepath,
	    'ACL'        => 'public-read',
	    'Metadata'   => array(
	        'User' => $username,
	    )
		));
		if(isset($result['ObjectURL'])) $imageUrl = $result['ObjectURL'];
		return $imageUrl;
	}

	public static function checkToken($userId, $apiToken){
		$auth = Authentication::findFirstByUserId($userId);
		if($auth && $auth->token == $apiToken){
			return true;
		}else{
			return false;
		}
	}

	public static function postExists($postCode){
		$post = Posts::findFirstByPostCode($postCode);
		if($post) return true;
		else return false;
	}

	public static function hasLikedByUsername($username, $postCode){
		$like = Likes::findFirst(array(
			"conditions"	=> "username = :username: AND postCode = :postCode:",
			"bind"				=> array("username" => $username, "postCode" => $postCode)
		));
		if($like) return true;
		else return false;
	}

	public static function hasLikedByUserId($userId, $postCode){
		$like = Likes::findFirst(array(
			"conditions"	=> "userId = :userId: AND postCode = :postCode:",
			"bind"				=> array("userId" => $userId, "postCode" => $postCode)
		));
		if($like) return true;
		else return false;
	}

	public static function like($userId, $username, $postCode){
		$like = new Likes();
		$like->userId = $userId;
		$like->username = $username;
		$like->postCode = $postCode;
		if($like->save()) return true;
		else return false;
	}

	public static function getUsernameFromUserId($userId){
		$user = Users::findFirstByUserId($userId);
		if($user) return $user->username;
		else return false;
	}

	public static function unlike($userId, $username, $postCode){
		$like = Likes::findFirst(array(
			"conditions"	=> "username = :username: AND postCode = :postCode:",
			"bind"				=> array("username" => $username, "postCode" => $postCode)
		));
		if($like->delete()) return true;
		else return false;
	}

	public static function isPostByUserId($postCode, $userId){
		$post = Posts::findFirst(array(
			"conditions"	=> "postCode = :postCode: AND userId = :userId:",
			"bind"				=> array("postCode" => $postCode, "userId" => $userId)
		));
		if($post) return true;
		else return false;
	}

	public static function getPostUserId($postCode){
		$post = Posts::findFirstByPostCode($postCode);
		if($post){
			return $post->userId;
		}else return false;
	}

	public static function deletePost($postCode){
		$post = Posts::findFirstByPostCode($postCode);

		if($post->delete() && self::deleteImage("post", $post->imageName)) return true;
		else return false;
	}

	public static function deleteImage($type, $imageName){

		$s3Client = S3Client::factory(array(
		    'key'    => 'AKIAJIZCEFFLSDZQ5CTQ',
		    'secret' => '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',
		));
		$result = $s3Client->listBuckets();

		$bucket = $result['Buckets'][0]['Name'];

		if($type == "post") $directory = "posts/";
		else if($type == "profileImage") $directory = "profileImages/";

		$result = $s3Client->deleteObject(array(
	    'Bucket' => $bucket,
	    'Key'    => $directory . $imageName,
		));
		return true;
	}

	public static function createNotification($forUserId, $fromUserId, $notificationType, $relatedTo){
		// 0 = like, 1 = new follow, 2 = comment
		$notification = Notifications::findFirst(array(
			"conditions"	=> "forUserId = :forUserId: AND fromUserId = :fromUserId: AND notificationType = :notificationType: AND relatedTo = :relatedTo:",
			"bind"				=> array("forUserId" => $forUserId, "fromUserId" => $fromUserId, "notificationType" => $notificationType, "relatedTo" => $relatedTo),
		));
		if(!$notification || $notification->notificationType == 2){
			$newNotification = new Notifications();
			$newNotification->forUserId = $forUserId;
			$newNotification->fromUserId = $fromUserId;
			$newNotification->notificationType = $notificationType;
			$newNotification->relatedTo = $relatedTo;
			$newNotification->checked = 0;  // 0 = not checked yet | 1 = yet checked
			if($newNotification->save()) return true;
			else return false;
		}else{
			return false;
		}
	}

	public static function checkNotification($notificationId, $userId){
		$notification = Notifications::findFirstByNotificationId($notificationId);

		if($notification && $notification->forUserId == $userId){
			$notification->checked = 1; // 0 = not checked yet | 1 = yet checked
			if($notification->save()) return true;
			else return false;
		}else{
			return false;
		}
	}

	public static function hasNewNotifications($userId){
		$notification = Notifications::findFirst(array(
			"conditions"	=> "forUserId = :forUserId: AND checked = 0",
			"bind"				=> array("forUserId" => $forUserId),
		));
		if($notification) return true;
		else return false;
	}

	public static function getNotificationsForUserId($forUserId){
		$notifications = Notifications::query()
			->where("forUserId = :forUserId:")
			->andWhere("checked = 0")
			->orderBy("createdAt DESC")
			->limit(20, 0)
			->bind(array("forUserId" => $forUserId))
			->execute();
		$notificationsArray = array();
		if(count($notifications) > 0){
			foreach($notifications as $rawNotification){
				$notification = new \stdClass();
				$notification->notificationId = $rawNotification->notificationId;
				$notification->forUserId = $rawNotification->forUserId;
				$notification->fromUserId = $rawNotification->fromUserId;
				$notification->fromUsername = self::getUsernameFromUserId($notification->fromUserId);
				$notification->relatedTo = $rawNotification->relatedTo;
				$notification->notificationType = $rawNotification->notificationType;
				$notification->checked = $rawNotification->checked;
				$notification->createdAt = self::processCreatedAt($rawNotification->createdAt);
				$notification->timestamp = strtotime($rawNotification->createdAt);
				array_push($notificationsArray, $notification);
			}
		}
		return $notificationsArray;
	}

	public static function commentExists($commentCode){
		$comment = Comments::findFirstByCommentCode($commentCode);
		if($comment) return true;
		else return false;
	}

	public static function getUniqueCommentCode(){
		return (self::commentExists($commentCode = uniqid())) ? self::getUniqueCommentCode() : $commentCode;
	}

	public static function postComment($fromUserId, $fromUsername, $postCode, $content){
		$comment = new Comments();
		$comment->userId = $fromUserId;
		$comment->username = $fromUsername;
		$comment->postCode = $postCode;
		$comment->commentCode = self::getUniqueCommentCode();
		$comment->content = $content;
		if($comment->save()) return true;
		else return false;
	}

	public static function deleteComment($commentCode){
		$comment = Comments::findFirstByCommentCode($commentCode);
		if($comment && $comment->delete()){
			return true;
		}else{
			return false;
		}
	}

	public static function getCommentsForPost($postCode, $limit, $offset){
		$commentsRaw = Comments::query()
			->where("postCode = :postCode:")
			->orderBy("createdAt DESC")
			->limit($limit, $offset)
			->bind(array("postCode" => $postCode))
			->execute();

		$comments = array();

		if(count($commentsRaw) > 0){
			foreach($commentsRaw as $commentRaw){
				$comment = new \stdClass();
				$comment->userId = $commentRaw->userId;
				$comment->username = $commentRaw->username;
				$comment->postCode = $commentRaw->postCode;
				$comment->commentCode = $commentRaw->commentCode;
				$comment->content = $commentRaw->content;
				$comment->timestamp = strtotime($commentRaw->createdAt);
				$comment->createdAt = self::processCreatedAt($commentRaw->createdAt);
				array_push($comments, $comment);
			}
		}

		return $comments;
	}

	public static function getNumberOfRegisteredUsers(){
		return Users::count();
	}

	public static function getNumberOfImLikes(){
		return Posts::count();
	}

	public static function sendConfirmationEmail($email){

		$user = Users::findFirstByEmail($email);

		if(!$user) die("NO USER");

		$token = Generator::generateEmailConfirmationToken();
		$confirmation = new Registration_Confirmation();
    $confirmation->userId = $user->userId;
    $confirmation->token = $token;
    $confirmation->confirmed = 0;
    if($confirmation->save()){	
			$mail = new PHPMailer;

	    $mail->IsSMTP();
	    $mail->SMTPAuth = true;
	    $mail->SMTPSecure = "ssl";
	    $mail->Host = "smtp.gmail.com";
	    $mail->Username = "imlikeapp@gmail.com";
	    $mail->Password = "xdrpcxszgesdfwhc";
	    $mail->Port = "465";

	    $mail->From = 'imlikeapp@gmail.com';
	    $mail->FromName = 'ImLike.in';
	    $mail->addAddress($email, $email);  // Add a recipient
	    $mail->addReplyTo('imlikeapp@gmail.com', 'ImLikeIn');

	    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	    $mail->isHTML(true);                                  // Set email format to HTML

	    $mail->Subject = 'ImLike registration';
	    $mail->Body    = 'You have been successfully registered at ImLike.in<br>Please confirm the registration by clicking on this link. <a href="https://imlike.in/api/confirmRegistration/'. $token .'">https://imlike.in/confirmRegistration/'. $token . '</a>';
	    $mail->AltBody = 'You have been successfully registered at ImLike.in Please confirm the registration by clicking on this link https://imlike.in/confirmRegistration/'. $token;

	    if(!$mail->send()) {
	      return false;
	    }else{
	    	return true;
	    }
	    

    }else{
    	foreach ($confirmation->getMessages() as $message) {
        echo "Message: ", $message->getMessage();
        echo " Field: ", $message->getField();
        echo " Type: ", $message->getType();
    	}
    }
	}

	public static function addMetadataToUser($userId, $username){
		$metadata = new User_Metadata();
		$metadata->userId = $userId;
		$metadata->username = $username;
		$metadata->profileImageUrl = self::DEFAULT_IMAGE_URL;
		$metadata->userDesc = new \Phalcon\Db\RawValue('""');
		if($metadata->save()){
		}else{
			foreach ($metadata->getMessages() as $message) {
		        echo "Message: ", $message->getMessage();
		        echo " Field: ", $message->getField();
		        echo " Type: ", $message->getType();
	    	}
		}
	}

	public static function getFollowerList($userId, $offset){
		$followers = Following::query()
			->where("follows = :userId:")
			->limit(20, $offset)
			->bind(array("userId" => $userId))
			->execute();

		$followersArray = array();

		if(count($followers) > 0){
			foreach($followers as $followerRaw){
				$follower = new \stdClass();
				$follower->userId = $followerRaw->follower;
				$follower->username = $followerRaw->followerUsername;
				array_push($followersArray, $follower);
			}
		}
		return $followersArray;
	}

	public static function getFollowsList($userId, $offset){
		$follows = Following::query()
			->where("follower = :userId:")
			->limit(20, $offset)
			->bind(array("userId" => $userId))
			->execute();

		$followsArray = array();

		if(count($follows) > 0){
			foreach($follows as $followRaw){
				$follow = new \stdClass();
				$follow->userId = $followRaw->follows;
				$follow->username = $followRaw->followsUsername;
				array_push($followsArray, $follow);
			}
		}
		return $followsArray;
	}

	public static function isCommentByUserId($commentCode, $userId){
		$comment = Comments::findFirst(array(
			"conditions"	=> "commentCode = :commentCode: AND userId = :userId:",
			"bind"				=> array("commentCode" => $commentCode, "userId" => $userId)
		));
		if($comment) return true;
		else return false;
	}

	public static function getGlobalMessage(){
		$rawMessage = Global_Messages::findFirst(array(
		    "order" => "createdAt DESC",
		    "limit" => 1
		));
		$message = new \stdClass();
		if($rawMessage){
			$message->messageId = $rawMessage->messageId;
			$message->content = $rawMessage->content;
			$message->createdAt = $rawMessage->createdAt;
			return $message;
		}else return false;
	}

	public static function isAdmin($userId){
		$adminIds = array("1");
		if(in_array($userId, $adminIds)) return true;
		else return false;
	}

	public static function broadcastMessage($message){
		$globalMessage = new Global_Messages();
		$globalMessage->content = $message;
		if($globalMessage->save()) return true;
		else return false;
	}
}