<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach ($arResult['ITEMS'] as $key => $arElement)
{
//    echo '<pre>';
//    var_dump($arElement);
//    echo '</pre>';
    $arResult['SECTIONS'][$arElement['IBLOCK_SECTION_ID']] = false;

	if(is_array($arElement["DETAIL_PICTURE"]) || !empty($arElement["PREVIEW_PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
		}

		$picture = (!empty($arElement['DETAIL_PICTURE']) ? $arElement['DETAIL_PICTURE'] : (!empty($arElement['PREVIEW_PICTURE']) ? $arElement['PREVIEW_PICTURE'] : ''));
		
		$arFileTmp = CFile::ResizeImageGet(
            $picture,
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
usort($arResult["ITEMS"], function ($item1, $item2) {
    return $item2['IBLOCK_SECTION_ID'] <=> $item1['IBLOCK_SECTION_ID'];
});
/**
 * Получаем название разделов для элементов
 */
$rsSection = \Bitrix\Iblock\SectionTable::getList(array(
    'filter' => array(
        'ID' => array_keys($arResult['SECTIONS']),
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'DEPTH_LEVEL' => 1,
    ),
    'select' =>  array('ID','CODE','NAME'),
));

while ($arSection = $rsSection->fetch())
{
    $arResult['SECTIONS'][$arSection['ID']] = $arSection['NAME'];
}
?>