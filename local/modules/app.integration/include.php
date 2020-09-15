<?php
namespace App\Integration;

/**
 * Базовый каталог модуля
 */
const BASE_DIR = __DIR__;
/**
 * Имя модуля
 */
const MODULE_ID = 'app.integration';

IncludeModuleLangFile(__FILE__);

$arClassBase = array(
    // rest client
    '\App\Integration\Rest\Client\Crm' => 'lib/rest/client/crm.php',
    '\App\Integration\Rest\Client\AbstractBase' => 'lib/rest/client/abstractBase.php',

	// rest server
	'\App\Integration\Rest\Server\Main' => 'lib/rest/server/main.php',

	// handlers
	'\App\Integration\Handlers\Order' => 'lib/handlers/order.php',

	'\App\Integration\Event' => 'lib/event.php',
	'\App\Integration\Tools' => 'lib/tools.php',
);

$arClassLib = array();

\Bitrix\Main\Loader::registerAutoLoadClasses(
	'app.integration',
	array_merge($arClassBase, $arClassLib)

);