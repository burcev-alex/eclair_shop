<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loader::includeModule('iblock');

class StrizhiCatalogAddingsComponent extends \CBitrixComponent
{
    const IBLOCK_ID = 4;
    const SECTION_ID = [27,34,35];

    private $sections = [];

    private function getData(){

        $arSelect = Array('*');
        $arFilter = Array(
            'IBLOCK_ID' => self::IBLOCK_ID,
            'IBLOCK_SECTION_ID' => self::SECTION_ID,
            'ACTIVE' => 'Y'
        );
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']] = $arFields;
            $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']]["ADD_URL"] = $arFields["DETAIL_PAGE_URL"].'?action=ADD2BASKET&id='.$arFields["ID"].'&parentid='.$this->arParams['ELEMENT_ID'];
            $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']]["PARENT_ID"] = $this->arParams['ELEMENT_ID'];
            $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']]["CATALOG"] = CCatalogProduct::GetByID($arFields["ID"]);
            $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']]["PRICE"] = CPrice::GetBasePrice($arFields["ID"]);
            $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']]["PICTURE"] = [];
            if((int)$arFields['PREVIEW_PICTURE'] > 0){

                $arFile = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]);
                $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']]["FILE"] = $arFile;
                if($arFile) {
                    $this->arResult["ADDINGS"][$arFields['IBLOCK_SECTION_ID']][$arFields['ID']]["PICTURE"] = CFile::ResizeImageGet($arFields["PREVIEW_PICTURE"],["width"=>"200","height"=>"200"],BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
                }
            }
        }

        $this->arResult["FIRST_ADDINGS"] =  $this->arResult["ADDINGS"][$this->arParams['VID']];
        unset( $this->arResult["ADDINGS"][$this->arParams['VID']]);
        $this->arResult["SECOND_ADDINGS"] = $this->arResult["ADDINGS"];
    }

    function executeComponent()
    {

        $this->getData();

        $this->includeComponentTemplate();

    }
}