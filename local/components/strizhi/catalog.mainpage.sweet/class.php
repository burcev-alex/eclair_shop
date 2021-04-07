<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;


Loader::includeModule('iblock');

class StrizhiSweetCatalogComponent extends \CBitrixComponent
{
    const IBLOCK_ID = 4;
    const SECTION = 41;

    private $sections = [];
    private $sectionIDs = [];
    private $sectionCode;
    private $section;

    private function getSectionData(){
        $sections = [];
        $filter = [
            'IBLOCK_ID' => self::IBLOCK_ID,
            'ID' => self::SECTION,
            //  'ACTIVE' => 'Y'
        ];
       /* $dbSections = \Bitrix\Iblock\SectionTable::getList(
            [
                'order' => ['SORT' => 'ASC'],
                'select' => ['*'],
                'filter' => $filter
            ]
        );

        $parent = [];
        if($sec = $dbSections->Fetch()){
            $parent = [
                'NAME' => $sec['NAME'],
                'ID' => $sec['ID'],
                'CODE' => $sec['CODE'],
                'LEFT_MARGIN' => $sec['LEFT_MARGIN'],
                'RIGHT_MARGIN' => $sec['RIGHT_MARGIN'],
                'DEPTH_LEVEL' => $sec['DEPTH_LEVEL'],
                'DESCRIPTION' => $sec['DESCRIPTION'],
                'URL' => '/catalog/'.$sec['CODE'].'/'
            ];

        }

        $filtersub =[
            'IBLOCK_ID' => self::IBLOCK_ID,
            '>LEFT_MARGIN' => $parent['LEFT_MARGIN'],
            '<RIGHT_MARGIN' => $parent['RIGHT_MARGIN'],
            '>DEPTH_LEVEL' => $parent['DEPTH_LEVEL']
        ];

        if(strlen($this->sectionCode)>0){
            $filtersub["CODE"] =  $this->sectionCode;
        }

        $sections = [];
        $dbSubSections = \Bitrix\Iblock\SectionTable::getList(
            [
                'order' => ['SORT' => 'ASC'],
                'select' => ['*'],
                'filter' => $filtersub
            ]
        );
        while($subSec = $dbSubSections->Fetch()){

            $sections["SECTIONS"][$subSec['ID']] = [
                'NAME' => $subSec['NAME'],
                'ID' => $subSec['ID'],
                'CODE' => $subSec['CODE'],
                'DESCRIPTION' => $subSec['DESCRIPTION'],
                'URL' => '/catalog/'.$subSec['CODE'].'/'
            ];
            //SEO tags
            $ipropSectionValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(self::IBLOCK_ID, $subSec['ID']);
            $arSEO = $ipropSectionValues->getValues();
            $sections["SECTIONS"][$subSec['ID']]['SEO'] = $arSEO;
        }*/

         $cacheID = 'sec'.$this->sectionCode;
         $cacheLifetime = 86400;
         $cachePath = '/'.$cacheID;

         $obCache = new CPHPCache();
         if( $obCache->InitCache($cacheLifetime,$cacheID,$cachePath) )
         {
             $sections = $obCache->GetVars();

         }
         elseif( $obCache->StartDataCache()  )
         {
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
                 'NAME' => $sec['NAME'],
                 'ID' => $sec['ID'],
                 'CODE' => $sec['CODE'],
                 'LEFT_MARGIN' => $sec['LEFT_MARGIN'],
                 'RIGHT_MARGIN' => $sec['RIGHT_MARGIN'],
                 'DEPTH_LEVEL' => $sec['DEPTH_LEVEL'],
                 'DESCRIPTION' => $sec['DESCRIPTION'],
                 'URL' => '/catalog/'.$sec['CODE'].'/'
             ];

         }

         $filtersub =[
             'IBLOCK_ID' => self::IBLOCK_ID,
             '>LEFT_MARGIN' => $parent['LEFT_MARGIN'],
             '<RIGHT_MARGIN' => $parent['RIGHT_MARGIN'],
             '>DEPTH_LEVEL' => $parent['DEPTH_LEVEL']
         ];

         if( $this->sectionCode){
             $filtersub["CODE"] =  $this->sectionCode;
         }


             $dbSubSections = \Bitrix\Iblock\SectionTable::getList(
                 [
                     'order' => ['SORT' => 'ASC'],
                     'select' => ['*'],
                     'filter' => $filtersub
                 ]
             );
             while($subSec = $dbSubSections->Fetch()){

                 $sections["SECTIONS"][$subSec['ID']] = [
                     'NAME' => $subSec['NAME'],
                     'ID' => $subSec['ID'],
                     'CODE' => $subSec['CODE'],
                     'DESCRIPTION' => $subSec['DESCRIPTION'],
                     'URL' => '/catalog/'.$subSec['CODE'].'/'
                 ];
                 //SEO tags
                 $ipropSectionValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(self::IBLOCK_ID, $subSec['ID']);
                 $arSEO = $ipropSectionValues->getValues();
                 $sections["SECTIONS"][$subSec['ID']]['SEO'] = $arSEO;
             }


             $obCache->EndDataCache($sections);
         }

       $this->arResult["SECTIONS"] = $this->sections =  $sections["SECTIONS"];
        foreach($sections["SECTIONS"] as $section){
            $this->sectionIDs[] = $section['ID'];

        }


    }

    private function getData(){

        $this->getSectionData();
        $this->getElements();

    }

    private function getElements(){

        $filter = [
            'IBLOCK_ID' => self::IBLOCK_ID,
            'IBLOCK_SECTION_ID' =>  $this->sectionIDs,
            'ACTIVE' => 'Y'

        ];

     /*   $dbElements = \Bitrix\Iblock\ElementTable::getList(
            [
                'order' => ['SORT' => 'ASC'],
                'select' => ['*' ],
                'filter' => $filter,

            ]
        );
        $elements = [];

        while($el = $dbElements->Fetch()){

            $dbProductPrice = CPrice::GetListEx(
                array(),
                array("PRODUCT_ID" => $el['ID']),
                false,
                false,
                array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
            );
            $dbPrice = $dbProductPrice->fetch();
            $sku = $this->getSKU($el["ID"]);

            $elements[$el['IBLOCK_SECTION_ID']][$el['ID']] =  [
                'NAME' => $el['NAME'],
                'PRICE' => number_format($sku["PRICE"],0,',',' '),
                'PRICE_FORMAT' => $sku["PRICE"],
                'ID' => $el['ID'],
                'URL' => '/catalog/'.$this->arResult["SECTIONS"][$el['IBLOCK_SECTION_ID']]["CODE"].'/'.$el['CODE'].'/',
                'PICTURE' => CFile::GetByID($el['PREVIEW_PICTURE'])->fetch(),
                'TEASER' => $el['PREVIEW_TEXT'],
                'PROPS' => $el,
                'OFFERID' =>$sku["ID"]
            ];
            if($el['PREVIEW_PICTURE']){
                $fileArr = CFile::GetByID($el['PREVIEW_PICTURE'])->fetch();
                $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                $elements[$el['IBLOCK_SECTION_ID']][$el['ID']]['PICTURE'] = $fileResArr;
            }
            else {
                if($el['DETAIL_PICTURE']){
                    $fileArr = CFile::GetByID($el['DETAIL_PICTURE'])->fetch();
                    $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                    $elements[$el['IBLOCK_SECTION_ID']][$el['ID']]['PICTURE'] = $fileResArr;
                }
            }

        }*/
       $obCache = new CPHPCache();
        $cacheLifetime = 86400;
        $cacheID = 'AllItemsIDs_'.$this->sectionCode;
        $cachePath = '/'.$cacheID;

        if( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) )
        {
            $elements = $obCache->GetVars();

        }
        elseif( $obCache->StartDataCache()  )
        {


            $dbElements = \Bitrix\Iblock\ElementTable::getList(
                [
                    'order' => ['SORT' => 'ASC'],
                    'select' => ['*' ],
                    'filter' => $filter,

                ]
            );
            $elements = [];

            while($el = $dbElements->Fetch()){

                $dbProductPrice = CPrice::GetListEx(
                    array(),
                    array("PRODUCT_ID" => $el['ID']),
                    false,
                    false,
                    array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
                );
                $dbPrice = $dbProductPrice->fetch();
                $sku = $this->getSKU($el["ID"]);

                $elements[$el['IBLOCK_SECTION_ID']][$el['ID']] =  [
                    'NAME' => $el['NAME'],
                    'PRICE' => number_format($sku["PRICE"],0,',',' '),
                    'PRICE_FORMAT' => $sku["PRICE"],
                    'ID' => $el['ID'],
                    'URL' => '/catalog/'.$this->arResult["SECTIONS"][$el['IBLOCK_SECTION_ID']]["CODE"].'/'.$el['CODE'].'/',
                    'PICTURE' => CFile::GetByID($el['PREVIEW_PICTURE'])->fetch(),
                    'TEASER' => $el['PREVIEW_TEXT'],
                    'PROPS' => $el,
                    'OFFERID' =>$sku["ID"]
                ];
                if($el['PREVIEW_PICTURE']){
                    $fileArr = CFile::GetByID($el['PREVIEW_PICTURE'])->fetch();
                    $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                    $elements[$el['IBLOCK_SECTION_ID']][$el['ID']]['PICTURE'] = $fileResArr;
                }
                else {
                    if($el['DETAIL_PICTURE']){
                        $fileArr = CFile::GetByID($el['DETAIL_PICTURE'])->fetch();
                        $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                        $elements[$el['IBLOCK_SECTION_ID']][$el['ID']]['PICTURE'] = $fileResArr;
                    }
                }

            }

            $obCache->EndDataCache($elements);
        }

        foreach($this->arResult["SECTIONS"] as &$section){
            if(isset($elements[$section["ID"]])){
                $section["ELEMENTS"] = $elements[$section["ID"]];
            }
        }


    }

    private function getSKU($productID){
        $result = [];
        if(CCatalogSKU::IsExistOffers($productID)){
            $offers = CCatalogSKU::getOffersList($productID);
            foreach($offers[$productID] as $offer){

                $result[] = CCatalogProduct::GetByID($offer["ID"]);
            }
        }

        $IDs = [];
        if(count($result) > 1){
            foreach($result as $res){
                $IDs[] =    $res["ID"];
            }
        }
        else {
            $IDs[] =$productID;
        }
        $dbProductPrice = CPrice::GetListEx(
            array(),
            array("PRODUCT_ID" => $IDs),
            false,
            false,
            array( "PRICE","PRODUCT_ID" )
        );
        $prices = [];
        while($pr = $dbProductPrice->fetch()){
            $prices[$pr["PRODUCT_ID"]] = $pr["PRICE"];
        }
        $key = array_keys($prices, min($prices));
        if(isset($key[0])){
            $ID = $key[0];
        }
        else {
            $ID = $productID;
        }

       return ["ID"=>$ID,"PRICE"=>min($prices)];
    }


    function executeComponent()
    {

        p2log(date('d.m.Y H:i:s'),'elements');
        p2log($_REQUEST['SECTION'],'elements');

        if(isset($_REQUEST['SECTION'])){
                $this->sectionCode = $_REQUEST['SECTION'];

        }
        $this->arResult["REQUEST"]=$_REQUEST;
        $this->getData();

        $this->includeComponentTemplate();
    }
}