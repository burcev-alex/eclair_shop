<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loader::includeModule('iblock');

class StrizhiCatalogComponent extends \CBitrixComponent
{
    const IBLOCK_ID = 4;

    private $sections = [];

    private function getData(){
        $dbSections = \Bitrix\Iblock\SectionTable::getList(
        [
            'order' => ['SORT' => 'ASC'],
            'select' => ['*'],
            'filter' => [
                'IBLOCK_ID' => self::IBLOCK_ID,
                'ACTIVE' => 'Y'
            ]
        ]
        );
        while($sec = $dbSections->Fetch()){
            $this->arResult["SECTIONS"][$sec['ID']] = [
                'NAME' => $sec['NAME'],
                'ID' => $sec['ID'],
                'URL' => '/catalog/'.$sec['CODE'].'/',

            ];
        }

        $this->sections = $this->arResult["SECTIONS"];

        $this->getElements();

    }

    private function getElements(){

        $dbElements = \Bitrix\Iblock\ElementTable::getList(
            [
                'order' => ['SORT' => 'ASC'],
                'select' => ['*'],
                'filter' => [
                    'IBLOCK_ID' => self::IBLOCK_ID,
                    'ACTIVE' => 'Y'

                ]
            ]
        );
        while($el = $dbElements->Fetch()){
            $this->arResult["SECTIONS"][$el['IBLOCK_SECTION_ID']]["ELEMENTS"][$el['ID']] =  [
               'NAME' => $el['NAME'],
                'ID' => $el['ID'],
                'URL' => '/catalog'.$this->sections[$el['IBLOCK_SECTION_ID']['CODE']].'/'.$el['CODE'].'/',
                'PICTURE' => CFile::GetByID($el['PREVIEW_PICTURE'])->fetch(),
                'TEASER' => $el['PREVIEW_TEXT']
            ];
            if($el['PREVIEW_PICTURE']){
                $fileArr = CFile::GetByID($el['PREVIEW_PICTURE'])->fetch();
                $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                $this->arResult["SECTIONS"][$el['IBLOCK_SECTION_ID']]["ELEMENTS"][$el['ID']]['PICTURE'] = $fileResArr;
            }
        }

    }


    function executeComponent()
    {

        $this->getData();

        $this->includeComponentTemplate();
    }
}