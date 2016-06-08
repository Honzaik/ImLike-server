<?php
namespace ImLike\Controllers;

use Aws\S3\S3Client;
use ImLike\Controllers\ControllerBase;
use ImLike\Forms\LoginForm;
use ImLike\Forms\RegisterForm;
use ImLike\Models\Users;
use ImLike\Models\Authentication;
use ImLike\Models\Posts;
use ImLike\Models\Following;
use ImLike\Models\User_Metadata;
use ImLike\Models\Registration_Confirmation;
use ImLike\Localization;
use ImLike\Images\ImageFormatter;
use ImLike\Images\NotJpegException;
use ImLike\Api\Generator;
use ImLike\Api\ApiMethods;

use Phalcon\Http\Response;
use Phalcon\Filter;
use Phalcon\Escaper;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;

use PHPMailer;

class ApiController extends ControllerBase{

	private $output = array();
	private $language = "en";
	private $filter;
	private $escaper;
	private $response;
	public static $postUrlRoot = "http://imlike.in/p/";

	public function initialize(){
		$this->view->disable();
		$this->filter = new Filter();
		$this->escaper = new Escaper();
		$this->response = new Response();
		$this->output["errors"] = new \stdClass();
		$this->output["response"] = new \stdClass();
	}

	private function printResponse(){
		$this->response->setContentType('application/json', 'utf-8');
		$this->response->setJsonContent($this->output, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		return $this->response->send();
	}

	public function getUsersAuthTokenAndAesKey($userId, $username, $isApp){
		$authentication = Authentication::findFirstByUserId($userId);
		$token = Localization::getText($this->language, __METHOD__ . "-tokenError"); // later replace en with languge code in $_SESSION
		if($authentication){ // user is already in the auth database, give him the token
			$token = $authentication->token;
			$aesKey = $authentication->aesKey;
		}else{ // user is not in the auth database, generate new token and tie it to his account
			$token = Generator::generateAuthToken();
			$aesKey = Generator::generateAESKey();
			$authentication = new Authentication();
			$authentication->userId = $userId;
			$authentication->username = $username;
			$authentication->token = $token;
			$authentication->aesKey = $aesKey;
			$authentication->save();
		}
		$this->output["response"]->token = $token;
		return array($token, $aesKey);
	}

	public function loginAction(){
		if($this->request->isPost()){
			$error = true; // set error as default
			$loginForm = new LoginForm();
			if($loginForm->isValid($this->request->getPost())){ // form structure is ok
				$email = $this->request->getPost("login-email");
				$password = $this->request->getPost("login-password");
				$isApp = $this->request->getPost("isApp");
				$user = Users::findFirstByEmail($email);
				if($user){ // user exists
					if($this->security->checkHash($password, $user->password)) { // passwords match, try to authenticate user
						$token = $this->getUsersAuthTokenAndAesKey($user->userId, $user->username, $isApp)[0];
						$this->session->set("userId", $user->userId);
						$this->session->set("username", $user->username);
						$this->session->set("apiToken", $token);
						$username = $this->escaper->escapeHtml($user->username);
						$this->output["response"]->userId = $user->userId;
						$this->output["response"]->username = $username;
						$this->output["response"]->loggedIn = true;
						return $this->printResponse();
						$error = false;
					}else{ // passwords don't match
						$error = true;
					}
				}else{ // user doesn't exist
					$error = true;
				}
			}else{ // form validation failed
				foreach ($loginForm->getMessages() as $message) {
					$errorName = $message->getField();
			    $this->output["errors"]->$errorName = $message->getMessage();
				}
			}
			if($error){
				$this->output["response"]->loggedIn = false;
				return $this->printResponse();
			}
		}else{ // not a post request
			die();
		}
	}

	public function logoutAction(){
		$previousUrl = $this->request->getHeader("HTTP_REFERER");
		$this->session->destroy();
		if($previousUrl == "") $this->response->redirect("http://imlike.in/");
		else $this->response->redirect($previousUrl);
		return $this->response->send();
	}

	public function registerAction(){
		if($this->request->isPost()){
			$registerForm = new RegisterForm();
			if ($registerForm->isValid($this->request->getPost())){
				$email = $this->escaper->escapeHtml($this->request->getPost("register-email"));
				$username = $this->escaper->escapeHtml($this->request->getPost("register-username"));
				$password = $this->request->getPost("register-password");
				$userWithSameEmail = Users::findFirstByEmail($email);
				$userWithSameUsername = Users::findFirstByUsername($username);
				$error = false;
				if($userWithSameEmail){
					$errorName = "register-email";
					$this->output["errors"]->$errorName = Localization::getText($this->language, __METHOD__ . "-duplicateEmail");
					$error = true;
				}
				if($userWithSameUsername){
					$errorName = "register-username";
					$this->output["errors"]->$errorName = Localization::getText($this->language, __METHOD__ . "-duplicateUsername");
					$error = true;
				}
				if(!$error){
					$user = new Users();
					$user->email = $email;
					$user->username = $username;
					$user->password = $this->security->hash($password);
					if($user->save()){
            if(ApiMethods::sendConfirmationEmail($email)){
              $this->output["response"]->registerSuccess = true; //Localization::getText($this->language, __METHOD__ . "-registerSuccess");
              echo (String) ApiMethods::addMetadataToUser($user->userId, $user->username);
            }else{
              $user->delete();
              $this->output["response"]->registerSuccess = false;
            }
          }
					else $this->output["response"]->registerSuccess = false; // "Something went wrong, try again."; //Localization::getText($this->language, __METHOD__ . "-registerFail");
				}else{
					$this->output["response"]->registerSuccess = false;
				}

			}
			foreach ($registerForm->getMessages() as $message) {
				$errorName = $message->getField();
			  $this->output["errors"]->$errorName = $message->getMessage();
			}
			$this->printResponse();
		}else{
			die();
		}
	}

  public function confirmRegistrationAction($token){
    $confirmation = Registration_Confirmation::findFirstByToken($token);
    if($confirmation){
      if($confirmation->confirmed == 0){
        $confirmation->confirmed = 1;
        if($confirmation->save()){
          echo "Successfully confirmed.";
        }else{
          echo "Something went wrong.";
        }
      }else{
        echo "This account has been already confirmed.";
      }
    }else{
      echo "Something went wrong.";
    }
  }

	public function getCsrfAction(){
		$csrf = "";
		if($this->security->getSessionToken() == "") $csrf = $this->security->getToken();
		else $csrf = $this->security->getSessionToken();
		$this->output["response"]->csrfToken = $csrf;
		$this->printResponse();
	}

	public function testAction(){
    $mail = new PHPMailer;

    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host = "smtp.gmail.com";
    $mail->Username = "oupicky2@gmail.com";
    $mail->Password = "password";
    $mail->Port = "465";

    $mail->From = 'oupicky2@gmail.com';
    $mail->FromName = 'Honzaik';
    $mail->addAddress('honzaik@seznam.cz', 'honzaik');  // Add a recipient
    $mail->addReplyTo('oupicky2@gmail.com', 'derp');

    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if(!$mail->send()) {
       echo 'Message could not be sent.';
       echo 'Mailer Error: ' . $mail->ErrorInfo;
       exit;
    }

    echo 'Message has been sent';
	}

  public function indexAction(){
  	$this->view->enable();
  	/*
  	$s3Client = S3Client::factory(array(
	    'key'    => 'AKIAJ4TK5OFNJYO6T2MQ',
	    'secret' => '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',
	));
	$result = $s3Client->listBuckets();

	$bucket = $result['Buckets'][0]['Name'];
	$iterator = $s3Client->getIterator('ListObjects', array(
	    'Bucket' => $bucket
	));

	foreach ($iterator as $object) {
	    $key = $object['Key'];
	    $plainUrl = $s3Client->getObjectUrl($bucket, $key);
	    echo $plainUrl . "<br>";
	};
	*/

  }

  public function uploadPostAction(){
  	$this->view->disable();
  	if($this->request->isPost()){
  		$requestUsername = $this->request->getPost("username");
  		$requestUserId = $this->request->getPost("userId");
  		$requestToken = $this->request->getPost("apiToken");
  		$requestCaption = ($this->request->getPost("caption") != null) ? $this->request->getPost("caption") : new \Phalcon\Db\RawValue('""');
  		$user = Authentication::findFirstByUserId($requestUserId);
  		$error = true;
  		if($user && $user->username === $requestUsername && $user->token === $requestToken){ // everything ok
  			if(isset($_FILES["image"]["name"])){
  				$filepath = $_FILES["image"]["tmp_name"];
  				try{
  					$imageFormatter = new ImageFormatter($filepath); // CHECK FOR UPLOAD SIZE
  					$filepath = $imageFormatter->getImage("post");
  					$post = true;
  					while($post){
  						$newImageFileName = uniqid() . ".jpg";
  						$post = Posts::findFirstByImageName($newImageFileName);
  					}

  					$imageUrl = ApiMethods::uploadToS3("post", $newImageFileName, $filepath, $requestUsername);
  					if($imageUrl != ""){
  						$newPost = new Posts();
  						$newPost->postCode = substr($newImageFileName, 0, -4);
  						$newPost->userId = $requestUserId;
  						$newPost->username = $requestUsername;
  						$newPost->imageName = $newImageFileName;
  						$newPost->imageUrl = $imageUrl;
  						$newPost->caption = $requestCaption;
  						if($newPost->save()){
  							$error = false;
  							$this->output["response"]->postUrl = self::$postUrlRoot . $newPost->postCode;
  						}else{
  							$error = true;
  							$this->output["errors"]->message = "Database error.";
  						}
  					}else{
  						$this->output["errors"]->message = "Upload to S3 error.";
  					}

  					/*
  					if(move_uploaded_file ($filepath, $this->registry->imagesDir . $newImageFileName)){ // replace for S3 upload;
  						$newPost = new Posts();
  						$newPost->postCode = substr($newImageFileName, 0, -4);
  						$newPost->userId = $requestUserId;
  						$newPost->username = $requestUsername;
  						$newPost->imageName = $newImageFileName;
  						$newPost->imageUrl = 'http://10.0.0.1/images/' . $newImageFileName;
  						$newPost->caption = $requestCaption;
  						if($newPost->save()){
  							$error = false;
  							$this->output["response"]->postUrl = self::$postUrlRoot . $newPost->postCode;
  						}else{
  							$error = true;
  							$this->output["errors"]->message = "Database error.";
  						}
  					}else{
  						$error = true;
  						$this->output["errors"]->message = "Internal error.";
  					} */
  				} catch(NotJpegException $e){
  					$this->output["errors"]->message ="Not a jpeg image.";
  				}
  			}else{
  				$this->output["errors"]->message = "No image to upload.";
  			}
  		}else{
  			$this->output["errors"]->message = "Wrong credentials";
  		}
  	}else{
  		return $this->dispatcher->forward(array(
            "controller" => "api",
            "action" => "index"
      ));
  	}

  	if($error) $this->output["response"]->success = false;
  	else $this->output["response"]->success = true;
  	$this->printResponse();
  }

  public function userPostsAction(){

  	if(!$this->request->hasQuery("userId")){
  		$this->output["errors"]->message = "No user id supplied.";
  		return $this->printResponse();
  	}else{
  		$userId = (int) $this->request->getQuery("userId", "int", -1);
	  	$newest = (int) $this->request->getQuery("newest", "int", 1);
	  	$limit = (int) $this->request->getQuery("limit", "int", 10);
	  	$offset = (int) $this->request->getQuery("offset", "int", 0);

	  	$returned = ApiMethods::getUserPosts($userId, $newest, $limit, $offset);
	  	if(array_key_exists("error", $returned)){
	  		$this->output["errors"] = $returned;
	  	}else{
	  		$this->output["response"]->posts = $returned;
	  	}
	  	return $this->printResponse();
  	}
  }

  public function followsPostsAction(){
  	if(!$this->request->hasQuery("userId")){
  		$this->output["errors"]->message = "No user id supplied.";
  		return $this->printResponse();
  	}else{
  		$userId = (int) $this->request->getQuery("userId", "int", -1);
	  	$newest = (int) $this->request->getQuery("newest", "int", 1);
	  	$limit = (int) $this->request->getQuery("limit", "int", 10);
	  	$offset = (int) $this->request->getQuery("offset", "int", 0);

	  	$returned = ApiMethods::getFollowsPosts($userId, $newest, $limit, $offset);
	  	if(array_key_exists("error", $returned)){
	  		$this->output["errors"] = $returned;
	  	}else{
	  		$this->output["response"]->posts = $returned;
	  	}
	  	$this->printResponse();
  	}
  }

  public function followAction(){
  	if($this->request->isPost()){
      $toFollow = $this->request->getPost("toFollow", "int", -1);
  		$userId = $this->request->getPost("userId", "int", -1);
  		$token = $this->request->getPost("apiToken");

  		if(ApiMethods::userExists($toFollow) && ApiMethods::userExists($userId)){
  			$usersExist = true;
  			$this->output["response"]->usersExist = true;
  		}else{
  			$usersExist = false;
  			$this->output["response"]->usersExist = false;
  		}
  		$auth = Authentication::findFirstByUserId($userId);
  		if($usersExist && $auth){
  			if($auth->token === $token){ // user authenticated
  				if(ApiMethods::isFollowing($auth->userId, $toFollow)){ //is following

  					$this->output["response"]->alreadyFollowing = true;

  				}else{ // not follwing add follow

  					$this->output["response"]->alreadyFollowing = false;

  					if(ApiMethods::follow($auth->userId, $toFollow)){
              $this->output["response"]->success = true;
              ApiMethods::createNotification($toFollow, $auth->userId, 1, "FOLLOWING");
  					}else $this->output["response"]->success = false;

  				}
  			}else{
  				$this->output["errors"] = "Authentication failed";
  				$this->output["response"]->success = false;
  			}
  		}else{
  			$this->output["errors"] = "one of the users doesnt exist";
  			$this->output["response"]->success = false;
  		}
  	}else{
  		die("no post or wrong param");
  	}
  	$this->printResponse();
  }

  public function unfollowAction(){
  if($this->request->isPost()){
      $toUnfollow = $this->request->getPost("toUnfollow", "int", -1);
  		$userId = $this->request->getPost("userId", "int", -1);
  		$token = $this->request->getPost("apiToken");

  		if(ApiMethods::userExists($toUnfollow) && ApiMethods::userExists($userId)){
  			$usersExist = true;
  			$this->output["response"]->usersExist = true;
  		}else{
  			$usersExist = false;
  			$this->output["response"]->usersExist = false;
  		}
  		$auth = Authentication::findFirstByUserId($userId);
  		if($usersExist && $auth){
  			if($auth->token === $token){ // user authenticated

  				$following = Following::findFirst(array(
  					"conditions"	=> "follower = :userId: AND follows = :toFollow:",
  					"bind"				=> array("userId" => $auth->userId, "toFollow" => $toUnfollow)
  				));

  				if($following){ // already record in db - unfollow

  					$this->output["response"]->alreadyFollowing = true;

  					if($following->delete()) $this->output["response"]->success = true;
  					else $this->output["response"]->success = false;

  				}else{ // not following - nothing to unfollow

  					$this->output["response"]->alreadyFollowing = false;

  				}
  			}else{
  				$this->output["errors"] = "Authentication failed";
  				$this->output["response"]->success = false;
  			}
  		}else{
  			$this->output["errors"] = "one of the users doesnt exist";
  			$this->output["response"]->success = false;
  		}
  	}else{
  		die("no post or wrong param");
  	}
  	$this->printResponse();
  }

  public function userInfoAction(){
    if(!$this->request->hasQuery("userId") && !$this->request->hasQuery("username")){
      $this->output["errors"]  = "No user id or username supplied.";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    if($this->request->hasQuery("username")){
      $userData = User_Metadata::findFirstByUsername($this->request->getQuery("username", "string", ""));
    }else{
      $userData = User_Metadata::findFirstByUserId((int) $this->request->getQuery("userId", "int", -1));
    }
    if(!$userData){
      $this->output["response"]->userExists = false;
      $this->output["response"]->hasData = false;
      $this->output["response"]->success = false;
      return $this->printResponse();
    }
    if($this->request->hasQuery("userId") && $this->request->hasQuery("requestUserId")){
      $requestUserId = $this->request->getQuery("requestUserId", "int", "-1");
      $this->output["response"]->youFollowing = ApiMethods::isFollowing($requestUserId, $this->request->getQuery("userId", "int", -1));
    }

    $followingCount = Following::count(array("conditions" => "follower = $userData->userId"));
    $followersCount = Following::count(array("conditions" => "follows = $userData->userId"));

    $this->output["response"]->userExists = true;
    $this->output["response"]->hasData = true;
    $this->output["response"]->userId = $userData->userId;
    $this->output["response"]->username = $userData->username;
    $this->output["response"]->profileImageUrl = $userData->profileImageUrl;
    $this->output["response"]->userDesc = $userData->userDesc;
    $this->output["response"]->followingCount = $followingCount;
    $this->output["response"]->followerCount = $followersCount;
    return $this->printResponse();
  }

  public function editProfileAction(){
    if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $apiToken = $this->request->getPost("apiToken");
      $userDesc = ($this->request->getPost("userDesc") !== null) ? $this->request->getPost("userDesc") : new \Phalcon\Db\RawValue('""');
      if(mb_strlen($userDesc) > 140){
        $this->output["errors"] = "Description cannot be longer than 140 characters.";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }
      if(ApiMethods::checkToken($userId, $apiToken)){
        $userMetadata = User_Metadata::findFirstByUserId($userId);
        if($userMetadata){
          $userMetadata->userDesc = $userDesc;
          if($userMetadata->save()){
            $this->output["response"]->success = true;
          }else{
            $this->output["errors"] = "Saving info to database failed";
            $this->output["response"]->success = false;
          }
        }else{
          $this->output["errors"] = "User info not found in database";
          $this->output["response"]->success = false;
        }
      }else{
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
      }
    }else{
      die("no post");
    }
    $this->printResponse();
  }

  public function uploadProfileImageAction(){
    if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $apiToken = $this->request->getPost("apiToken");
      $userDesc = ($this->request->getPost("userDesc") !== null) ? $this->request->getPost("userDesc") : new \Phalcon\Db\RawValue('""');
      $userMetadata = User_Metadata::findFirstByUserId($userId);
      if(!$userMetadata){
        $this->output["errors"] = "User info not found in database";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }
      $username = $userMetadata->username;
      if($userDesc !== null && mb_strlen($userDesc) > 140){
        $this->output["errors"] = "Description cannot be longer than 140 characters.";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }
      if(isset($_FILES["profileImage"]["name"])){
        $filepath = $_FILES["profileImage"]["tmp_name"];
        try{
          $imageFormatter = new ImageFormatter($filepath); // CHECK FOR UPLOAD SIZE
          $filepath = $imageFormatter->getImage("profileImage");
          $newImageFileName = $username . $userId . "_profileImage.jpg";


          $imageUrl = ApiMethods::uploadToS3("profileImage", $newImageFileName, $filepath, $username);
          if($imageUrl != ""){
           if($userDesc !== null){
              $userMetadata->userDesc = $userDesc;
            }
            $userMetadata->profileImageUrl = $imageUrl;
            if($userMetadata->save()) $this->output["response"]->success = true;
          }else{
            $this->output["errors"]->message = "Upload to S3 error.";
            $this->output["response"]->success = false;
          }

        } catch(NotJpegException $e){
          $this->output["errors"] ="Not a jpeg image.";
          $this->output["response"]->success = false;
        }
      }else{
        $this->output["errors"] ="No image supplied.";
        $this->output["response"]->success = false;
      }
      $this->printResponse();
    }else{
      die("no post");
    }
  }

  public function likeAction(){
     if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $username = $this->request->getPost("username");
      $apiToken = $this->request->getPost("apiToken");
      $postCode = $this->escaper->escapeHtml($this->request->getPost("postCode"));
      $this->output["response"]->postCode = $postCode;

      if(!$this->output["response"]->postExists = ApiMethods::postExists($postCode)){
        $this->output["errors"] = "Post doesn't exist";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::usernameHasId($username, $userId)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if($this->output["response"]->hasLiked = ApiMethods::hasLikedByUsername($username, $postCode)){
        $this->output["errors"] = "You have already liked this post.";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(ApiMethods::like($userId, $username, $postCode)){
        if(!ApiMethods::isPostByUserId($postCode, $userId)){
          ApiMethods::createNotification(ApiMethods::getPostUserId($postCode), $userId, 0, $postCode);
        }
        $this->output["response"]->success = true;
      }else{
        $this->output["errors"] = "Something went wrong, try again";
        $this->output["response"]->success = false;
      }
      return $this->printResponse();
    }else{

    }
  }

  public function unlikeAction(){
     if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $username = $this->request->getPost("username");
      $apiToken = $this->request->getPost("apiToken");
      $postCode = $this->escaper->escapeHtml($this->request->getPost("postCode"));
      $this->output["response"]->postCode = $postCode;

      if(!$this->output["response"]->postExists = ApiMethods::postExists($postCode)){
        $this->output["errors"] = "Post doesn't exist";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!$this->output["response"]->hasLiked = ApiMethods::hasLikedByUsername($username, $postCode)){
        $this->output["errors"] = "You have not liked this post.";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(ApiMethods::unlike($userId, $username, $postCode)){
        $this->output["response"]->success = true;
      }else{
        $this->output["errors"] = "Something went wrong, try again";
        $this->output["response"]->success = false;
      }
      return $this->printResponse();
    }else{

    }
  }

  public function deletePostAction(){
   if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $postCode = $this->escaper->escapeHtml($this->request->getPost("postCode"));
      $apiToken = $this->request->getPost("apiToken");
      $this->output["response"]->postCode = $postCode;
      if(!$this->output["response"]->postExists = ApiMethods::postExists($postCode)){
        $this->output["errors"] = "Post doesn't exist";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::isPostByUserId($postCode, $userId)){
        $this->output["errors"] = "The post is not by this user.";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(ApiMethods::deletePost($postCode)){
        $this->output["response"]->success = true;
      }else{
        $this->output["errors"] = "Something went wrong, try again";
        $this->output["response"]->success = false;
      }
      return $this->printResponse();
    }
  }

  public function hasNewNotificationsAction(){
   if($this->request->isPost()){
    $userId = $this->request->getPost("userId");
    $apiToken = $this->request->getPost("apiToken");

    if(!ApiMethods::checkToken($userId, $apiToken)){
      $this->output["errors"] = "User authentication failed";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    $hasNotifications = ApiMethods::hasNewNotifications($userId);
    $this->output["response"]->success = $hasNotifications;
    return $this->printResponse();
   }
  }

  public function getNotificationsAction(){
    if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $apiToken = $this->request->getPost("apiToken");

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      $notifications = ApiMethods::getNotificationsForUserId($userId);
      $this->output["response"]->notifications = $notifications;
      return $this->printResponse();
    }
  }

  public function checkNotificationsAction(){
    if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $apiToken = $this->request->getPost("apiToken");
      $notificationIds = $this->request->getPost("notificationIds");

      $notificationIdsArray = explode(";", $notificationIds);
      array_pop($notificationIdsArray);

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(count($notificationIdsArray) > 0){
        foreach($notificationIdsArray as $notificationId){
          if(ApiMethods::checkNotification($notificationId, $userId)) $this->output["response"]->success = true;
          else $this->output["response"]->success = false;
        }
      }

      return $this->printResponse();
    }
  }

  public function postCommentAction(){
     if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $apiToken = $this->request->getPost("apiToken");
      $username = $this->request->getPost("username");
      $postCode = $this->request->getPost("postCode");
      $content = $this->request->getPost("content");

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::usernameHasId($username, $userId)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::postExists($postCode)){
        $this->output["errors"] = "Post doesn't exist.";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::usernameHasId($username, $userId)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(mb_strlen($content) > 255){
        $this->output["errors"] = "Comment too long. Max 255 chars";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(mb_strlen($content) <= 0){
        $this->output["errors"] = "No comment attached.";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      $content = trim(preg_replace("/\n{2,}/", "\n", $content), "\r\n");

      if(ApiMethods::postComment($userId, $username, $postCode, $content)){
        ApiMethods::createNotification(ApiMethods::getPostUserId($postCode), $userId, 2, $postCode);
        $this->output["response"]->success = true;
      }else{
        $this->output["response"]->success = false;
        $this->output["errors"] = "Something went wrong, try again.";
      }

      return $this->printResponse();
    }
  }

  public function getCommentsAction($postCode, $offset){
    if(!$this->request->hasQuery("postCode")){
      $this->output["errors"] = "No parametres";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    $postCode = $this->request->getQuery("postCode", "string", -1);

    $offset = $this->request->getQuery("offset", "int", 0);

    if(!ApiMethods::postExists($postCode)){
      $this->output["errors"] = "Post doesn't exist.";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    $COMMENT_LIMIT = 10;

    $comments = ApiMethods::getCommentsForPost($postCode, $COMMENT_LIMIT, $offset);
    $this->output["response"]->comments = $comments;
    $this->output["response"]->success = true;
    return $this->printResponse();

  }

  public function getPostAction(){
    if(!$this->request->hasQuery("postCode") & !$this->request->hasQuery("userId")){
      $this->output["errors"] = "No parametres";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    $postCode = $this->request->getQuery("postCode", "string", -1);
    $userId = $this->request->getQuery("userId", "int", -1);

    $post = ApiMethods::getPostInfo($postCode, $userId);
    $this->output["response"]->posts = $post;
    return $this->printResponse();
  }

  public function getLikeListAction($postCode){
    if(!$this->request->hasQuery("postCode")){
      $this->output["errors"] = "No parametres";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    $postCode = $this->request->getQuery("postCode", "string", -1);
  }

  public function getFollowersListAction(){

    if(!$this->request->hasQuery("userId")){
      $this->output["errors"] = "No parametres";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    $offset = 0;
    $userId = $this->request->getQuery("userId", "int", 0);

    if($this->request->hasQuery("offset")){
      $offset = $this->request->getQuery("offset", "int", 0);
    }

    $followers = ApiMethods::getFollowerList($userId, $offset);

    $this->output["response"]->users = $followers;
    $this->output["response"]->success = true;
    return $this->printResponse();


  }

  public function getFollowsListAction(){

    if(!$this->request->hasQuery("userId")){
      $this->output["errors"] = "No parametres";
      $this->output["response"]->success = false;
      return $this->printResponse();
    }

    $offset = 0;
    $userId = $this->request->getQuery("userId", "int", 0);

    if($this->request->hasQuery("offset")){
      $offset = $this->request->getQuery("offset", "int", 0);
    }

    $follows = ApiMethods::getFollowsList($userId, $offset);

    $this->output["response"]->users = $follows;
    $this->output["response"]->success = true;
    return $this->printResponse();


  }

  public function deleteCommentAction(){
   if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $commentCode = $this->escaper->escapeHtml($this->request->getPost("commentCode"));
      $apiToken = $this->request->getPost("apiToken");

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::isCommentByUserId($commentCode, $userId)){
        $this->output["errors"] = "Comment is not yours";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(ApiMethods::deleteComment($commentCode)){
        $this->output["response"]->success = true;
      }else{
        $this->output["errors"] = "Something went wrong, try again";
        $this->output["response"]->success = false;
      }
      return $this->printResponse();
    }
  }

    public function getGlobalMessageAction(){
      $globalMessage = ApiMethods::getGlobalMessage();

      $this->output["response"]->message = $globalMessage;
      $this->output["response"]->success = true;
      return $this->printResponse();
  }

  public function broadcastGlobalAction(){
    if($this->request->isPost()){
      $userId = $this->request->getPost("userId");
      $message = $this->escaper->escapeHtml($this->request->getPost("message"));
      $apiToken = $this->request->getPost("apiToken");

      if(!ApiMethods::isAdmin($userId)){
        $this->output["errors"] = "User in not an admin";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(!ApiMethods::checkToken($userId, $apiToken)){
        $this->output["errors"] = "User authentication failed";
        $this->output["response"]->success = false;
        return $this->printResponse();
      }

      if(ApiMethods::broadcastMessage($message)){
        $this->output["response"]->success = true;
      }else{
        $this->output["errors"] = "Something went wrong, try again";
        $this->output["response"]->success = false;
      }
      return $this->printResponse();
    }
  }

}
