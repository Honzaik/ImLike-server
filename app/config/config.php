<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => 'password',
        'dbname'      => 'imlike',
    ),
    'application' => array(
        'appDir'         => __DIR__ . '/../../app/',
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'modelsDir'      => __DIR__ . '/../../app/models/',
        'viewsDir'       => __DIR__ . '/../../app/views/',
        'pluginsDir'     => __DIR__ . '/../../app/plugins/',
        'libraryDir'     => __DIR__ . '/../../app/library/',
        'cacheDir'       => __DIR__ . '/../../app/cache/',
        'formsDir'       => __DIR__ . '/../../app/forms/',
        'imageStuffDir'  => __DIR__ . '/../../app/library/images/',
        'baseUri'        => '/',
    ),
    'other' => array(
        'imagesDir'      => __DIR__ . '/../../public/images/',
        'profileImagesDir' => __DIR__ . '/../../public/profileImages/',
    ),
));
