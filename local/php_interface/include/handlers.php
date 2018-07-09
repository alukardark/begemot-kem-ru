<?php

/**
 * Добавляем классы в автозагрузку
 */
$handlersDir = '/local/php_interface/classes/handlers/';

\Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    '\Axi\Handlers\Main' => $handlersDir . 'Main.php',
    '\Axi\Handlers\IBlock' => $handlersDir . 'IBlock.php',
));

/**
 * Регистрируем обработчики событий
 */
$em = \Bitrix\Main\EventManager::getInstance();

$em->addEventHandler('iblock', 'OnBeforeIBlockElementAdd', array('\Axi\Handlers\IBlock', 'OnBeforeIBlockElementAdd'));
$em->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', array('\Axi\Handlers\IBlock', 'OnBeforeIBlockElementUpdate'));
$em->addEventHandler('iblock', 'OnBeforeIBlockElementDelete', array('\Axi\Handlers\IBlock', 'OnBeforeIBlockElementDelete'));
