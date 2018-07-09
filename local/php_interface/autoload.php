<?php

require $_SERVER['DOCUMENT_ROOT'] . "/local/composer/vendor/autoload.php";

$classesDir = "/local/php_interface/classes";
$modelsDir = "/local/php_interface/models";

\Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    '\Axi\Helpers' => $classesDir. '/Helpers.php',
    '\Geo' => $classesDir . '/Geo.php',
    '\ShopsModel' => $modelsDir . '/ShopsModel.php',
    '\SocnetsModel' => $modelsDir . '/SocnetsModel.php',
    '\GalleriesModel' => $modelsDir . '/GalleriesModel.php',
    '\TenantsModel' => $modelsDir . '/TenantsModel.php',
    '\APIInfoscreenModel' => $modelsDir . '/APIInfoscreenModel.php',
    '\APIBonusModel' => $modelsDir . '/APIBonusModel.php',
    '\APISaleModel' => $modelsDir . '/APISaleModel.php',
    '\APIStarModel' => $modelsDir . '/APIStarModel.php',
));