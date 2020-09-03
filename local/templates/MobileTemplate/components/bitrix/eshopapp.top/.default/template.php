<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CJSCore::Init(array("jquery"));
$page_size = 24;
$page = 1;
$itr = 0;
?>

<?if(count($arResult["ITEMS"]) > 0): ?>
	<?
	//$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	//$arNotify = unserialize($notifyOption);

	?>

<div class="main_catalog hidden-other">
<?foreach($arResult["ITEMS"] as $key => $arItem):
	if(is_array($arItem))
	{
        $itr++;
        if ($itr == $page_size) {
            $itr = 0;
            $page++;
        }
		$bPicture = is_array($arItem["PREVIEW_IMG"]);

		?>
	<div class="main_catalog_item page_catalog_<?=$page?>" data-page="<?=$page?>" >
<!--		--><?//if ($bPicture):?>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="main_catalog_item_img">
<!--                <span>-->
                    <img src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>"  alt="<?=$arItem["NAME"]?>" />
<!--                </span>-->
            </a>
<!--		--><?//endif?>
		<h2>
            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
                <?=$arItem["NAME"]?>
            </a>
        </h2>

		<div class="main_catalog_item_price">
		<?if(!is_array($arItem["OFFERS"]) || empty($arItem["OFFERS"])):?>
			<?
				$numPrices = count($arParams["PRICE_CODE"]);
				foreach($arItem["PRICES"] as $code=>$arPrice):
					if($arPrice["CAN_ACCESS"]):?>
						<?if ($numPrices>1):?><p style="padding: 0; margin-bottom: 5px;"><?=$arResult["PRICES"][$code]["TITLE"];?>:</p><?endif?>
						<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
							<div class="price">
								<div class="main_price_container oldprice">
									<span class="item_price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br/> 
									<span class="item_price_old"><?=$arPrice["PRINT_VALUE"]?></span>
								</div>
							</div>
						<?else:?>
							<div class="main_price_container">
								<span class="item_price"><?=$arPrice["PRINT_VALUE"]?></span>
							</div>
						<?endif;
					endif;
				endforeach;
			?>

			<?if ($arItem["CAN_BUY"]):?>
				<noindex>
				<a href="<?=$arItem["ADD_URL"]?>"
					class="main_item_buy button_red_small"
					rel="nofollow"
					onclick="
						BX.addClass(BX.findParent(this, {class : 'main_catalog_item'}, false), 'add2cart');//	setTimeout('BX.removeClass(obj, \'add2cart\')', 3000);
						return addItemToCart(this);"
					id="catalog_add2cart_link_<?=$arItem['ID']?>">
					<?=GetMessage("CATALOG_ADD")?>
				</a>
				</noindex>
			<?endif?>
		<?endif?>
		</div>
		<div class="clb"></div>
		<a href="<?=$arParams["BASKET_URL"]?>" class="main_catalog_item_cartlink button_yellow_small" ontouchstart="BX.toggleClass(this, 'active');" ontouchend="BX.toggleClass(this, 'active');"><?=GetMessage("CATALOG_IN_CART")?></a>
	</div>
<?
	}
endforeach;
?>
	<div class="clb"></div>
</div>
<?elseif($USER->IsAdmin()):?>
<h3 class="hitsale"><span></span><?=GetMessage("CR_TITLE_".$arParams["FLAG_PROPERTY_CODE"])?></h3>
<div class="listitem-carousel">
	<?=GetMessage("CR_TITLE_NULL")?>
</div>
<?endif;?>
