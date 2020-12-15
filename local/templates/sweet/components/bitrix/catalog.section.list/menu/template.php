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
$this->setFrameMode(true);

?>
   <ul>
    <?
if (0 < $arResult["SECTIONS_COUNT"])
{

    foreach ($arResult['SECTIONS'] as &$arSection)
    {
        ?>

        <h2 class="bx_catalog_line_title"><? echo $arSection['NAME']; ?></h2>

        <?
    }

}
?></ul>