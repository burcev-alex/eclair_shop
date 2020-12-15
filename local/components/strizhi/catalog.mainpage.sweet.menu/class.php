<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;


Loader::includeModule('iblock');

class StrizhiSweetMenuCatalogComponent extends \CBitrixComponent
{
    const IBLOCK_ID = 4;
    const SECTION = 41;

    function executeComponent()
    {

        $menuArr = [];

        $filter = [
            'IBLOCK_ID' => self::IBLOCK_ID,
            'ID' => self::SECTION,
            //  'ACTIVE' => 'Y'
        ];
        $dbSections = \Bitrix\Iblock\SectionTable::getList(
            [
                'order' => ['SORT' => 'ASC'],
                'select' => ['*'],
                'filter' => $filter
            ]
        );

        $parent = [];
        if($sec = $dbSections->Fetch()){

            $parent = [
                'ID' => $sec['ID'],
                'LEFT_MARGIN' => $sec['LEFT_MARGIN'],
                'RIGHT_MARGIN' => $sec['RIGHT_MARGIN'],
                'DEPTH_LEVEL' => $sec['DEPTH_LEVEL'],
            ];

        }
        $filtersub =[
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => self::IBLOCK_ID,
            '>LEFT_MARGIN' => $parent['LEFT_MARGIN'],
            '<RIGHT_MARGIN' => $parent['RIGHT_MARGIN'],
            '>DEPTH_LEVEL' => $parent['DEPTH_LEVEL']
        ];
        $dbSubSections = \Bitrix\Iblock\SectionTable::getList(
            [
                'order' => ['SORT' => 'ASC'],
                'select' => ['*'],
                'filter' => $filtersub
            ]
        );
        while($subSec = $dbSubSections->Fetch()){

            $menuArr[] = [
                $subSec['NAME'],
                '/catalog/'.$subSec['CODE'].'/',
                ['/catalog/'.$subSec['CODE'].'/'],
                [
                    'FROM_IBLOCK' => 1,
                    'IS_PARENT' => null,
                    'DEPTH_LEVEL' => 1
                ]
            ];
        }

       return $menuArr;
    }
}