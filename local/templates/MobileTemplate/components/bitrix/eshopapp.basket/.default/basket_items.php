<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><? $APPLICATION->SetPageProperty("BodyClass", "cart");
CJSCore::Init(array("jquery"));
?>
<div id="id-cart-list">
    <div class="cart_item_list">
        <div class="cart_item_list_top_container">
            <a href="javascript:void(0)" class="bedit cart_item_list_filter_button" onclick="changeMode()"></a>
            <div class="clb"></div>
        </div>

        <ul id="id-cart-ul">
            <? if (count($arResult["ITEMS"]["AnDelCanBuy"]) > 0): ?>
            <?
            $i = 0;
            foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems) {
                ?>
                <li id="basketItemID_<?= $arBasketItems["ID"] ?>" <?
                if (strlen($arBasketItems["DETAIL_PAGE_URL"]) > 0): ?>onclick="if (!BX.hasClass(BX('body'), 'edit')) app.loadPageBlank({url:'<?= htmlspecialcharsback($arBasketItems["DETAIL_PAGE_URL"]) ?>'});"<?
                endif;
                ?>>
                    <?
                    if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
                        <?
                        if (strlen($arBasketItems["DETAIL_PAGE_URL"]) > 0):?>
                            <a class="cart_item_list_img" href="<?= $arBasketItems["DETAIL_PAGE_URL"] ?>">
                        <?endif; ?>
                        <?
                        if (!empty($arResult["ITEMS_IMG"][$arBasketItems["ID"]]["SRC"])) :?>
                            <img src="<?= $arResult["ITEMS_IMG"][$arBasketItems["ID"]]["SRC"] ?>"
                                 alt="<?= $arBasketItems["NAME"] ?>"/>
                        <?endif ?>
                        <?
                        if (strlen($arBasketItems["DETAIL_PAGE_URL"]) > 0):?>
                            </a>
                        <?endif; ?>

                        <div class="cart_item_list_title">
                            <span><?= $arBasketItems["NAME"] ?></span>
                        </div>

                        <?
                        if (in_array("PROPS", $arParams["COLUMNS_LIST"])) {
                            ?>
                            <div class="cart_item_list_description_text">
                                <ul>
                                    <?
                                    foreach ($arBasketItems["PROPS"] as $val) {
                                        echo "<li>" . $val["NAME"] . ": " . $val["VALUE"] . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?
                        } ?>
					<?endif; ?>
					<?
					#echo '<pre>'.print_r($arBasketItems, true).'</pre>';
					?>
					<!--<div class="basket-item-block-properties">
						<div class="basket-item-property basket-item-property-scu-text" data-entity="basket-item-sku-block">
							<div class="basket-item-property-name">Крупа</div>
							<div class="basket-item-property-value">
								<ul class="basket-item-scu-list">
									<li class="basket-item-scu-item" title="Мультизлаковая" data-entity="basket-item-sku-field" data-initial="false" data-value-id="Мультизлаковая" data-sku-name="Мультизлаковая" data-property="KRUPA">
										<span class="basket-item-scu-item-inner">Мультизлаковая 2</span>
									</li>
									<li class="basket-item-scu-item" title="Мультизлаковая" data-entity="basket-item-sku-field" data-initial="false" data-value-id="Мультизлаковая" data-sku-name="Мультизлаковая" data-property="KRUPA">
										<span class="basket-item-scu-item-inner">Мультизлаковая 1</span>
									</li>
								</ul>
							</div>
						</div>
					</div>
					-->

                    <?
                    if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
                        <?
                        if (doubleval($arBasketItems["FULL_PRICE"]) > 0):?>
                            <div class="cart_price_conteiner oldprice whsnw">
                                <span class="item_price"><?= $arBasketItems["PRICE_FORMATED"] ?></span>
                                <span class="item_price_old"><?= $arBasketItems["FULL_PRICE_FORMATED"] ?></span>
                            </div>
                        <? else:?>
                            <div class="cart_price_conteiner whsnw">
                                <span class="item_price"><?= $arBasketItems["PRICE_FORMATED"] ?></span>
                            </div>
                        <?endif ?>
                    <?endif; ?>

                    <?
                    if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
                        <div class="cart_item_count">
                            <span><?= GetMessage("SALE_QUANTITY") ?>:</span>
                            <a href="javascript:void(0)" class="count_minus"
                               ontouchstart="if (BX('QUANTITY_<?= $arBasketItems["ID"] ?>').value > 1) BX('QUANTITY_<?= $arBasketItems["ID"] ?>').value--;calcBasket();"><span></span></a>
                            <input maxlength="18" min="1" type="number" class="quantity_input"
                                   name="QUANTITY_<?= $arBasketItems["ID"] ?>" value="<?= $arBasketItems["QUANTITY"] ?>"
                                   size="3" id="QUANTITY_<?= $arBasketItems["ID"] ?>">
                            <a href="javascript:void(0)" class="count_plus"
                               ontouchstart="BX('QUANTITY_<?= $arBasketItems["ID"] ?>').value++;calcBasket();"><span></span></a>
                        </div>
                    <?endif; ?>

                    <?
                    if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
                        <a class="cart_item_remove"
                           href="<?= str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"]) ?>"
                           onclick="/*if (confirm('<?= GetMessage("SALE_DELETE_CONFIRM") ?>')) */ return DeleteFromCart(this); //else return false;"
                           title="<?= GetMessage("SALE_DELETE_PRD") ?>"></a>
                    <?endif; ?>
                    <?
                    if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
                        <a class="cart_item_delayed"
                           href="<?= str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["shelve"]) ?>"
                           onclick="return DelayInCart(this);"></a>
                    <?endif; ?>
                    <div class="clb"></div>
                </li>
                <?
                $i++;
            }
            ?>
        </ul>
        <? endif ?>
    </div>

    <div class="cart_item_bottom" id="cart_item_bottom"
         <? if (!count($arResult["ITEMS"]["AnDelCanBuy"]) > 0): ?>style="display:none"<? endif; ?>>
        <!--	--><? //if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
        <!--		<div class="cart_item_total_price" >-->
        <!--		--><? //echo GetMessage("SALE_ALL_WEIGHT")?><!--:-->
        <!--		<span id="weight">--><? //=$arResult["allWeight_FORMATED"]?><!--</span>-->
        <!--		</div>-->
        <!--	--><? //endif;?>
        <div id="all_discount">
            <? if (doubleval($arResult["DISCOUNT_PRICE"]) > 0): ?>
                <div class="cart_item_total_price">
                    <? echo GetMessage("SALE_CONTENT_DISCOUNT") ?><?
                    if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"]) > 0)
                        echo " (" . $arResult["DISCOUNT_PERCENT_FORMATED"] . ")"; ?>:
                    <?= $arResult["DISCOUNT_PRICE_FORMATED"] ?>
                </div>
            <? endif; ?>
        </div>
        <? if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'): ?>
            <div class="cart_item_total_price">
                <? echo GetMessage('SALE_VAT_EXCLUDED') ?>
                <span id="vat_excluded"><?= $arResult["allNOVATSum_FORMATED"] ?></span>
            </div>
            <div class="cart_item_total_price">
                <? echo GetMessage('SALE_VAT_INCLUDED') ?>
                <span id="vat_included"><?= $arResult["allVATSum_FORMATED"] ?></span>
            </div>
        <? endif; ?>
        <div class="cart_item_total_price">
            <?= GetMessage("SALE_ITOGO") ?>: <span class="price"><strong
                        id="all_price"><?= $arResult["allSum_FORMATED"] ?></strong></span>
        </div>
        <hr class="cart_item_hr">
        <? if ($arParams["HIDE_COUPON"] != "Y"): ?>
            <div class="cart_item_coupon">
                <span><?= GetMessage("SALE_COUPON") ?>:</span>
                <div class="cart_item_list_search_input_container">
                    <input value="<? if (!empty($arResult["COUPON"])): ?><?= $arResult["COUPON"] ?><? endif; ?>"
                           name="COUPON" type="text">
                </div>
                <div class="clb"></div>
            </div>
        <? endif; ?>
        <hr class="cart_item_hr">
        <input type="hidden" value="<? echo GetMessage("SALE_UPDATE") ?>" name="BasketRefresh">
        <a id="basketOrderButton2" class="cart_item_checkout button_red_medium"
           ontouchstart="BX.toggleClass(this, 'active');" ontouchend="BX.toggleClass(this, 'active');"
           onclick="app.loadPage('<?= $arParams["PATH_TO_ORDER"] ?>'); return false;"><? echo GetMessage("SALE_ORDER") ?></a>
        <br/>
    </div>

    <div class="cart-notetext" id="empty_cart_text"
         <? if (count($arResult["ITEMS"]["AnDelCanBuy"]) > 0): ?>style="display:none"<? endif; ?>>
        <div class="detail_item tac">
		<span class="empty_cart_text">
			<?= GetMessage("SALE_NO_ACTIVE_PRD"); ?>
		</span>
        </div>
    </div>

</div>