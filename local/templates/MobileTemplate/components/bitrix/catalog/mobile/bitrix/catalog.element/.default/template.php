<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$sticker = "";
if (array_key_exists("PROPERTIES", $arResult) && is_array($arResult["PROPERTIES"]))
{
	foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
		if (array_key_exists($propertyCode, $arResult["PROPERTIES"]) && intval($arResult["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
		{
			$sticker = toLower($arResult["PROPERTIES"][$propertyCode]["NAME"]);
			break;
		}
}

$strObName = 'obCatalogElement';
?>
<div class="detail_item" id="<?=$strObName;?>">
	<?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
		<div class="detail_item_img_container" <?if (!empty($arResult["PHOTO_GALLERY"])):?>onclick="showPhoto(<?=CUtil::PhpToJsObject($arResult["PHOTO_GALLERY"])?>, '<?=$arResult["NAME"]?>')"<?endif?>>
			<a class="detail_item_img" href="javascript:void(0)">
				<?if(is_array($arResult["DETAIL_PICTURE_SMALL"])):?>
					<img id="catalog_detail_image" src="<?=$arResult["DETAIL_PICTURE_SMALL"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
				<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
					<img id="catalog_detail_image" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
				<?endif?>
			</a>
			<!-- <span class="detail_item_img_lupe"></span> -->
		</div>
	<?endif;?>
	<div class="right-col" id="elementProps-<?= $arResult['ID']; ?>">
		<h2 class="detail_item_title">
			<a href="<?=$arResult["DETAIL_PAGE_URL"]?>" title="<?=$arResult["NAME"]?>"><?=$arResult["NAME"]?></a>
			<?if ($sticker):?><br/><span style="color:#9b0000; font-size: 14px;"><?=$sticker?></span><?endif?>
		</h2>
		<div class="form product-scu-container" data-entity="sku-block" data-entity-id="<?= $arResult['ID']; ?>">
		<?
			$firstOffer = $arResult['OFFERS'][$arResult['OFFER_ID_SELECTED']]; 
			foreach ($arResult['SKU_PROPS'] as $key => $arSkuOffer) {
				$propertyId = intval($arSkuOffer['ID']);
				?>
				<div class="line" data-code="<?=$arSkuOffer['CODE']; ?>" data-entity="sku-line-block">
					<div class="left">
						<p><?=$arSkuOffer['NAME']; ?>:</p>
					</div>
					<div class="right">
					<?foreach ($arSkuOffer['VALUES'] as $arValue) {
						$firstElement = false;

						if (intval($arValue['ID']) == intval($firstOffer['FILTER_PROPS'][$arSkuOffer['CODE']]['VALUE_ENUM_ID'])) {
							$firstElement = true;
						}
						?>
						<p class="sku-value<?=$firstElement ? ' active' : '';?>" data-treevalue="<?= $propertyId; ?>_<?= intval($arValue['ID']); ?>" data-onevalue="<?= intval($arValue['ID']); ?>"><?=$arValue['NAME']; ?></p>
						<?
					}
					?>
					</div>
				</div>
				<?
			}
		?>
		</div>

		<div class="line amountBlock" data-type-container="order">
			<div class="cash productPrice">
				<span class="prefix-priceOffer"></span>
				<b class="priceOffer"><?=round($arResult['MIN_PRICE']['VALUE'], 2); ?></b>
				<span class="sufix-priceOffer"> руб.</span>
			</div>
		</div>

		<div class="line button-block">
			<a class="btn btn-default product-item-detail-buy-button" rel="nofollow" href="/eshop_app/catalog/?action=ADD2BASKET&id_top1=<?=$firstOffer['ID']; ?>&SECTION_ID=<?=$arResult['IBLOCK_SECTION_ID'];?>&ELEMENT_ID=<?=$arResult['ID']; ?>" data-action="add_basket" data-product="<?=$firstOffer['ID']; ?>">Купить</a>
		</div>
	</div>
</div>

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

<?if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0)
{
	$arPropertyRecommend = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"];
	unset($arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]);
	if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):?>
	<div class="detail_item_description info <?if (!CMobile::getInstance()->isLarge()) echo "close";?>">
		<h3 onclick="OpenClose(BX(this).parentNode)"><?=GetMessage("CATALOG_PROPERTIES")?> <span class="detail_item_arrow"></span></h3>
		<div class="detail_item_description_text">
			<ul>
			<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
				<li>
					<table>
						<tr>
							<td class="detail_item_feature"><span><?=$arProperty["NAME"]?>:</span></td>
							<td class="detail_item__featurevalue"><span>
							<?if(is_array($arProperty["DISPLAY_VALUE"])):
								echo implode(" / ", $arProperty["DISPLAY_VALUE"]);
							elseif($pid=="MANUAL"):
								?><a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a><?
							else:
								echo $arProperty["DISPLAY_VALUE"];?>
							<?endif?>
							</span></td>
						</tr>
					</table>
				</li>
			<?endforeach?>
			</ul>
		</div>
	</div>
	<?endif;
}
?>

<div class="row">
    <div class="col-xs-12 popular" >
        <h3>Популярные в разделе </h3>
        <?
        global $arFilterDetail;

        $arFilterDetail = [
            'IBLOCK_SECTION_ID' => $arResult["ORIGINAL_PARAMETERS"]["SECTION_ID"],
            '!ID' => $arResult['ID']
        ];

        $APPLICATION->IncludeComponent("strizhi:eshopapp.top", ".default", array(
            "FILTER_NAME" => "arFilterDetail",
            "IBLOCK_TYPE_ID" => "catalog",
            "IBLOCK_ID" => "4",
            "ELEMENT_SORT_FIELD" => "shows",
            "ELEMENT_SORT_ORDER" => "DESC",
            "ELEMENT_COUNT" => "8",
            "FLAG_PROPERTY_CODE" => "SALELEADER",
            "OFFERS_LIMIT" => "5",
            "ACTION_VARIABLE" => "action",
            "PRODUCT_ID_VARIABLE" => "id_top1",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            "PRODUCT_PROPS_VARIABLE" => "prop1",
            "CATALOG_FOLDER" => SITE_DIR . "eshop_app/catalog/",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "180",
            "CACHE_GROUPS" => "Y",
            "DISPLAY_COMPARE" => "N",
            "PRICE_CODE" => array(
                0 => "BASE",
            ),
            "USE_PRICE_COUNT" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_PROPERTIES" => array(),
            "CONVERT_CURRENCY" => "N",
            "DISPLAY_IMG_WIDTH" => "300",
            "DISPLAY_IMG_HEIGHT" => "300",
            "BASKET_URL" => SITE_DIR . "eshop_app/personal/cart/",
            "SHARPEN" => "30",
            "VARIABLE_ALIASES" => array(
                "SECTION_ID" => "SECTION_ID",
                "ELEMENT_ID" => "ELEMENT_ID",
            )
        ),
            false
        ); ?>
    </div>

</div>

<script type="text/javascript">
	app.setPageTitle({"title" : "<?=CUtil::JSEscape(htmlspecialcharsback($arResult["NAME"]))?>"});
	function showPhoto(arPhotos, descr)
	{
		var photos = [];
		for (var i=0; i<arPhotos.length; i++)
		{
			photos[i] = {url : arPhotos[i], description : descr};
		}
		app.openPhotos({
			"photos": photos
		});
	}
</script>
<?php
$arJSParams = array(
	'UNIT' => $productUnit,
	'UNIT_COEFFICIENT' => $arResult['UNIT_COEFFICIENT'],
    'CONTAINER' => $strObName,
    'SITE_ID' => $component->getSiteId(),
    'CONFIGURATION' => array(
        'OFFERS_PROPS_FILTER' => $arParams['OFFER_TREE_PROPS'],
        'PRODUCT_PROPS_TO_SHOW' => $arParams['PROPERTY_CODE'],
        'OFFERS_PROPS_TO_SHOW' => $arParams['OFFER_TREE_PROPS'],
    ),
    'QUANTITY_ITERATION' => $stepIterationQuantity,
    'DEFAULT_QUANTITY' => $defaultQuantity,
    'IS_BIND_OFFER' => $arResult['IS_BIND_OFFER'],
    'PRODUCT_ID' => $arResult['ID'],
    'PRODUCT_SECT_ID' => $arResult['IBLOCK_SECTION_ID'],
    'OFFERS' => $arResult['OFFERS'],
    'OFFERS_PROPS' => $arResult['SKU_PROPS'],
    'OFFER_ID_SELECTED' => $arResult['OFFER_ID_SELECTED'],
    'OFFERS_COLORS' => $arResult['COLLECTION_COLOR'],
    'IS_AUTHORIZED' => $USER->IsAuthorized(),
);
?>
<script type="text/javascript">
	BX.message({
		TITLE_PRICE_GET_INFO: 'цену уточняйте',
		SITE_ID: '<?php echo SITE_ID; ?>'
	});

	var container<?=$strObName; ?> = new App.Shop.CatalogElement(<?=CUtil::PhpToJSObject($arJSParams); ?>);
</script>


