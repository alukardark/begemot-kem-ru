<?php

use Bitrix\Main\Loader;


date_default_timezone_set("Asia/Novokuznetsk");

if ($_REQUEST['kkk'] == 'kkk') {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    ini_set('display_errors', 1);
}

Loader::includeModule("main");
Loader::includeModule("iblock");
Loader::includeModule('ws.projectsettings');

require_once "autoload.php";

require_once "include/arrilot.php";

require_once "include/functions.php";

require_once "globals.php";

require_once "include/ie.php";

require_once "include/handlers.php";

Loader::includeModule('artamonov.api');
require_once "restapi/init.php";