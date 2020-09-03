<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$arParams = array(
	"MENU_FILE_PATH" => SITE_DIR . "/demo_app/.mobile_menu.php",
);
CMobile::getInstance()->setLargeScreenSupport(false);
CMobile::getInstance()->setScreenCategory("NORMAL");
$APPLICATION->IncludeComponent(
	'bitrix:mobileapp.menu',
	'mobile',
	$arParams,
	false,
	Array('HIDE_ICONS' => 'Y'));
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php") ?>