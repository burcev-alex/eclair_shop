<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

 $preview = !empty($arResult['PREVIEW_PICTURE']);
 if(!empty($arResult['MORE_PHOTO'])){
     foreach($arResult['MORE_PHOTO'] as $key=>$photo){
         if($photo["ID"] == 0 && $preview){
             unset($arResult['MORE_PHOTO'][$key]);
             $arImgBig = \CFile::ResizeImageGet(
                 $arResult["PREVIEW_PICTURE"]['ID'],
                 [
                     'width'=>300,
                     'height'=>300
                 ],BX_RESIZE_IMAGE_EXACT,
                 true
             );
             $arResult['MORE_PHOTO'][] = $arResult["PREVIEW_PICTURE"];
         }
     }
 }
