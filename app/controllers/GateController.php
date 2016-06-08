<?php
namespace ImLike\Controllers;

use ImLike\Controllers\ControllerBase;
use ImLike\Forms\LoginForm;
use ImLike\Forms\RegisterForm;

use Phalcon\Http\Response;

class GateController extends ControllerBase{

	public function beforeExecuteRoute($dispatcher){
		parent::beforeExecuteRoute($dispatcher);
		if($this->session->has("username")){
			echo "here";
			$response = new Response();
			$response->redirect("home");
			$response->send();
		}
	}

	public function indexAction(){
		$loginForm = new LoginForm();
		$registerForm = new RegisterForm();
		$this->view->loginForm = $loginForm;
		$this->view->registerForm = $registerForm;
		
		if($this->security->getSessionToken() == "") $this->view->token = $this->security->getToken();
		else $this->view->token = $this->security->getSessionToken();
	}

}

