<?php
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if (!empty($arResult['ITEMS']))
{
	$arEmptyPreview = false;
	$strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
	if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
	{
		$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
		if (!empty($arSizes))
		{
			$arEmptyPreview = array(
				'SRC' => $strEmptyPreview,
				'WIDTH' => intval($arSizes[0]),
				'HEIGHT' => intval($arSizes[1])
			);
		}
		unset($arSizes);
	}

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
		if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']) && 'Y' == $arParams['PRODUCT_DISPLAY_MODE'])
		{
			$arSKUPropList = CIBlockPriceTools::getTreeProperties(
				$arSKU,
				$arParams['OFFER_TREE_PROPS'],
				array(
					'PICT' => $arEmptyPreview,
					'NAME' => '-'
				)
			);
			$arNeedValues = array();
			CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);

			$arSKUPropIDs = array_keys($arSKUPropList);
			if (empty($arSKUPropIDs)) {
				$arParams['PRODUCT_DISPLAY_MODE'] = 'N';
			}
			else {
				$arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);
			}
		}
	}

	$arNewItemsList = array();
	$arItem["ADDITIONAL_PICTURES"] = [];
	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$productPhotoSlider = [];
		$arItem["ADDITIONAL_PICTURES"] = $productPhotoSlider;

		$arItem['CHECK_QUANTITY'] = false;
		if (!isset($arItem['CATALOG_MEASURE_RATIO']))
			$arItem['CATALOG_MEASURE_RATIO'] = 1;
		if (!isset($arItem['CATALOG_QUANTITY']))
			$arItem['CATALOG_QUANTITY'] = 0;
		$arItem['CATALOG_QUANTITY'] = (
			0 < $arItem['CATALOG_QUANTITY'] && is_float($arItem['CATALOG_MEASURE_RATIO'])
			? floatval($arItem['CATALOG_QUANTITY'])
			: intval($arItem['CATALOG_QUANTITY'])
		);
		$arItem['CATALOG'] = false;
		if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
			$arItem['CATALOG_SUBSCRIPTION'] = 'N';

		CIBlockPriceTools::getLabel($arItem, $arParams['LABEL_PROP']);

		$productPictures = CIBlockPriceTools::getDoublePicturesForItem($arItem, $arParams['ADD_PICT_PROP']);
		if (empty($productPictures['PICT']))
			$productPictures['PICT'] = $arEmptyPreview;
		if (empty($productPictures['SECOND_PICT']))
			$productPictures['SECOND_PICT'] = $productPictures['PICT'];

		$arItem['PREVIEW_PICTURE'] = $productPictures['PICT'];
		$arItem['PREVIEW_PICTURE_SECOND'] = $productPictures['SECOND_PICT'];
		$arItem['SECOND_PICT'] = true;
		$arItem['PRODUCT_PREVIEW'] = $productPictures['PICT'];
		$arItem['PRODUCT_PREVIEW_SECOND'] = $productPictures['SECOND_PICT'];

        if (!empty($arItem["OFFERS"])) {

            $selected = false;
            if (!empty($arResult["FILTERED_OFFERS_ID"])){
                $selected = end($arResult["FILTERED_OFFERS_ID"][$arItem["ID"]]);
            }
            if ($selected)
                $arItem["OFFER_ID_SELECTED"] = $selected;

            if (empty($arItem["OFFER_ID_SELECTED"])){
                $arItem["OFFER_ID_SELECTED"] = current($arItem["OFFERS"])["ID"];
            }

            $curItem = array();
            $colors = array();
            $sizes = array();
	        $orientation = array();
            $arOfferId = array();
            foreach ($arItem["OFFERS"] as $offer){
                $arOfferId[] = $offer['ID'];
                if(IntVal($offer['PRICES']['BASE']['VALUE']) > 0){
	                $arItem["OFFER_ID_SELECTED"] = $offer['ID'];
                }
            }
	        $arOfferId = array_unique($arOfferId);

            $massForIdAndSort = [];
            $priceOffer = [];
            foreach ($arItem["OFFERS"] as $k => $offer){
				$importantValue = 100;

                $massForIdAndSort[] = [
                    "ID_OFER" => $offer["ID"],
                    "SORT" => $importantValue,
                    "RAZMER" => $offer["PROPERTIES"]["RAZMER"]["VALUE"],
                    "SIZE_FILTER" => $offer["PROPERTIES"]["RAZMER"]["VALUE_ENUM_ID"],
                    "COLOR_MAIN_FILTER" => $offer["PROPERTIES"]["TSVET_DLYA_OTBORA"]["VALUE_ENUM_ID"],
                ];

                $oneOffer = array(
					"IN_FAV" => "N"
				);

                $keys = array_keys($offer["PRICES"]);
                if ($offer["PRICES"][$keys[0]]) {
                    $price[$offer["ID"]] = round($offer["PRICES"][$keys[0]]["VALUE_VAT"], 2);
                }
	            $priceOffer[$offer["ID"]] = round($offer["PRICES"][$keys[0]]["VALUE_VAT"], 4);

	            if ($offer["PROPERTIES"]["RAZMER"]["VALUE"]) {
		            $sizes[$offer["ID"]] = str_replace([","], ["."], $offer["PROPERTIES"]["RAZMER"]["VALUE"]);
	            }

	            if ($offer["PROPERTIES"]["ORIENTATSIYA"]["VALUE"]) {
		            $orientation[$offer["ID"]] = $offer["PROPERTIES"]["ORIENTATSIYA"]["VALUE"];
	            }

                $sort[$offer["ID"]] = 100;

                $oneOffer["GALLERY"][] = $offer["DETAIL_PICTURE"]["SRC"] ? $offer["DETAIL_PICTURE"]["SRC"] : $strEmptyPreview;
                if (!empty($offer["PROPERTIES"]["MORE_PHOTO"])){
                    foreach ($offer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $picKey => $photo){
                        $oneOffer["GALLERY"][] = CFile::GetPath($photo);
                    }
                }

	            $oneOffer["GALLERY"] = array_merge($oneOffer["GALLERY"], $productPhotoSlider);

                foreach ($arParams['OFFER_TREE_PROPS'] as $codeProp) {
	                $arValueProp = [];
	                $arValueProp["ID"] = $offer["PROPERTIES"][$codeProp]["ID"];
	                $arValueProp["NAME"] = $offer["PROPERTIES"][$codeProp]["NAME"];
	                $arValueProp["CODE"] = $offer["PROPERTIES"][$codeProp]["CODE"];
	                $arValueProp["VALUE"] = $offer["PROPERTIES"][$codeProp]["VALUE"];
	                $arValueProp["VALUE_ENUM_ID"] = $offer["PROPERTIES"][$codeProp]["VALUE_ENUM_ID"];

	                $oneOffer["FILTER_PROPS"][$codeProp] = $arValueProp;
                }

	            // перевод валюты в UAH
	            $baseSiteCurrency = \CSaleLang::GetLangCurrency(SITE_ID);
	            $offer["PRICES"][$keys[0]]["VALUE"] = \Bitrix\Sale\PriceMaths::roundPrecision(\CCurrencyRates::ConvertCurrency($offer["PRICES"][$keys[0]]["VALUE"], $offer["PRICES"][$keys[0]]["CURRENCY"], $baseSiteCurrency));

                $oneOffer["FILTER_PROPS"]["PRICE"] = $offer["PRICES"][$keys[0]];
				$oneOffer["ID"] = $offer["ID"];

				if(IntVal($offer["ID"]) > 0) {
					$curItem["OFFERS"][$offer["ID"]] = $oneOffer;
				}
            }

	        // если выбрана сортировка по цене, тогда меняем подрядок вывода ТП
	        if($_REQUEST["ORDER"] == "PRICE_MAX"){
		        $sortValue = max($priceOffer);
		        $sortKey = array_search($sortValue, $priceOffer);
		        $sort[$sortKey] = "10000";
	        }
	        else if($_REQUEST["ORDER"] == "PRICE_MIN"){
		        $sortValue = min($priceOffer);
		        $sortKey = array_search($sortValue, $priceOffer);
		        $sort[$sortKey] = "10000";
			}
			
			#p($massForIdAndSort);

	        if(IntVal($sortKey) > 0){
	        	foreach($massForIdAndSort as $k=>$massItem){
	        		if($massItem["ID_OFER"] == $sortKey){
				        $massForIdAndSort[$k]["SORT"] = 10000;
			        }
		        }
	        }

            usort($massForIdAndSort, function($a, $b){
                return ($a['SORT'] > $b['SORT']);
            });

			$arItem["OFFERS_SORT"] = $massForIdAndSort;

            $VAZHN_OFERS[] = [
            	"ID_PROD" => $arItem["ID"],
	            "ID_VAZN_OFER" => $massForIdAndSort[count($massForIdAndSort)-1]["ID_OFER"],
			];

            $curItem["COLORS"] =  $colors;
            $curItem["SIZES"] = $sizes;
            $curItem["ORIENTATION"] = $orientation;
            $curItem["PRICE"] = $price;
            $curItem["SORT"] = $sort;
            $curItem["OFFER_ID_SELECTED"] = $arItem["OFFER_ID_SELECTED"];

            //$curItem["OFFERS"]=array_reverse($curItem["OFFERS"],true);

	        $maxPictures = 0;
            if ($arItem["OFFER_ID_SELECTED"]) {
                foreach ($arItem["OFFERS"] as $offer) {
                    if ($offer["ID"] == $arItem["OFFER_ID_SELECTED"]) {
                        $arItem["FIRST_PICTURE"] =  $offer["DETAIL_PICTURE"] ? $offer["DETAIL_PICTURE"] : array('SRC' => $strEmptyPreview);
                        //$maxPictures = 5;
                        foreach ($offer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $offerKey => $pictureId) {
                            if ($offerKey == 0 && empty($offer["DETAIL_PICTURE"])){
                                $arItem["FIRST_PICTURE"]["SRC"] = CFile::GetPath($pictureId);
                                $maxPictures++;
                                continue;
                            }
                            $arItem["ADDITIONAL_PICTURES"][] = CFile::GetPath($pictureId);
                        }

                        $arItem["OFFER_PROPS"][$offer["PROPERTIES"]["TSVET_TESMY_SAYT"]["NAME"]] = $offer["PROPERTIES"]["TSVET_TESMY_SAYT"]["VALUE"];
                        $arItem["OFFER_PROPS"][$offer["PROPERTIES"]["TSVET_ZUBEV_SAYT"]["NAME"]] = $offer["PROPERTIES"]["TSVET_ZUBEV_SAYT"]["VALUE"];
                        $arItem["OFFER_PROPS"][$offer["PROPERTIES"]["TSVET_BEGUNKA_SAYT"]["NAME"]] = $offer["PROPERTIES"]["TSVET_BEGUNKA_SAYT"]["VALUE"];
                        $arItem["OFFER_PROPS"][$offer["PROPERTIES"]["RAZMER"]["NAME"]] = $offer["PROPERTIES"]["RAZMER"]["VALUE"];
                    }
                }
            } else {
                $arItem["FIRST_PICTURE"] =  current($arItem["OFFERS"])["DETAIL_PICTURE"];
            }

            if(strlen($arItem["FIRST_PICTURE"]["SRC"]) == 0){
	            $arItem["FIRST_PICTURE"] = $arEmptyPreview;
            }

	        $arResult["JS_PARAMS"]["SKU_PROPS"] = $arSKUPropList;

            $arResult["JS_PARAMS"]["ITEMS"][$arItem["ID"]] = $curItem;
        }
        //END

		if ($arResult['MODULES']['catalog'])
		{
			$arItem['CATALOG'] = true;
			if (!isset($arItem['CATALOG_TYPE']))
				$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
			if (
				(CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
				&& !empty($arItem['OFFERS'])
			)
			{
				$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
			}
			switch ($arItem['CATALOG_TYPE'])
			{
				case CCatalogProduct::TYPE_SET:
					$arItem['OFFERS'] = array();
					$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
					break;
				case CCatalogProduct::TYPE_SKU:
					break;
				case CCatalogProduct::TYPE_PRODUCT:
				default:
					$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
					break;
			}
		}
		else
		{
			$arItem['CATALOG_TYPE'] = 0;
			$arItem['OFFERS'] = array();
		}

		
		if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
		{
			if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
			{
				$arMatrixFields = $arSKUPropKeys;
				$arMatrix = array();

				$arNewOffers = array();
				$boolSKUDisplayProperties = false;
				$arItem['OFFERS_PROP'] = false;
				$arItem['SKU_TREE_VALUES'] = array();

				$arDouble = array();
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					#echo '<pre>'.print_r($arOffer['DISPLAY_PROPERTIES'], true).'</pre>';
					$arOffer['ID'] = (int)$arOffer['ID'];
					if (isset($arDouble[$arOffer['ID']]))
						continue;
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
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
							}
							elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
							}
							$arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
						}
						$arRow[$strOneCode] = $arCell;
					}
					$arMatrix[$keyOffer] = $arRow;

					unset($arRow);

					CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']);

					CIBlockPriceTools::setRatioMinPrice($arOffer, false);

					$offerPictures = CIBlockPriceTools::getDoublePicturesForItem($arOffer, $arParams['OFFER_ADD_PICT_PROP']);
					$arOffer['OWNER_PICT'] = empty($offerPictures['PICT']);
					$arOffer['PREVIEW_PICTURE'] = false;
					$arOffer['PREVIEW_PICTURE_SECOND'] = false;
					$arOffer['SECOND_PICT'] = true;
					if (!$arOffer['OWNER_PICT'])
					{
						if (empty($offerPictures['SECOND_PICT']))
							$offerPictures['SECOND_PICT'] = $offerPictures['PICT'];
						$arOffer['PREVIEW_PICTURE'] = $offerPictures['PICT'];
						$arOffer['PREVIEW_PICTURE_SECOND'] = $offerPictures['SECOND_PICT'];
					}
					if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
						unset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]);

					$arDouble[$arOffer['ID']] = true;
					$arNewOffers[$keyOffer] = $arOffer;
				}
				unset($keyOffer, $arOffer);
				$arItem['OFFERS'] = $arNewOffers;

				$arUsedFields = array();
			$arSortFields = array();

			$matrixKeys = array_keys($arMatrix);
			#echo '<pre>'.print_r($arMatrix, true).'</pre>';
			foreach ($arSKUPropIDs as $propkey => $propCode)
			{
				$boolExist = $arMatrixFields[$propCode];
				foreach ($matrixKeys as $keyOffer)
				{
					if ($boolExist)
					{
						if (!isset($arItem['OFFERS'][$keyOffer]['TREE'])){
							$arItem['OFFERS'][$keyOffer]['TREE'] = array();
						}

						$propId = $arSKUPropList[$propCode]['ID'];
						$value = $arMatrix[$keyOffer][$propCode]['VALUE'];
						#echo '<pre>'.print_r($arMatrix[$keyOffer], true).'</pre>';
						#echo '<pre>'.print_r($propCode, true).'</pre>';
						
						if (!isset($arItem['SKU_TREE_VALUES'][$propId])){
							$arItem['SKU_TREE_VALUES'][$propId] = array();
						}

						$arItem['SKU_TREE_VALUES'][$propId][$value] = true;
						$arItem['OFFERS'][$keyOffer]['TREE']['PROP_'.$propId] = $value;
						$arItem['OFFERS'][$keyOffer]['SKU_SORT_'.$propCode] = $arMatrix[$keyOffer][$propCode]['SORT'];
						
						$arUsedFields[$propCode] = true;
						$arSortFields['SKU_SORT_'.$propCode] = SORT_NUMERIC;
						
						unset($value, $propId);
					}
					else
					{
						unset($arMatrix[$keyOffer][$propCode]);
					}
				}
				unset($keyOffer);
			}
				
			unset($propkey, $propCode);
			unset($matrixKeys);
			$arItem['OFFERS_PROP'] = $arUsedFields;
			$arItem['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

			Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

				

				$arMatrix = array();
				$intSelected = -1;
				$arItem['MIN_PRICE'] = false;
				$arItem['MIN_BASIS_PRICE'] = false;
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					if (empty($arItem['MIN_PRICE']))
					{
						if ($arItem['OFFER_ID_SELECTED'] > 0) {
							$foundOffer = ($arItem['OFFER_ID_SELECTED'] == $arOffer['ID']);
						}
						else {
							$foundOffer = $arOffer['CAN_BUY'];
						}
						if ($foundOffer)
						{
							$intSelected = $keyOffer;
							$arItem['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
							$arItem['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
						}
						unset($foundOffer);
					}

					$arSKUProps = false;
					if (!empty($arOffer['DISPLAY_PROPERTIES']))
					{
						$boolSKUDisplayProperties = true;
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

					$arOneRow = array(
						'ID' => $arOffer['ID'],
						'NAME' => $arOffer['~NAME'],
						'TREE' => $arOffer['TREE'],
						'DISPLAY_PROPERTIES' => $arSKUProps,
						'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
						'BASIS_PRICE' => $arOffer['MIN_PRICE'],
						'SECOND_PICT' => $arOffer['SECOND_PICT'],
						'OWNER_PICT' => $arOffer['OWNER_PICT'],
						'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
						'PREVIEW_PICTURE_SECOND' => $arOffer['PREVIEW_PICTURE_SECOND'],
						'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
						'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
						'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
						'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
						'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
						'CAN_BUY' => $arOffer['CAN_BUY'],
					);
					$arMatrix[$keyOffer] = $arOneRow;
				}
				if (-1 == $intSelected)
				{
					$intSelected = 0;
					$arItem['MIN_PRICE'] = (isset($arItem['OFFERS'][0]['RATIO_PRICE']) ? $arItem['OFFERS'][0]['RATIO_PRICE'] : $arItem['OFFERS'][0]['MIN_PRICE']);
					$arItem['MIN_BASIS_PRICE'] = $arItem['OFFERS'][0]['MIN_PRICE'];
				}
				if (!$arMatrix[$intSelected]['OWNER_PICT'])
				{
					$arItem['PREVIEW_PICTURE'] = $arMatrix[$intSelected]['PREVIEW_PICTURE'];
					$arItem['PREVIEW_PICTURE_SECOND'] = $arMatrix[$intSelected]['PREVIEW_PICTURE_SECOND'];
				}

				

				$arItem['JS_OFFERS'] = $arMatrix;
				$arItem['OFFERS_SELECTED'] = $intSelected;
				$arItem['OFFERS_PROPS_DISPLAY'] = $boolSKUDisplayProperties;
			}
			else
			{
				$arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
					$arItem['OFFERS'],
					$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
				);
			}
		}
		if(empty($arItem['MIN_PRICE'])){
			foreach ($arItem['OFFERS'] as $keyOffer => $offer)
			{
				$keys = array_keys($offer["PRICES"]);
				$arItem['MIN_PRICE'] = $offer["PRICES"][$keys[0]];
				break;
			}
		}
		
		if(isset($arItem['MIN_PRICE']['CURRENCY']) && $arItem['MIN_PRICE']['CURRENCY'] != 'UAH'){
			$baseSiteCurrency = \CSaleLang::GetLangCurrency(SITE_ID);
			
	        $arItem['MIN_PRICE']["VALUE"] = \Bitrix\Sale\PriceMaths::roundPrecision(\CCurrencyRates::ConvertCurrency($arItem['MIN_PRICE']["VALUE"], $arItem['MIN_PRICE']['CURRENCY'], $baseSiteCurrency));
		}

		if (
			$arResult['MODULES']['catalog']
			&& $arItem['CATALOG']
			&&
				($arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT
				|| $arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
		)
		{
			CIBlockPriceTools::setRatioMinPrice($arItem, false);
			$arItem['MIN_BASIS_PRICE'] = $arItem['MIN_PRICE'];
		}

		if (!empty($arItem['DISPLAY_PROPERTIES']))
		{
			foreach ($arItem['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
			{
				if ('F' == $arDispProp['PROPERTY_TYPE'])
					unset($arItem['DISPLAY_PROPERTIES'][$propKey]);
			}
		}

		$arItem['LAST_ELEMENT'] = 'N';
		$arNewItemsList[$key] = $arItem;
	}

	$arNewItemsList[$key]['LAST_ELEMENT'] = 'Y';
	$arResult['ITEMS'] = $arNewItemsList;

	$arResult['SKU_PROPS'] = $arSKUPropList;
	$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;
	$arResult['IMPORTANT_OFFERS'] = $VAZHN_OFERS;

	$arResult['CURRENCIES'] = array();
	if ($arResult['MODULES']['currency'])
	{
		if ($boolConvert)
		{
			$currencyFormat = CCurrencyLang::GetFormatDescription($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
			$arResult['CURRENCIES'] = array(
				array(
					'CURRENCY' => $arResult['CONVERT_CURRENCY']['CURRENCY_ID'],
					'FORMAT' => array(
						'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
						'DEC_POINT' => $currencyFormat['DEC_POINT'],
						'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
						'DECIMALS' => $currencyFormat['DECIMALS'],
						'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
						'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
					)
				)
			);
			unset($currencyFormat);
		}
		else
		{
			$currencyIterator = CurrencyTable::getList(array(
				'select' => array('CURRENCY')
			));
			while ($currency = $currencyIterator->fetch())
			{
				$currencyFormat = CCurrencyLang::GetFormatDescription($currency['CURRENCY']);
				$arResult['CURRENCIES'][] = array(
					'CURRENCY' => $currency['CURRENCY'],
					'FORMAT' => array(
						'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
						'DEC_POINT' => $currencyFormat['DEC_POINT'],
						'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
						'DECIMALS' => $currencyFormat['DECIMALS'],
						'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
						'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
					)
				);
			}
			unset($currencyFormat, $currency, $currencyIterator);
		}
	}
}

$arResult["JS_PARAMS"]["IBLOCK_SECTION_ID"] = $arResult['IBLOCK_SECTION_ID'];
$arResult["JS_PARAMS"]["IS_AUTHORIZED"] = $USER->IsAuthorized();
$arResult["JS_PARAMS"]["SITE_ID"] = SITE_ID;