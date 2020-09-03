<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach ($arResult['ITEMS'] as $key => $arElement)
{
//    echo '<pre>';
//    var_dump($arElement);
//    echo '</pre>';

	if(is_array($arElement["DETAIL_PICTURE"]) || !empty($arElement["PREVIEW_PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
		}

		$picture = (!empty($arElement['DETAIL_PICTURE']) ? $arElement['DETAIL_PICTURE'] : (!empty($arElement['PREVIEW_PICTURE']) ? $arElement['PREVIEW_PICTURE'] : ''));
		
		$arFileTmp = CFile::ResizeImageGet(
			$arElement['DETAIL_PICTURE'],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
            BX_RESIZE_IMAGE_EXACT,
			true, $arFilter
		);
		
		$arResult["ITEMS"][$key]["PREVIEW_IMG"] = array(
			"SRC" => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	} else {


		$arResult["ITEMS"][$key]["PREVIEW_IMG"] = array(
            "SRC" => SITE_TEMPLATE_PATH."/images/default.png",
        );
    }
}
?>