<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
foreach($arResult['ITEMS'] as $key=>$item){

    $res = CIBlockSection::GetByID($item["IBLOCK_SECTION_ID"]);
    if($ar_res = $res->GetNext()){

            $arResult["ITEMS"][$key]["SECTIONNAME"] = $ar_res["NAME"];

    }
if(!empty( $item["PREVIEW_PICTURE"])){
    $arImgBig = \CFile::ResizeImageGet(
        $item["PREVIEW_PICTURE"]['ID'],
        [
            'width'=>300,
            'height'=>300
        ],BX_RESIZE_IMAGE_EXACT,
        true
    );

    $arResult['ITEMS'][$key]['PREVIEW_PICTURE']['SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['PREVIEW_PICTURE']['SAFE_SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['PREVIEW_PICTURE']['UNSAFE_SRC'] = $arImgBig['src'];
}
elseif(!empty( $item["DETAIL_PICTURE"])){
    $arImgBig = \CFile::ResizeImageGet(
        $item["DETAIL_PICTURE"]['ID'],
        [
            'width'=>300,
            'height'=>300
        ],BX_RESIZE_IMAGE_EXACT,
        true
    );

    $arResult['ITEMS'][$key]['DETAIL_PICTURE']['SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['DETAIL_PICTURE']['SAFE_SRC'] = $arImgBig['src'];
    $arResult['ITEMS'][$key]['DETAIL_PICTURE']['UNSAFE_SRC'] = $arImgBig['src'];
}
    if(!empty($item["OFFERS"])){
        foreach($item["OFFERS"] as $ofKey=>$offer){
            if(!empty($offer["DETAIL_PICTURE"])){
                $arImgOffer = \CFile::ResizeImageGet(
                    $offer["DETAIL_PICTURE"]['ID'],
                    [
                        'width'=>300,
                        'height'=>300
                    ],BX_RESIZE_IMAGE_EXACT,
                    true
                );

                $arResult['ITEMS'][$key]["OFFERS"][$ofKey]['DETAIL_PICTURE']['SRC'] = $arImgOffer['src'];
                $arResult['ITEMS'][$key]["OFFERS"][$ofKey]['DETAIL_PICTURE']['SAFE_SRC'] = $arImgOffer['src'];
                $arResult['ITEMS'][$key]["OFFERS"][$ofKey]['DETAIL_PICTURE']['UNSAFE_SRC'] = $arImgOffer['src'];
            }
        }
    }

}
//print '<pre>';print_r($arResult['ITEMS']);die();
$arParams = $component->applyTemplateModifications();
