<?
require($_SERVER["DOCUMENT_ROOT"]."/eshop_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");
?>
<?$APPLICATION->IncludeComponent(
    "strizhi:order",
    "",
    Array(
    )
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>