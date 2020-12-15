<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();
foreach($arResult['ITEMS'] as $key=>$item){

    $arImgBig = \CFile::ResizeImageGet(
        $item["PREVIEW_PICTURE"]['ID'],
        [
            'width'=>300,
            'height'=>300
        ],BX_RESIZE_IMAGE_EXACT,
        true
    );

    $arResult['ITEMS'][$key]['PREVIEW_PICTURE']['SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['PREVIEW_PICTURE']['WIDTH'] = $arImgBig['width'];
    $arResult['ITEMS'][$key]['PREVIEW_PICTURE']['HEIGHT'] = $arImgBig['height'];

    $arResult['ITEMS'][$key]['PREVIEW_PICTURE_SECOND']['SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['PREVIEW_PICTURE_SECOND']['WIDTH'] = $arImgBig['width'];
    $arResult['ITEMS'][$key]['PREVIEW_PICTURE_SECOND']['HEIGHT'] = $arImgBig['height'];
    $arResult['ITEMS'][$key]['PRODUCT_PREVIEW']['SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['PRODUCT_PREVIEW']['WIDTH'] = $arImgBig['width'];
    $arResult['ITEMS'][$key]['PRODUCT_PREVIEW']['HEIGHT'] = $arImgBig['height'];

    $arResult['ITEMS'][$key]['PRODUCT_PREVIEW_SECOND']['SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['PRODUCT_PREVIEW_SECOND']['WIDTH'] = $arImgBig['width'];
    $arResult['ITEMS'][$key]['PRODUCT_PREVIEW_SECOND']['HEIGHT'] = $arImgBig['height'];

}