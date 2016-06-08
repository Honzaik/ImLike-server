<?php
require_once('redis-session.php');

$db = new Redis();
$sessHandler = new RedisSessionHandler($db);
session_set_save_handler($sessHandler);
session_start();
echo "hi";