<?php

define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$cart = new SaleBasketLineComponent ();
$cart->initComponent ('bitrix:sale.basket.basket.line');
$cart->includeComponentLang();


$APPLICATION->RestartBuffer();
header('Content-Type: text/html; charset='.LANG_CHARSET);
$cart->executeAjax($_POST["siteId"]);

	die();
