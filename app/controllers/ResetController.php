<?php
namespace ImLike\Controllers;

use ImLike\Controllers\ControllerBase;

use ImLike\Models\Users;
use ImLike\Models\Password_Reset;
use ImLike\Models\Authentication;


use ImLike\Forms\ForgotPasswordForm;

class ResetController extends ControllerBase{

	private static $resetLinkRoot = "http://10.0.0.1/reset/token/";

	public function beforeExecuteRoute($dispatcher){
		if($dispatcher->getActionName() == "token"){
			if(count($dispatcher->getParams()) != 1){
				die("wrong params");
			}
		}
	}

	public function indexAction(){ // move to ApiController
		$this->view->disable();
		if($this->request->isPost()){
			$forgotForm = new ForgotPasswordForm();
			if($forgotForm->isValid($this->request->getPost())){
				$resetToken = $this->request->getPost("forgot-resetToken");
				$newPassword = $this->security->hash($this->request->getPost("forgot-password"));
				$reset = Password_Reset::findFirstByToken($resetToken);
				if($reset){
					$userId = $reset->userId;
					$user = Users::findFirstByUserId($userId);
					$user->password = $newPassword;
					$auth = Authentication::findFirstByUserId($userId);
					if($user->save()){
						if(!$auth || $auth->delete()){
							echo "everything ok";
						}else{
							echo "failed to delete auth";
						}
					}else echo "database error";
				}else{
					echo "bad token";
				}
			}else{
				foreach ($forgotForm->getMessages() as $message) {
			    echo $message->getMessage();
				}
				echo "\nform not valid";
			}
		}else{
			echo "no post";
		}
	}

	public function tokenAction($resetToken){
		$reset = Password_Reset::findFirstByToken($resetToken);
		if($reset){
			$form = new ForgotPasswordForm();
			$this->view->forgotForm = $form;
			$this->view->resetToken = $resetToken;
			$this->view->csrfToken = $this->security->getToken();
		}else{
			$this->view->disable();
			echo "No reset record.";
		}
	}

  public function passwordAction(){
  	$this->view->disable();
  	if($this->request->isPost()){
  		$email = $this->request->getPost("email");
  		$user = Users::findFirstByEmail($email);
	  	if($user){
	  		if($this->sendResetMail($user->email, $user->userId)) echo "email successfully sent.";
	  		else echo "something went wrong-";
	  	}else{
	  		die("User doesn't exist");
	  	}
  	}else{
  		return $this->dispatcher->forward(array(
            "controller" => "api",
            "action" => "index"
      ));
  	}
  }

  private function generateResetLink($userId){
		$tokenLenght = 32;
		$isSecure = true;
		$token = bin2hex(openssl_random_pseudo_bytes($tokenLenght, $isSecure));
		$reset = Password_Reset::findFirstByToken($token);
		if($reset) return $this->generateResetLink();
		else{
			$newReset = new Password_Reset();
			$newReset->userId = $userId;
			$newReset->token = $token;
			if($newReset->save()) return self::$resetLinkRoot . $token;
			else echo "database errro";
		}
  }

  private function sendResetMail($email, $userId){
  	$resetLink = $this->generateResetLink($userId);

  	$mail = new \PHPMailer;

		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host = "smtp.gmail.com";
		$mail->Username = "oupicky2@gmail.com";
		$mail->Password = "password";
		$mail->Port = "465";

		$mail->From = 'oupicky2@gmail.com';
		$mail->FromName = 'Honzaik';
		$mail->addAddress($email, 'honzaik');  // Add a recipient
		$mail->addReplyTo('oupicky2@gmail.com', 'derp');

		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = 'Password reset';
		$mail->Body    = 'Password reset link: <a href=' . $resetLink .'>' . $resetLink . '</a>';
		$mail->AltBody = 'Password reset link: ' . $resetLink;

		if(!$mail->send()) {
		   return false;
		}

		return true;
  }
}