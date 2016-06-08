<?php
use Phalcon\Mvc\Router;

$router = new Router();

$router->add('/user/:params', array(
		"controller" 	=> "user",
		"action" 			=> "index",
		"params" 			=> 1,
));

$router->add('/u/:params', array(
		"controller" 	=> "user",
		"action" 			=> "index",
		"params" 			=> 1,
));

$router->add('/p/:params', array(
		"controller" 	=> "post",
		"action" 			=> "index",
		"params" 			=> 1,
));

$router->add('/post/:params', array(
		"controller" 	=> "post",
		"action" 			=> "index",
		"params" 			=> 1,
));

return $router;

