<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$arIds = [];
foreach ($arResult['ITEMS']['AnDelCanBuy'] as $key  =>  $arElement) {
    $arIds[]    =   $arElement['ID'];
}

echo '<pre>';
//var_dump($arIds);
//var_dump($arResult);
echo '</pre>';

foreach ($arResult["ITEMS_IMG"] as $val=>$arPhoto)
{
	$arFileTmp = CFile::ResizeImageGet(
		$arPhoto,
		array("width" => "110", "height" =>"110"),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);
	$arResult["ITEMS_IMG"][$val]  = array(
		"SRC" => $arFileTmp["src"],
		'WIDTH' => $arFileTmp["width"],
		'HEIGHT' => $arFileTmp["height"],
	);
}
?>