<?php
mb_internal_encoding("UTF-8");
$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(array(
    'ImLike\Models' => $config->application->modelsDir,
    'ImLike\Controllers' => $config->application->controllersDir,
    'ImLike\Forms' => $config->application->formsDir,
    'ImLike\Images' => $config->application->libraryDir . "images/",
    'ImLike\Generators' => $config->application->libraryDir . "generators/",
    'ImLike\Api' => $config->application->libraryDir . "api/",
    'ImLike' => $config->application->libraryDir,
));

$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir
    )
)->register();

require_once $config->application->libraryDir . "amazon/aws-autoloader.php";
require_once $config->application->libraryDir . "phpmailer/PHPMailerAutoload.php";
