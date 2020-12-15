<?php

define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Sale;

CModule::IncludeModule('sale');
CModule::IncludeModule('iblock');

$arFields = array(
    "PRODUCT_ID" => $_REQUEST["id"],
    "PRICE" => $_REQUEST["price"],
    "QUANTITY" => 1  ,
  //  "LID" => LANGUAGE_ID ,
   // "CURRENCY" => "RUB" ,
    'CUSTOM_PRICE' => 'Y',

);
$res = null;
$basketItems = [];

$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
$element = CCatalogProduct::GetList([],["ID"=>$arFields["PRODUCT_ID"]])->fetch();
//$arFields["NAME"] = $element["ELEMENT_NAME"];

if(isset($_REQUEST["addings"])) {

    foreach($_REQUEST["addings"] as $adding){
        $arFields["PRICE"] +=   $adding["price"] ;

        $arFields["PROPS"][] = [
            "NAME" =>$adding["id"] ,
            "CODE" => "ADDING-".$adding["id"] ,
            "VALUE" => "Добавка: ".$adding["name"].", ". $adding["qnt"].' шт.'
        ];
    }
}

$r = Bitrix\Catalog\Product\Basket::addProduct($arFields);
/*
if(isset($arFields["PRODUCT_ID"]) && (int)$arFields["PRODUCT_ID"] > 0){

    $element = CCatalogProduct::GetList([],["ID"=>$arFields["PRODUCT_ID"]])->fetch();

    $arFields["NAME"] = $element["ELEMENT_NAME"];
    $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    $item = $basket->createItem('catalog', $arFields["PRODUCT_ID"]);

    $res = \Bitrix\Catalog\Product\Basket::addProductToBasket($basket, $arFields, array('SITE_ID' => SITE_ID));

    if (!$res->isSuccess()) {
        var_dump($res->getErrorMessage());
    }
   // $item->setFields($arFields);
    if(isset($_REQUEST["addings"])) {

        foreach($_REQUEST["addings"] as $adding){
            $arFields["PRICE"] =  $arFields["PRICE"] +$adding["price"] ;

          $arFields["PROPS"][] = [
                "NAME" => "Добавка: ".$adding["name"].", ". $adding["qnt"].' шт.' ,
                "CODE" => "ADDING",
                "VALUE" => $adding["id"]
            ];
      //  $res = Add2BasketByProductID($arFields["PRODUCT_ID"]);
        }
    }
   // $basket->save();

   // $product = CSaleBasket::Add($arFields);
   // $res = CSaleBasket::Add($arFields);


    Add2BasketByProductID(
        $arFields["PRODUCT_ID"],
        1,
        array(
            $arFields,
            $arFields["PROPS"]
        )
    );

}
if(isset($_REQUEST["addings"])){
    $text = '';
    foreach($_REQUEST["addings"] as $adding){
        $addingFields = [
            "PRODUCT_ID" => $adding["id"],
            "PRICE" => $adding["price"]  ,
            "QUANTITY" => $adding["qnt"]  ,
            "CURRENCY" => $adding["currency"]  ,
            "NAME" => $adding["name"]  ,
            "LID" => $adding["lid"]  ,
        ];

        if($res){
            $addingFields["PROPS"][] = [
                "NAME" => "Parent product",
                "CODE" => "PARENT_ID",
                "VALUE" => $res
            ] ;
            $addingFields["PROPS"][] = [
                "NAME" => "Блюдо",
                "CODE" => "DISH",
                "VALUE" => 'Добавка к блюду: #'.$res.' '.$arFields["NAME"]
            ] ;
            $resadding = CSaleBasket::Add($addingFields);

        }
    }
}*/
$result['result'] = 'success';
$result['res'] = $basketItems;



echo \Bitrix\Main\Web\Json::encode($result);
