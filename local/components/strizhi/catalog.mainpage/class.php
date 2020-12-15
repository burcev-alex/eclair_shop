<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;


Loader::includeModule('iblock');

class StrizhiCatalogComponent extends \CBitrixComponent
{
    const IBLOCK_ID = 4;
    const SECTION_ADDINGS = [27,34,35];

    private $sections = [];
    private $sectionIDs = [];

    private function getData(){

        $filter = [
            'IBLOCK_ID' => self::IBLOCK_ID,
            '!ID' => self::SECTION_ADDINGS,
            'ACTIVE' => 'Y'
        ];
        if(isset($this->arParams["IBLOCK_SECTION_ID"]) && !empty($this->arParams["IBLOCK_SECTION_ID"])){
            $filter["IBLOCK_SECTION_ID"] = $this->arParams["IBLOCK_SECTION_ID"];
        }
        if(isset($this->arParams["IBLOCK_SECTION_CODE"])){
            $filter["CODE"] = $this->arParams["IBLOCK_SECTION_CODE"];
        }

        $dbSections = \Bitrix\Iblock\SectionTable::getList(
        [
            'order' => ['SORT' => 'ASC'],
            'select' => ['*'],
            'filter' => $filter
        ]
        );

        while($sec = $dbSections->Fetch()){

            $this->sectionIDs[] = $sec['ID'];
            $this->arResult["SECTIONS"][$sec['ID']] = [
                'NAME' => $sec['NAME'],
                'ID' => $sec['ID'],
                'CODE' => $sec['CODE'],
                'DESCRIPTION' => $sec['DESCRIPTION'],
                'URL' => '/catalog/'.$sec['CODE'].'/'
            ];
            //SEO tags
            $ipropSectionValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(self::IBLOCK_ID, $sec['ID']);
            $arSEO = $ipropSectionValues->getValues();
            $this->arResult["SECTIONS"][$sec['ID']]['SEO'] = $arSEO;
        }

        $this->sections = $this->arResult["SECTIONS"];

        $this->getElements();

    }

    private function getElements(){

        $dbElements = \Bitrix\Iblock\ElementTable::getList(
            [
                'order' => ['SORT' => 'ASC'],
                'select' => ['*','ADDINGS'=>'PROPERTY_PROP.IBLOCK_PROPERTY_ID'
                ],
                'filter' => [
                    'IBLOCK_ID' => self::IBLOCK_ID,
                    'IBLOCK_SECTION_ID' => $this->sectionIDs,
                    'ACTIVE' => 'Y'

                ],
                'runtime' => [
                    'PROPERTY_PROP' => array(
                        'data_type' => \Bitrix\Iblock\IblockElementProperty::class,
                        'reference' => array(
                            '=this.ID' => 'ref.IBLOCK_ELEMENT_ID'
                        ),
                    )
                ]
            ]
        );
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
            $this->arResult["SECTIONS"][$el['IBLOCK_SECTION_ID']]["ELEMENTS"][$el['ID']] =  [
               'NAME' => $el['NAME'],
               'PRICE' => number_format($sku["PRICE"],0,',',' '),
               'PRICE_FORMAT' => (int)$sku["PRICE"],
                'ID' => $el['ID'],
                'URL' => '/catalog/'.$this->sections[$el['IBLOCK_SECTION_ID']]['CODE'].'/'.$el['CODE'].'/',
                'PICTURE' => CFile::GetByID($el['PREVIEW_PICTURE'])->fetch(),
                'TEASER' => $el['PREVIEW_TEXT'],
                'ADDINGS' => $el['ADDINGS'],
                'OFFERID' =>$sku["ID"]
            ];
            if($el['PREVIEW_PICTURE']){
                $fileArr = CFile::GetByID($el['PREVIEW_PICTURE'])->fetch();
                $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                $this->arResult["SECTIONS"][$el['IBLOCK_SECTION_ID']]["ELEMENTS"][$el['ID']]['PICTURE'] = $fileResArr;
            }
            else {
                if($el['DETAIL_PICTURE']){
                      $fileArr = CFile::GetByID($el['DETAIL_PICTURE'])->fetch();
                $fileResArr = CFile::ResizeImageGet($fileArr,['width'=>300,'height'=>300],BX_RESIZE_IMAGE_EXACT);
                $this->arResult["SECTIONS"][$el['IBLOCK_SECTION_ID']]["ELEMENTS"][$el['ID']]['PICTURE'] = $fileResArr;
                }
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

        $this->getData();

        $this->includeComponentTemplate();
    }
}