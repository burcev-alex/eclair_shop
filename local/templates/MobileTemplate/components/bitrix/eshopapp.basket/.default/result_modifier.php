<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arIds = [];
foreach ($arResult['ITEMS']['AnDelCanBuy'] as $key => $arElement) {
    $arIds[] = $arElement['ID'];
}

function p($arr)
{
    echo '<pre>'.print_r($arr, true).'</pre>';
}

foreach ($arResult['ITEMS_IMG'] as $val => $arPhoto) {
    $arFileTmp = CFile::ResizeImageGet(
        $arPhoto,
        ['width' => '110', 'height' => '110'],
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true
    );
    $arResult['ITEMS_IMG'][$val] = [
        'SRC' => $arFileTmp['src'],
        'WIDTH' => $arFileTmp['width'],
        'HEIGHT' => $arFileTmp['height'],
    ];
}

$arProperties = [];

$arResult['JS_PARAMS'] = [
    'PROPS' => [],
    'PRICE' => [],
];
foreach ($arResult['ITEMS']['AnDelCanBuy'] as $iteration => $row) {
    $offerId = $row['PRODUCT_ID'];

	$arOffer = \CIblockElement::GetList([], ['ID' => $offerId], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_CML2_LINK'])->Fetch();
	$arResult['ITEMS']['AnDelCanBuy'][$iteration]['IBLOCK_ID'] = $arOffer['IBLOCK_ID'];
    if ($arOffer['IBLOCK_ID'] == 5) {
        // нужно найти  родительский элемент
        $productId = intval($arOffer['PROPERTY_CML2_LINK_VALUE']);

		//  свойства инфоблоков
		if(!isset($arProperties[$arOffer['IBLOCK_ID']])){
			$properties = \CIBlockProperty::GetList(['id' => 'asc'], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $arOffer['IBLOCK_ID']]);
			while ($prop_fields = $properties->GetNext()) {
				$arProperties[$arOffer['IBLOCK_ID']][$prop_fields['CODE']] = $prop_fields['NAME'];
			}
		}
    } else {
        $productId = intval($arOffer['ID']);
    }

    $offers = CCatalogSKU::getOffersList($productId);

    $offerIds = [];
    foreach ($offers[$productId] as $arOffer) {
        $offerIds[] = $arOffer['ID'];
    }

    $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'CATALOG_PRICE_1'];
    $selectOther = [
        'KRUPA',
        'BUTTER',
        'PREPARED_ON',
        'SOUCE',
        'FILLING',
        'TASTE',
        'COMPLECT',
        'EGG',
        'COFFEE',
        'FILL',
        'GAS',
        'CAKES_SET',
        'PIROGI_SET',
    ];
    foreach ($selectOther as $code) {
        $arSelect[] = 'PROPERTY_'.$code;
    }

    // собрать свойства по товару участвующие в формировании ТП
    $sku = [];
    $price = [];
    $arrOffers = [];

    if (count($offerIds) > 0) {
        $rsOffers = \CIblockElement::GetList([], ['ID' => $offerIds], false, false, $arSelect);
        while ($arItem = $rsOffers->Fetch()) {
            $price[$arItem['ID']] = $arItem['CATALOG_PRICE_1'];

            foreach ($selectOther as $code) {
                if (strlen($arItem['PROPERTY_'.$code.'_VALUE']) > 0) {
                    $arrOffers[$arItem['ID']][$code] = $arItem['PROPERTY_'.$code.'_ENUM_ID'];

                    $sku[$code][$arItem['PROPERTY_'.$code.'_ENUM_ID']] = $arItem['PROPERTY_'.$code.'_VALUE'];
                }
            }
        }
    }

    $arResult['ITEMS']['AnDelCanBuy'][$iteration]['OFFERS']['PRICES'] = $price;
    $arResult['ITEMS']['AnDelCanBuy'][$iteration]['OFFERS']['PROPS'] = $sku;
	$arResult['ITEMS']['AnDelCanBuy'][$iteration]['OFFERS']['IDS'] = $offerIds;
	
    $arResult['JS_PARAMS']['PROPS'][$row['ID']] = $arrOffers;
    $arResult['JS_PARAMS']['PRICE'][$row['ID']] = $price;
}

$arResult['PROPERTIES'] = $arProperties;

$arResult['JS_PARAMS']['ITEMS'] = $arResult['ITEMS']['AnDelCanBuy'];

#p($arResult['JS_PARAMS']);

//echo '<pre>'.print_r($arResult['ITEMS']["AnDelCanBuy"], true).'</pre>';
