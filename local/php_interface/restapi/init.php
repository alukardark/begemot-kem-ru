<?php

use Artamonov\Api\Init as Api;


$cotrollersDir = "/local/php_interface/restapi";

\Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    'Artamonov\Api\Controllers\BaseAPIController' => $cotrollersDir. '/BaseAPIController.php',
    'Artamonov\Api\Controllers\Infoscreen' => $cotrollersDir. '/Infoscreen.php',
    'Artamonov\Api\Controllers\Bonus' => $cotrollersDir. '/Bonus.php',
    'Artamonov\Api\Controllers\Sale' => $cotrollersDir. '/Sale.php',
    'Artamonov\Api\Controllers\Shops' => $cotrollersDir. '/Shops.php',
    'Artamonov\Api\Controllers\Star' => $cotrollersDir. '/Star.php',
));

$api = new Api();
$api->start();