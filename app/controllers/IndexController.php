<?php
namespace ImLike\Controllers;

use ImLike\Controllers\ControllerBase;
use ImLike\Api\ApiMethods;

class IndexController extends ControllerBase{

    public function indexAction(){
    	$this->view->numberRegisteredUsers = ApiMethods::getNumberOfRegisteredUsers();
    	$this->view->numberImLikes = ApiMethods::getNumberOfImLikes();
    }
}

