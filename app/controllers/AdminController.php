<?php
namespace ImLike\Controllers;


class AdminController extends ControllerBase{

	private static $adminList = array("honzaik", "imlike");


	public function indexAction(){
		if(in_array($this->view->loggedInUsername, self::$adminList)) $this->view->isAllowed = true;
		else $this->view->isAllowed = false;
	}

}