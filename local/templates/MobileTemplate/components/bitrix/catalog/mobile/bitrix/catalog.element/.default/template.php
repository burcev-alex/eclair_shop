<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$sticker = "";
$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
    'ID' => $mainId,
    'DISCOUNT_PERCENT_ID' => $mainId.'_dsc_pict',
    'STICKER_ID' => $mainId.'_sticker',
    'BIG_SLIDER_ID' => $mainId.'_big_slider',
    'BIG_IMG_CONT_ID' => $mainId.'_bigimg_cont',
    'SLIDER_CONT_ID' => $mainId.'_slider_cont',
    'OLD_PRICE_ID' => $mainId.'_old_price',
    'PRICE_ID' => $mainId.'_price',
    'DISCOUNT_PRICE_ID' => $mainId.'_price_discount',
    'PRICE_TOTAL' => $mainId.'_price_total',
    'SLIDER_CONT_OF_ID' => $mainId.'_slider_cont_',
    'QUANTITY_ID' => $mainId.'_quantity',
    'QUANTITY_DOWN_ID' => $mainId.'_quant_down',
    'QUANTITY_UP_ID' => $mainId.'_quant_up',
    'QUANTITY_MEASURE' => $mainId.'_quant_measure',
    'QUANTITY_LIMIT' => $mainId.'_quant_limit',
    'BUY_LINK' => $mainId.'_buy_link',
    'ADD_BASKET_LINK' => $mainId.'_add_basket_link',
    'BASKET_ACTIONS_ID' => $mainId.'_basket_actions',
    'NOT_AVAILABLE_MESS' => $mainId.'_not_avail',
    'COMPARE_LINK' => $mainId.'_compare_link',
    'TREE_ID' => $mainId.'_skudiv',
    'DISPLAY_PROP_DIV' => $mainId.'_sku_prop',
    'DISPLAY_MAIN_PROP_DIV' => $mainId.'_main_sku_prop',
    'OFFER_GROUP' => $mainId.'_set_group_',
    'BASKET_PROP_DIV' => $mainId.'_basket_prop',
    'SUBSCRIBE_LINK' => $mainId.'_subscribe',
    'TABS_ID' => $mainId.'_tabs',
    'TAB_CONTAINERS_ID' => $mainId.'_tab_containers',
    'SMALL_CARD_PANEL_ID' => $mainId.'_small_card_panel',
    'TABS_PANEL_ID' => $mainId.'_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $arResult['NAME'];

$haveOffers = !empty($arResult['OFFERS']);
if ($haveOffers)
{
    $actualItem = isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']])
        ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]
        : reset($arResult['OFFERS']);
    $showSliderControls = false;

    foreach ($arResult['OFFERS'] as $offer)
    {
        if ($offer['MORE_PHOTO_COUNT'] > 1)
        {
            $showSliderControls = true;
            break;
        }
    }
}
else
{
    $actualItem = $arResult;
    $showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

if (array_key_exists("PROPERTIES", $arResult) && is_array($arResult["PROPERTIES"]))
{
	foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
		if (array_key_exists($propertyCode, $arResult["PROPERTIES"]) && intval($arResult["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
		{
			$sticker = toLower($arResult["PROPERTIES"][$propertyCode]["NAME"]);
			break;
		}
}
?>
<div class="itemSingle flex">
    <div class="itemProducts">
        <div class="itemImages">
            <?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
                <a class="itemImages__href" href="javascript:void(0)">
                    <?if(is_array($arResult["DETAIL_PICTURE_SMALL"])):?>
                        <img src="<?=$arResult["DETAIL_PICTURE_SMALL"]["SRC"]?>" alt="">
                    <?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
                        <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="">
                    <?endif?>
                </a>
            <?endif;?>

            <button type="button" class="item__btn"></button>

            <div class="item__content">
                <?if ($arResult["DETAIL_TEXT"] || $arResult["PREVIEW_TEXT"]):?>
                    <div class="detail_item_description open" >
                        <h3><?=GetMessage("CATALOG_FULL_DESC")?></h3>
                        <div class="detail_item_description_text">
                            <?if($arResult["DETAIL_TEXT"]):?>
                                <br /><?=$arResult["DETAIL_TEXT"]?><br />
                            <?elseif($arResult["PREVIEW_TEXT"]):?>
                                <br /><?=$arResult["PREVIEW_TEXT"]?><br />
                            <?endif;?>
                        </div>
                    </div>
                <?endif?>
            </div>
        </div>

        <a href="#" class="itemProducts__href"><?=$arResult["NAME"]?></a>

        <div class="itemSingle__price">800 ₽</div>
        <div class="itemPlural__desc">
            <?if($arResult["DETAIL_TEXT"]):?>
                <?=$arResult["DETAIL_TEXT"]?>
            <?elseif($arResult["PREVIEW_TEXT"]):?>
                <?=$arResult["PREVIEW_TEXT"]?>
            <?endif;?>
        </div>
    </div>
    <?if(!is_array($arResult["OFFERS"]) || empty($arResult["OFFERS"])):?>

        <?foreach($arResult["PRICES"] as $code=>$arPrice):?>
            <?if($arPrice["CAN_ACCESS"]):?>
                <?//=$arResult["CAT_PRICES"][$code]["TITLE"];?>
                <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                    <div class="detail_price_container oldprice">
                        <span class="item_price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br />
                        <span class="item_price_old"><?=$arPrice["PRINT_VALUE"]?></span>
                    </div>
                <?else:?>
                    <div class="detail_price_container">
                        <span class="item_price"><?=$arPrice["PRINT_VALUE"]?></span>
                    </div>
                <?endif;?>
            <?endif;?>
        <?endforeach;?>

        <?if($arResult["CAN_BUY"]):?>
            <?if($arParams["USE_PRODUCT_QUANTITY"]):?>
                <div class="clb"></div>
                <div class="detail_item_buy_container">
                    <form action="<?=POST_FORM_ACTION_URI?>" id="quantity_form" method="post" enctype="multipart/form-data"  >
                        <div class="detail_item_count">
                            <a href="javascript:void(0)" class="count_minus" id="count_minus" ontouchstart="if (BX('item_quantity').value > 1) BX('item_quantity').value--;"><span></span></a>
                            <input type="number" id="item_quantity" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="1">
                            <a href="javascript:void(0)" class="count_plus" id="count_plus" ontouchstart="BX('item_quantity').value++;"><span></span></a>
                        </div>
                        <input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="ADD2BASKET">
                        <input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arResult["ID"]?>">
                        <a class="detail_item_buykey button_red_medium" ontouchstart="BX.toggleClass(this, 'active');" ontouchend="BX.toggleClass(this, 'active');" href="javascript:void(0)" onclick="
                                BX.addClass(BX.findParent(this, {class : 'detail_item'}, false), 'add2cart');
                                app.onCustomEvent('onItemBuy', {});
                                BX.ajax({
                                timeout:   30,
                                method:   'POST',
                                url:       '<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
                                processData: false,
                                data: {
                        <?echo $arParams["ACTION_VARIABLE"]?>: 'ADD2BASKET',
                        <?echo $arParams["PRODUCT_ID_VARIABLE"]?>: '<?echo $arResult["ID"]?>',
                        <?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>: BX('quantity_form').elements['<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>'].value
                                },
                                onsuccess: function(reply){
                                },
                                onfailure: function(){
                                }
                                });
                                return BX.PreventDefault(event);
                                "><?echo GetMessage("CATALOG_BUY")?></a>
                        <a class="detail_item_buykey_cartlink button_yellow_small" href="<?echo $arParams["BASKET_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_IN_CART")?></a>
                    </form>
                </div>

            <?else:?>
            2
                <div class="detail_item_buy_container">
                    <noindex>
                        <a class="detail_item_buykey button_red_medium" ontouchstart="BX.toggleClass(this, 'active');" ontouchend="BX.toggleClass(this, 'active');" href="<?echo $arResult["ADD_URL"]?>" onclick="
						BX.addClass(BX.findParent(this, {class : 'detail_item'}, false), 'add2cart');
						return addItemToCart(this);" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>
                        <a class="detail_item_buykey_cartlink button_yellow_small" href="<?echo $arParams["BASKET_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_IN_CART")?></a>
                    </noindex>
                </div>
            <?endif;?>


            <?if(count($arResult["MORE_PHOTO"])>0):?>
                <div class="detail_item_gallery" onclick="showPhoto(<?=CUtil::PhpToJsObject($arResult["PHOTO_GALLERY"])?>, '<?=$arResult["NAME"]?>')">
                    <div class="detail_item_gallery_topborder"></div>
                    <span class="detail_item_gallery_left"></span>
                    <div class="detail_item_gallery_tcontainer">
                        <ul>
                            <?foreach($arResult["MORE_PHOTO"] as $photo):?>
                                <li><a href="javascript:void(0)"><span><img src="<?=$photo["SRC"]?>" alt=""></span></a></li>
                            <?endforeach?>
                        </ul>
                    </div>
                    <div class="clb"></div>
                    <span class="detail_item_gallery_right"></span>
                </div>
            <?endif?>


        <?/*elseif((count($arResult["PRICES"]) > 0) || is_array($arResult["PRICE_MATRIX"])):?>
			<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
					"NOTIFY_ID" => $arResult['ID'],
					"NOTIFY_PRODUCT_ID" => $arParams['PRODUCT_ID_VARIABLE'],
					"NOTIFY_ACTION" => $arParams['ACTION_VARIABLE'],
					"NOTIFY_URL" => htmlspecialcharsback($arResult["SUBSCRIBE_URL"]),
					"NOTIFY_USE_CAPTHA" => "N"
				),
				$component
			);?>
		<?*/endif?>
    <?endif;?>

    <!-- выводим добавки и опции -->
    <div class="product-item-detail-info-section">
        <?

        $arResult['OFFERS_PROP'] = ['KRUPA' => true];
        $arParams['PRODUCT_INFO_BLOCK_ORDER'] = [0 => 'sku', 1 => 'props'];
        foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName)
        {
            switch ($blockName)
            {
                case 'sku':
                    echo 'sku';
                    if ($haveOffers && !empty($arResult['OFFERS_PROP']))
                    {
                        echo 'haveOffers';
                        ?>
                        <div id="<?=$itemIds['TREE_ID']?>">
                            <?
                            foreach ($arResult['SKU_PROPS'] as $skuProperty)
                            {
                                if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
                                    continue;

                                $propertyId = $skuProperty['ID'];
                                $skuProps[] = array(
                                    'ID' => $propertyId,
                                    'SHOW_MODE' => $skuProperty['SHOW_MODE'],
                                    'VALUES' => $skuProperty['VALUES'],
                                    'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
                                );
                                ?>
                                <div class="product-item-detail-info-container" data-entity="sku-line-block">
                                    <div class="product-item-detail-info-container-title"><?=htmlspecialcharsEx($skuProperty['NAME'])?></div>
                                    <div class="product-item-scu-container">
                                        <div class="product-item-scu-block">
                                            <div class="product-item-scu-list">
                                                <ul class="product-item-scu-item-list">
                                                    <?
                                                    foreach ($skuProperty['VALUES'] as &$value)
                                                    {
                                                        $value['NAME'] = htmlspecialcharsbx($value['NAME']);

                                                        if ($skuProperty['SHOW_MODE'] === 'PICT')
                                                        {
                                                            ?>
                                                            <li class="product-item-scu-item-color-container" title="<?=$value['NAME']?>"
                                                                data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
                                                                data-onevalue="<?=$value['ID']?>">
                                                                <div class="product-item-scu-item-color-block">
                                                                    <div class="product-item-scu-item-color" title="<?=$value['NAME']?>"
                                                                         style="background-image: url('<?=$value['PICT']['SRC']?>');">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <?
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <li class="product-item-scu-item-text-container" title="<?=$value['NAME']?>"
                                                                data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
                                                                data-onevalue="<?=$value['ID']?>">
                                                                <div class="product-item-scu-item-text-block">
                                                                    <div class="product-item-scu-item-text"><?=$value['NAME']?></div>
                                                                </div>
                                                            </li>
                                                            <?
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                                <div style="clear: both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }

                    break;

                case 'props':
                    if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                    {
                        ?>
                        <div class="product-item-detail-info-container">
                            <?
                            if (!empty($arResult['DISPLAY_PROPERTIES']))
                            {
                                ?>
                                <dl class="product-item-detail-properties">
                                    <?
                                    foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
                                    {
                                        if (isset($arParams['MAIN_BLOCK_PROPERTY_CODE'][$property['CODE']]))
                                        {
                                            ?>
                                            <dt><?=$property['NAME']?></dt>
                                            <dd><?=(is_array($property['DISPLAY_VALUE'])
                                                    ? implode(' / ', $property['DISPLAY_VALUE'])
                                                    : $property['DISPLAY_VALUE'])?>
                                            </dd>
                                            <?
                                        }
                                    }
                                    unset($property);
                                    ?>
                                </dl>
                                <?
                            }

                            if ($arResult['SHOW_OFFERS_PROPS'])
                            {
                                ?>
                                <dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_MAIN_PROP_DIV']?>"></dl>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }

                    break;
            }
        }
        ?>
    </div>
    <!-- -->
    <div class="orderForm__amound">
        <button type="submit" class="orderForm__btn">В корзину<!-- 800 ₽--></button>
    </div>
</div>