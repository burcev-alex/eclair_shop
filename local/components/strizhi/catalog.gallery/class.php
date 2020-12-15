<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;


Loader::includeModule('iblock');

class StrizhiCatalogGalleryComponent extends \CBitrixComponent
{

    const IBLOCK_ID = 8;
    private $code = 'cakes';



    private function getData(){
        if(isset($this->arParams['CODE'])){
            $this->code = $this->arParams['CODE'];
        }
        $arSelect = [];
        $filter = [
            'IBLOCK_ID' => self::IBLOCK_ID,
            'CODE' => $this->code,
            'ACTIVE' => 'Y'
        ];

        $res = CIBlockElement::GetList(Array(), $filter, false, Array("nPageSize"=>50), $arSelect);
        while($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $this->arResult['FIELDS'] = $arFields;
            $this->arResult['PROPS'] = $arProps;
            if(isset($arProps['GALLERY'])){
                if(!empty($arProps['GALLERY']['VALUE'])){
                    foreach($arProps['GALLERY']['VALUE'] as $val){
                        $fileArr = CFile::GetByID($val)->fetch();
                        $path=CFile::GetPath($val);
                        $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                        $fileResArr['origin'] = $path;
                        $this->arResult['PROPS']['PICTURE'][] = $fileResArr;
                    }
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