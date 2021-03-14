<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$this->setFrameMode(true);

?>
<div class="row">
<?

$sectionID = [];
$sectionCODE = '';
if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
    $section[] = $arResult["VARIABLES"]["SECTION_ID"];

if (0 < strlen($arResult["VARIABLES"]["SECTION_CODE"]))
    $sectionCODE = $arResult["VARIABLES"]["SECTION_CODE"];

    $APPLICATION->IncludeComponent(
        "strizhi:catalog.mainpage",
        ".default",
        array(
            "IBLOCK_SECTION_ID" => $sectionID, // have to be an array
            "IBLOCK_SECTION_CODE" => $sectionCODE // have to be an array
        ));
?>
</div>