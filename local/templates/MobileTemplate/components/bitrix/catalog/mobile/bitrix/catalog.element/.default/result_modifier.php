<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Iblock;

if(is_array($arResult["DETAIL_PICTURE"]) || !empty($arResult["PREVIEW_PICTURE"]))
{
	$arFilter = '';
	//if($arParams["SHARPEN"] != 0)
	//{
		$arFilter = array("name" => "sharpen", "precision" => 100/*$arParams["SHARPEN"]*/);
	//}

    $picture = (!empty($arResult['DETAIL_PICTURE']) ? $arResult['DETAIL_PICTURE'] : (!empty($arResult['PREVIEW_PICTURE']) ? $arResult['PREVIEW_PICTURE'] : ''));

    $arFileTmp = CFile::ResizeImageGet(
        $picture,
		array("width" => 300, "height" => 300),
        BX_RESIZE_IMAGE_EXACT,
		true, $arFilter
	);

	$arResult['DETAIL_PICTURE_SMALL'] = array(
		'SRC' => $arFileTmp["src"],
		'WIDTH' => $arFileTmp["width"],
		'HEIGHT' => $arFileTmp["height"],
	);
}
foreach ($arResult["DISPLAY_PROPERTIES"] as $code => $property)
{
	if ($property["PROPERTY_TYPE"] == "F")
		unset($arResult["DISPLAY_PROPERTIES"][$code]);
}

if (is_array($arResult['MORE_PHOTO']) && count($arResult['MORE_PHOTO']) > 0)
{
	$arPhotoGallery = array();
	if(count($arResult["MORE_PHOTO"])>0)
	{
		foreach($arResult["MORE_PHOTO"] as $photo)
		{
			$arPhotoGallery[] = $photo["SRC"];
		}
		$arResult["PHOTO_GALLERY"] = $arPhotoGallery;
	}

	foreach ($arResult['MORE_PHOTO'] as $key => $arFile)
	{
		$arFilter = '';
		$arFilter = array("name" => "sharpen", "precision" => 100);

		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => 60, "height" => 60),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arFile['PREVIEW_WIDTH'] = $arFileTmp["width"];
		$arFile['PREVIEW_HEIGHT'] = $arFileTmp["height"];

		$arFile['SRC'] = $arFileTmp['src'];
		$arResult['MORE_PHOTO'][$key] = $arFile;
	}
}

// -------------------------------------------------------------------------------------

$arSKUPropList = array();
$arSKUPropIDs = array();
$arSKUPropKeys = array();
$boolSKU = false;
$strBaseCurrency = '';
$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);

if ($arResult['MODULES']['catalog'])
{
	if (!$boolConvert)
		$strBaseCurrency = CCurrency::GetBaseCurrency();

	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
	$boolSKU = !empty($arSKU) && is_array($arSKU);

	if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']))
	{
		$arSKUPropList = CIBlockPriceTools::getTreeProperties(
			$arSKU,
			$arParams['OFFER_TREE_PROPS'],
			array(
				'PICT' => $arEmptyPreview,
				'NAME' => '-'
			)
		);
		$arSKUPropIDs = array_keys($arSKUPropList);
	}
}


$arResult['CHECK_QUANTITY'] = false;
if (!isset($arResult['CATALOG_MEASURE_RATIO']))
	$arResult['CATALOG_MEASURE_RATIO'] = 1;
if (!isset($arResult['CATALOG_QUANTITY']))
	$arResult['CATALOG_QUANTITY'] = 0;
$arResult['CATALOG_QUANTITY'] = (
	0 < $arResult['CATALOG_QUANTITY'] && is_float($arResult['CATALOG_MEASURE_RATIO'])
	? (float)$arResult['CATALOG_QUANTITY']
	: (int)$arResult['CATALOG_QUANTITY']
);
$arResult['CATALOG'] = false;
if (!isset($arResult['CATALOG_SUBSCRIPTION']) || 'Y' != $arResult['CATALOG_SUBSCRIPTION'])
	$arResult['CATALOG_SUBSCRIPTION'] = 'N';

CIBlockPriceTools::getLabel($arResult, $arParams['LABEL_PROP']);


if ($arResult['MODULES']['catalog'])
{
	$arResult['CATALOG'] = true;
	if (!isset($arResult['CATALOG_TYPE']))
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
	if (
		(CCatalogProduct::TYPE_PRODUCT == $arResult['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arResult['CATALOG_TYPE'])
		&& !empty($arResult['OFFERS'])
	)
	{
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
	}
	switch ($arResult['CATALOG_TYPE'])
	{
		case CCatalogProduct::TYPE_SET:
			$arResult['OFFERS'] = array();
			$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
			break;
		case CCatalogProduct::TYPE_SKU:
			break;
		case CCatalogProduct::TYPE_PRODUCT:
		default:
			$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
			break;
	}
}
else
{
	$arResult['CATALOG_TYPE'] = 0;
	$arResult['OFFERS'] = array();
}

if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
{
	$boolSKUDisplayProps = false;

	$arResultSKUPropIDs = array();
	$arFilterProp = array();
	$arNeedValues = array();
	foreach ($arResult['OFFERS'] as &$arOffer)
	{
		foreach ($arSKUPropIDs as &$strOneCode)
		{
			if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
			{
				$arResultSKUPropIDs[$strOneCode] = true;
				if (!isset($arNeedValues[$arSKUPropList[$strOneCode]['ID']]))
					$arNeedValues[$arSKUPropList[$strOneCode]['ID']] = array();
				$valueId = (
					$arSKUPropList[$strOneCode]['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST
					? $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']
					: $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']
				);
				$arNeedValues[$arSKUPropList[$strOneCode]['ID']][$valueId] = $valueId;
				unset($valueId);
				if (!isset($arFilterProp[$strOneCode]))
					$arFilterProp[$strOneCode] = $arSKUPropList[$strOneCode];
			}
		}
		unset($strOneCode);
	}
	unset($arOffer);

	// If offers does not have filtered props, unset this prop.
	$arSkuNeedleIds = array_keys($arNeedValues);
	foreach ($arSKUPropList as $k => $sku){
	    if (!in_array($sku["ID"], $arSkuNeedleIds)){
	        unset($arSKUPropList[$k]);
        }
    }
    unset($arSkuNeedleIds);

	CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);

	foreach($arSKUPropList as $code=>$valProp){
		if($valProp["CODE"] == "RAZMER"){
			$arSKUPropList[$code]["NAME"] = $valProp["NAME"].", см";
		}
		foreach($valProp["VALUES"] as $valueId=>$valItem){
			if(IntVal($valItem["ID"]) == 0){
				unset($arSKUPropList[$code]["VALUES"][$valueId]);
			}
		}
	}

	$arSKUPropIDs = array_keys($arSKUPropList);
	$arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);


	$arMatrixFields = $arSKUPropKeys;
	$arMatrix = array();

	$arNewOffers = array();

	$arIDS = array($arResult['ID']);
	$arOfferSet = array();
	$arResult['OFFER_GROUP'] = false;
	$arResult['OFFERS_PROP'] = false;

	$arDouble = array();
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		$arOffer['ID'] = (int)$arOffer['ID'];
		if (isset($arDouble[$arOffer['ID']]))
			continue;
		$arIDS[] = $arOffer['ID'];
		$boolSKUDisplayProperties = false;
		$arOffer['OFFER_GROUP'] = false;
		$arRow = array();
		foreach ($arSKUPropIDs as $propkey => $strOneCode)
		{
			$arCell = array(
				'VALUE' => 0,
				'SORT' => PHP_INT_MAX,
				'NA' => true
			);
			if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
			{
				$arMatrixFields[$strOneCode] = true;
				$arCell['NA'] = false;
				if ('directory' == $arSKUPropList[$strOneCode]['USER_TYPE'])
				{
					$intValue = $arSKUPropList[$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
					$arCell['VALUE'] = $intValue;
				}
				elseif ('L' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
				{
					$arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID'];
				}
				elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
				{
					$arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE'];
				}
				$arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
			}
			$arRow[$strOneCode] = $arCell;
		}
		$arMatrix[$keyOffer] = $arRow;

		\CIBlockPriceTools::setRatioMinPrice($arOffer, false);

		$arOffer['MORE_PHOTO'] = array();
		$arOffer['MORE_PHOTO_COUNT'] = 0;

		if(isset($arOffer["DETAIL_PICTURE"])) {
			$arFirstElementPhoto = $arOffer["DETAIL_PICTURE"];
		}

		$offerSlider = [];

		if(is_array($arFirstElementPhoto)){
			$offerSlider[] = [
				'ID' => $arFirstElementPhoto["ID"],
				'SRC' => $arFirstElementPhoto["SRC"],
				'WIDTH' => $arFirstElementPhoto["WIDTH"],
				'HEIGHT' => $arFirstElementPhoto["HEIGHT"]
			];
		}

		$offerSliderMore = CIBlockPriceTools::getSliderForItem($arOffer, $arParams['OFFER_ADD_PICT_PROP'], $arParams['ADD_DETAIL_TO_SLIDER'] == 'Y');
		if (empty($offerSliderMore))
		{
			$offerSliderMore = $productSlider;
		}
		else{
			$offerSlider = array_merge($offerSlider, $offerSliderMore);
		}


		if($arResult['IS_MORE_PHOTO']) {
			$offerSlider = array_merge($offerSlider, $arResult['MORE_PHOTO']);
		}

		krsort($offerSlider);

		$arOffer['MORE_PHOTO'] = $offerSlider;
		$arOffer['MORE_PHOTO_COUNT'] = count($offerSlider);

		if (CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']))
		{
			$boolSKUDisplayProps = true;
		}

		$arDouble[$arOffer['ID']] = true;
		$arNewOffers[$keyOffer] = $arOffer;
	}
	$arResult['OFFERS'] = $arNewOffers;
	$arResult['SHOW_OFFERS_PROPS'] = $boolSKUDisplayProps;

	$arUsedFields = array();
	$arSortFields = array();

	foreach ($arSKUPropIDs as $propkey => $strOneCode)
	{
		$boolExist = $arMatrixFields[$strOneCode];
		foreach ($arMatrix as $keyOffer => $arRow)
		{
			if ($boolExist)
			{
				if (!isset($arResult['OFFERS'][$keyOffer]['TREE']))
					$arResult['OFFERS'][$keyOffer]['TREE'] = array();
				$arResult['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$strOneCode]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
				$arResult['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
				$arUsedFields[$strOneCode] = true;
				$arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
			}
			else
			{
				unset($arMatrix[$keyOffer][$strOneCode]);
			}
		}
	}
	$arResult['OFFERS_PROP'] = $arUsedFields;
	$arResult['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');


	$offerSet = array();
	if (!empty($arIDS) && CBXFeatures::IsFeatureEnabled('CatCompleteSet'))
	{
		$offerSet = array_fill_keys($arIDS, false);
		$rsSets = CCatalogProductSet::getList(
			array(),
			array(
				'@OWNER_ID' => $arIDS,
				'=SET_ID' => 0,
				'=TYPE' => CCatalogProductSet::TYPE_GROUP
			),
			false,
			false,
			array('ID', 'OWNER_ID')
		);
		while ($arSet = $rsSets->Fetch())
		{
			$arSet['OWNER_ID'] = (int)$arSet['OWNER_ID'];
			$offerSet[$arSet['OWNER_ID']] = true;
			$arResult['OFFER_GROUP'] = true;
		}
		if ($offerSet[$arResult['ID']])
		{
			foreach ($offerSet as &$setOfferValue)
			{
				if ($setOfferValue === false)
				{
					$setOfferValue = true;
				}
			}
			unset($setOfferValue);
			unset($offerSet[$arResult['ID']]);
		}
		if ($arResult['OFFER_GROUP'])
		{
			$offerSet = array_filter($offerSet);
			$arResult['OFFER_GROUP_VALUES'] = array_keys($offerSet);
		}
	}

	$arMatrix = array();
	$intSelected = -1;
	$arResult['MIN_PRICE'] = false;
	$arResult['MIN_BASIS_PRICE'] = false;
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		if (empty($arResult['MIN_PRICE']))
		{
			if ($arResult['OFFER_ID_SELECTED'] > 0)
				$foundOffer = ($arResult['OFFER_ID_SELECTED'] == $arOffer['ID']);
			else
				$foundOffer = $arOffer['CAN_BUY'];
			if ($foundOffer)
			{
				$intSelected = $keyOffer;
				$arResult['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
				$arResult['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
			}
			unset($foundOffer);
		}

		$arSKUProps = false;
		if (!empty($arOffer['DISPLAY_PROPERTIES']))
		{
			$boolSKUDisplayProps = true;
			$arSKUProps = array();
			foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
			{
				if ('F' == $arOneProp['PROPERTY_TYPE'])
					continue;
				$arSKUProps[] = array(
					'NAME' => $arOneProp['NAME'],
					'VALUE' => $arOneProp['DISPLAY_VALUE']
				);
			}
			unset($arOneProp);
		}
		if (isset($arOfferSet[$arOffer['ID']]))
		{
			$arOffer['OFFER_GROUP'] = true;
			$arResult['OFFERS'][$keyOffer]['OFFER_GROUP'] = true;
		}
		reset($arOffer['MORE_PHOTO']);
		$firstPhoto = current($arOffer['MORE_PHOTO']);
		$arOneRow = array(
			'ID' => $arOffer['ID'],
			'NAME' => $arOffer['~NAME'],
			'TREE' => $arOffer['TREE'],
			'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
			'BASIS_PRICE' => $arOffer['MIN_PRICE'],
			'DISPLAY_PROPERTIES' => $arSKUProps,
			'PREVIEW_PICTURE' => $firstPhoto,
			'DETAIL_PICTURE' => $firstPhoto,
			'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
			'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
			'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
			'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
			'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
			'OFFER_GROUP' => (isset($offerSet[$arOffer['ID']]) && $offerSet[$arOffer['ID']]),
			'CAN_BUY' => $arOffer['CAN_BUY'],
			'SLIDER' => $arOffer['MORE_PHOTO'],
			'SLIDER_COUNT' => $arOffer['MORE_PHOTO_COUNT'],
		);
		$arMatrix[$keyOffer] = $arOneRow;
	}
	if (-1 == $intSelected)
	{
		$intSelected = 0;
		$arResult['MIN_PRICE'] = (isset($arResult['OFFERS'][0]['RATIO_PRICE']) ? $arResult['OFFERS'][0]['RATIO_PRICE'] : $arResult['OFFERS'][0]['MIN_PRICE']);
		$arResult['MIN_BASIS_PRICE'] = $arResult['OFFERS'][0]['MIN_PRICE'];
	}
	$arResult['JS_OFFERS'] = $arMatrix;
	$arResult['OFFERS_SELECTED'] = $intSelected;
	if ($arMatrix[$intSelected]['SLIDER_COUNT'] > 0)
	{
		$arResult['MORE_PHOTO'] = array_merge($arResult['MORE_PHOTO'], $arMatrix[$intSelected]['SLIDER']);
	}

	$arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
}

$arResult['MORE_PHOTO_COUNT'] = count($arResult['MORE_PHOTO']);

if ($arResult['MODULES']['catalog'] && $arResult['CATALOG'])
{
	if ($arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT || $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
	{
		CIBlockPriceTools::setRatioMinPrice($arResult, false);
		$arResult['MIN_BASIS_PRICE'] = $arResult['MIN_PRICE'];
	}
	if (
		CBXFeatures::IsFeatureEnabled('CatCompleteSet')
		&& (
			$arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT
			|| $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET
		)
	)
	{
		$rsSets = CCatalogProductSet::getList(
			array(),
			array(
				'@OWNER_ID' => $arResult['ID'],
				'=SET_ID' => 0,
				'=TYPE' => CCatalogProductSet::TYPE_GROUP
			),
			false,
			false,
			array('ID', 'OWNER_ID')
		);
		if ($arSet = $rsSets->Fetch())
		{
			$arResult['OFFER_GROUP'] = true;
		}
	}
}

$arResult['SKU_PROPS'] = $arSKUPropList;


$arOfferId = array();
$arCustomJS = array();
foreach ($arResult["OFFERS"] as $key => $offer)
{
	$keys = array_keys($offer["PRICES"]);

    $arOfferId[] = $offer["ID"];

    $arResult['OFFERS'][$offer["ID"]]["ID"] = $offer["ID"];

    foreach ($arParams["OFFER_TREE_PROPS"] as $offerTreeProps)
    {
        if (is_array($offer["PROPERTIES"][$offerTreeProps]))
        {
            $arResult['OFFERS'][$offer["ID"]]["FILTER_PROPS"][$offerTreeProps]["ID"] = $offer["PROPERTIES"][$offerTreeProps]["ID"];
            $arResult['OFFERS'][$offer["ID"]]["FILTER_PROPS"][$offerTreeProps]["NAME"] = $offer["PROPERTIES"][$offerTreeProps]["NAME"];
            $arResult['OFFERS'][$offer["ID"]]["FILTER_PROPS"][$offerTreeProps]["CODE"] = $offer["PROPERTIES"][$offerTreeProps]["CODE"];
            $arResult['OFFERS'][$offer["ID"]]["FILTER_PROPS"][$offerTreeProps]["VALUE"] = $offer["PROPERTIES"][$offerTreeProps]["VALUE"];
            $arResult['OFFERS'][$offer["ID"]]["FILTER_PROPS"][$offerTreeProps]["VALUE_ENUM_ID"] = $offer["PROPERTIES"][$offerTreeProps]["VALUE_ENUM_ID"];
        }
    }

	$arResult['OFFERS'][$offer["ID"]]["FILTER_PROPS"]["PRICE"] = $offer["PRICES"][$keys[0]];

    $arResult['OFFERS'][$offer["ID"]]["BUY_URL"] = $offer["BUY_URL"];
    $arResult['OFFERS'][$offer["ID"]]["ADD_URL"] = $offer["ADD_URL"];
    $arResult['OFFERS'][$offer["ID"]]["PHOTO"] = $offer["MORE_PHOTO"];

    unset($arResult["OFFERS"][$key]);
}

$selected = false;
if (!empty($arResult["FILTERED_OFFERS_ID"])){
	$selected = end($arResult["FILTERED_OFFERS_ID"][$arResult["ID"]]);
}
if ($selected) {
	$arResult["OFFER_ID_SELECTED"] = $selected;
}

if (empty($arResult["OFFER_ID_SELECTED"])){
	$arResult["OFFER_ID_SELECTED"] = current($arResult["OFFERS"])["ID"];
}

?>