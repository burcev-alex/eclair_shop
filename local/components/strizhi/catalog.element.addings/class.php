<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loader::includeModule('iblock');

class StrizhiCatalogAddingsComponent extends \CBitrixComponent
{
    const IBLOCK_ID = 4;
    const SECTION_ID = 27;

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
            $this->arResult["ADDINGS"][$arFields['ID']] = $arFields;
            $this->arResult["ADDINGS"][$arFields['ID']]["ADD_URL"] = $arFields["DETAIL_PAGE_URL"].'?action=ADD2BASKET&id='.$arFields["ID"];
            if((int)$arFields['DETAIL_PICTURE'] > 0){

                $arFile = CFile::GetFileArray($arFields["DETAIL_PICTURE"]);
                if($arFile) {

                }

            }
        }
    }

    function executeComponent()
    {

        $this->getData();

        $this->includeComponentTemplate();

    }
}