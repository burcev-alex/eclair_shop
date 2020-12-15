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

$arViewModeList = $arResult['VIEW_MODE_LIST'];

$arCurView = $arViewStyles[$arParams['VIEW_MODE']];

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

?><div class="<? echo $arCurView['CONT']; ?> container"><?

    foreach ($arResult['SECTIONS'] as &$arSection)
    {
        $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

        if (false === $arSection['PICTURE'])
            $arSection['PICTURE'] = array(
                'SRC' => $arCurView['EMPTY_IMG'],
                'ALT' => (
                '' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
                    ? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
                    : $arSection["NAME"]
                ),
                'TITLE' => (
                '' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
                    ? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
                    : $arSection["NAME"]
                )
            );
        ?><span id="<? echo $this->GetEditAreaId($arSection['ID']); ?>">
        <a
                href="<? echo $arSection['SECTION_PAGE_URL']; ?>"
                class="bx_catalog_line_img"
                style="background-image: url('<? echo $arSection['PICTURE']['SRC']; ?>');"
                title="<? echo $arSection['PICTURE']['TITLE']; ?>"
        ></a>
        <h2 class="bx_catalog_line_title"><a href="<? echo $arSection['SECTION_PAGE_URL']; ?>"><? echo $arSection['NAME']; ?></a><?
            if ($arParams["COUNT_ELEMENTS"])
            {
                ?> <span>(<? echo $arSection['ELEMENT_CNT']; ?>)</span><?
            }
            ?></h2><?
        if ('' != $arSection['DESCRIPTION'])
        {
            ?><p class="bx_catalog_line_description"><? echo $arSection['DESCRIPTION']; ?></p><?
        }
        ?><div style="clear: both;"></div>
        </span><?
    }
?></div>