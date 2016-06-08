<?php
namespace ImLike\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller{

	public function beforeExecuteRoute($dispatcher){
		$controllerName = $dispatcher->getControllerName();
		$actionName = $dispatcher->getActionName();
		if($controllerName == "index" && $actionName == "index") $this->view->isHomepage = true;
		else $this->view->isHomepage = false;
	}

	 public function initialize(){
	 	$this->view->loggedInUsername = $this->session->get("username");
	    $this->view->loggedInUserId = $this->session->get("userId");
	    $this->view->loggedInApiToken = $this->session->get("apiToken");
	    $this->view->baseUrl = $_SERVER['HTTP_HOST'];
	    if($this->view->loggedInUsername == null) $this->view->loggedIn == false;
	    else $this->view->loggedIn = true;
	 }

}
