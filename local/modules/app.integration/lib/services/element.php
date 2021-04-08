<?php

namespace App\Integration\Services;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Section.
 */
class Element
{
    public function __construct()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
    }

    public function save($data)
    {
		\CModule::IncludeModule('iblock');
        $xmlId = $data['ID'];

        $el = new \CIBlockElement();

        if (! array_key_exists('IBLOCK_SECTION_DATA', $data)) {
            return $xmlId;
        }

        $parentSectionId = false;
        if (strlen($data['IBLOCK_SECTION_ID']) > 0) {
            $rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'XML_ID' => $data['IBLOCK_SECTION_ID']]);
            while ($arSect = $rsSect->Fetch()) {
                $parentSectionId = $arSect['ID'];
            }

            if (! $parentSectionId) {
                $rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'ID' => $data['IBLOCK_SECTION_DATA']['XML_ID']]);
                while ($arSect = $rsSect->Fetch()) {
                    $parentSectionId = $arSect['ID'];
                }
            }

            if (! $parentSectionId) {
                $rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'CODE' => $data['IBLOCK_SECTION_DATA']['CODE']]);
                while ($arSect = $rsSect->Fetch()) {
                    $parentSectionId = $arSect['ID'];
                }
            }
        }

        $arProperties = [];
        foreach ($data['PROPERTIES'] as $propertyCode => $propValues) {
            if (! array_key_exists('PROPERTY_TYPE', $propValues)) {
                foreach ($propValues as $firstValue) {
                    if ($firstValue['PROPERTY_TYPE'] == 'L') {
                        $property_enums = \CIBlockPropertyEnum::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'CODE' => $propertyCode]);
                        while ($enum_fields = $property_enums->Fetch()) {
                            if ($enum_fields['VALUE'] == $firstValue['VALUE_ENUM']) {
                                $arProperties[$propertyCode][] = $enum_fields['ID'];
                            }
                        }
                    } elseif ($firstValue['PROPERTY_TYPE'] == 'E') {
						$value = $firstValue['VALUE'];
						// TODO hard code
                        $rsBindElement = \CIBlockElement::GetList(['SORT' => 'ASC'], ['XML_ID' => $firstValue['VALUE']], false, false, ['ID']);
                        if (IntVal($rsBindElement->AffectedRowsCount()) > 0) {
                            if ($arBindElement = $rsBindElement->Fetch()) {
                                $value = $arBindElement['ID'];
                            }
                        } else {
							// TODO hard code
                            $rsBindElementOther = \CIBlockElement::GetList(['SORT' => 'ASC'], ['EXTERNAL_ID' => $firstValue['VALUE']], false, false, ['ID']);
                            if ($arBindElementOther = $rsBindElementOther->Fetch()) {
                                $value = $arBindElementOther['ID'];
                            }
                        }
                        $arProperties[$propertyCode][] = $value;
                    } else {
                        $arProperties[$propertyCode][] = $propValues['VALUE'];
                    }
                }
            } else {
                if ($propValues['PROPERTY_TYPE'] == 'L') {
                    $property_enums = \CIBlockPropertyEnum::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'CODE' => $propertyCode]);
                    while ($enum_fields = $property_enums->Fetch()) {
                        if ($enum_fields['VALUE'] == $propValues['VALUE_ENUM']) {
                            $arProperties[$propertyCode] = $enum_fields['ID'];
                        }
                    }
                } elseif ($propValues['PROPERTY_TYPE'] == 'E') {
					$arProperties[$propertyCode] = $propValues['VALUE'];
					// TODO hard code
                    $rsBindElement = \CIBlockElement::GetList(['SORT' => 'ASC'], ['XML_ID' => $propValues['VALUE']], false, false, ['ID']);
                    if (IntVal($rsBindElement->AffectedRowsCount()) > 0) {
                        while ($arBindElement = $rsBindElement->Fetch()) {
                            $arProperties[$propertyCode] = $arBindElement['ID'];
                        }
                    } else {
						// TODO hard code
                        $rsBindElementOther = \CIBlockElement::GetList(['SORT' => 'ASC'], ['EXTERNAL_ID' => $propValues['VALUE']], false, false, ['ID']);
                        while ($arBindElementOther = $rsBindElementOther->Fetch()) {
                            $arProperties[$propertyCode] = $arBindElementOther['ID'];
                        }
                    }
                } else {
                    $arProperties[$propertyCode] = $propValues['VALUE'];
                }
            }
        }

        if (count($arProperties) == 0) {
            $properties = \CIBlockProperty::GetList(['sort' => 'asc', 'name' => 'asc'], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID']]);
            while ($prop_fields = $properties->Fetch()) {
                $arProperties[$prop_fields['CODE']] = '';
            }
        }

        $arFields = [
            'ACTIVE' => $data['ACTIVE'],
            'XML_ID' => $data['XML_ID'] ? $data['XML_ID'] : $data['ID'],
            'IBLOCK_SECTION_ID' => $parentSectionId,
            'IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'],
            'NAME' => $data['NAME'],
            'CODE' => $data['CODE'],
            'SORT' => $data['SORT'],
            'PREVIEW_TEXT' => $data['PREVIEW_TEXT'],
            'PREVIEW_TEXT_TYPE' => $data['PREVIEW_TEXT_TYPE'],
            'DETAIL_TEXT' => $data['DETAIL_TEXT'],
            'DETAIL_TEXT_TYPE' => $data['DETAIL_TEXT_TYPE'],
            'TAGS' => $data['TAGS']
		];

        if(count($arProperties) > 0){
			$arFields['PROPERTY_VALUES'] = $arProperties;
		}

        if (strlen($data['PREVIEW_PICTURE']) > 0) {
            $arFields['PREVIEW_PICTURE'] = \CFile::MakeFileArray($data['PREVIEW_PICTURE']);
        }

        if (strlen($data['DETAIL_PICTURE']) > 0) {
            $arFields['DETAIL_PICTURE'] = \CFile::MakeFileArray($data['DETAIL_PICTURE']);
        }

        $ID = 0;
        if (intval($data['ID']) > 0) {
            $rsElement = \CIBlockElement::GetList(['id' => 'asc'], ['IBLOCK_ID' => $arFields['IBLOCK_ID'], 'XML_ID' => $arFields['XML_ID']]);
            while ($arElement = $rsElement->Fetch()) {
                $ID = $arElement['ID'];
            }
		}

        if ($ID > 0) {
            $res = $el->Update($ID, $arFields);
        } else {
            $ID = $el->Add($arFields);
		}
		
        if (intval($ID) == 0) {
            $xmlId = 0;
        } else {

            if(array_key_exists('PRICE', $data)){
                $arFieldsProduct = [
                    'ID' => $ID,
                    'QUANTITY' => 1000,
                    'QUANTITY_TRACE' => 'N',
                    'CAN_BUY_ZERO' => 'Y'
                ];
                \CCatalogProduct::Add($arFieldsProduct);

                $arFieldsPrice = [
                    'PRODUCT_ID' => $ID,
                    'CATALOG_GROUP_ID' => 1,
                    'PRICE' => $data['PRICE']['PRICE'],
                    'CURRENCY' => $data['PRICE']['CURRENCY']
                ];

                $resPrice = \CPrice::GetList(
                    [],
                    [
                        'PRODUCT_ID' => $ID,
                        'CATALOG_GROUP_ID' => 1,
                    ]
                );

                if ($arrPrice = $resPrice->Fetch()) {
                    \CPrice::Update($arrPrice['ID'], $arFieldsPrice);
                } else {
                    \CPrice::Add($arFieldsPrice);
                }

                \CPrice::SetBasePrice($ID, $data['PRICE']['PRICE'], $data['PRICE']['CURRENCY']);
            }
        }

        return $xmlId;
    }

    public function delete($data)
    {
        $xmlId = $data['ID'];

        $el = new \CIBlockElement();

        $ID = 0;
        $rsSect = \CIBlockElement::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'XML_ID' => $data['XML_ID'] ? $data['XML_ID'] : $data['ID']]);
        while ($arSect = $rsSect->GetNext()) {
            $ID = $arSect['ID'];
        }

        if ($ID > 0) {
            \CIBlockElement::Delete($ID);
        }

        if (intval($ID) == 0) {
            $xmlId = 0;
        }

        return $xmlId;
    }
}
