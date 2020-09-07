<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CJSCore::Init(array("jquery"));
?>

<div class="item_list_component">
    <ul id="section_items">
        <?
        if ($_REQUEST["ajax_get_page"] == "Y") {
            $APPLICATION->RestartBuffer();
        }

        ?>
        <? foreach ($arResult["ITEMS"] as $cell => $arElement): ?>
            <li id="<?= $this->GetEditAreaId($arElement['ID']); ?>"
            <!--        onclick="app.openNewPage('--><? //=$arElement["DETAIL_PAGE_URL"]?>//')"

            >
            <?
            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

            $sticker = "";
            if (array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"])) {
                foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
                    if (array_key_exists($propertyCode, $arElement["PROPERTIES"]) && intval($arElement["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0) {
                        $sticker = toLower($arElement["PROPERTIES"][$propertyCode]["NAME"]);
                        break;
                    }
            }
            ?>
            <table style="width: 100%;">
                <tr class="wrapper-flex">
                    <td style="max-width: 70px;">
                        <? if (is_array($arElement["PREVIEW_PICTURE"])): ?>
                            <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>" class="item_list_img"><span><img
                                            src="<?= $arElement["PREVIEW_PICTURE"]["SRC"] ?>"
                                            alt="<?= $arElement["NAME"] ?>"
                                            title="<?= $arElement["NAME"] ?>" id="galleryBigImages-<?=$arElement["ID"];?>"/></span></a>
                        <? elseif (is_array($arElement["DETAIL_PICTURE"])): ?>
                            <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>" class="item_list_img"><span><img
                                            src="<?= $arElement["DETAIL_PICTURE"]["SRC"] ?>"
                                            alt="<?= $arElement["NAME"] ?>"
                                            title="<?= $arElement["NAME"] ?>" id="galleryBigImages-<?=$arElement["ID"];?>"/></span></a>
                        <? endif ?>
                    </td>
                    <td style="width: 100%; padding-left: 10px;">
                        <div class="item_list_title">
                            <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>"><?= $arElement["NAME"] ?><? if ($sticker): ?><? endif ?></a>
                        </div>

                        <? if (is_array($arElement["DISPLAY_PROPERTIES"])): ?>
                            <div class="item_item_description_text">
                                <ul>
                                    <? foreach ($arElement["DISPLAY_PROPERTIES"] as $pid => $arProperty): ?>
                                        <li><?= $arProperty["NAME"] ?>:
                                            <? if (is_array($arProperty["DISPLAY_VALUE"]))
                                                echo implode(" / ", $arProperty["DISPLAY_VALUE"]);
                                            else
                                                echo $arProperty["DISPLAY_VALUE"]; ?>
                                        </li>
                                    <? endforeach ?>
                                </ul>
                            </div>
                        <? endif ?>

						<div class="product-item-info-container" id="itemProps-<?= $arElement["ID"] ?>" data-entity="sku-block" data-entity-id="<?= $arElement["ID"] ?>">
							<?
							$firstElement = $arResult["JS_PARAMS"]["ITEMS"][$arElement["ID"]]["OFFERS"][$arElement["OFFER_ID_SELECTED"]]["FILTER_PROPS"];

							foreach ($arResult['SKU_PROPS'] as $skuProperty) {

								$propertyId = $skuProperty['ID'];
								$skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
								if (!isset($arElement['SKU_TREE_VALUES'][$propertyId])) {
									continue;
								}
								?>
								<div class="row product-item-scu-container" data-entity="sku-line-block">
									<div class="leftCol"><?= $skuProperty['NAME']; ?>:</div>
									<div class="rightCol" data-code="<?= $skuProperty['CODE']; ?>">
										<div class="item-scu-values">
											<?
											#echo '<pre>'.print_r($arElement['SKU_TREE_VALUES'], true).'</pre>';
											foreach ($skuProperty['VALUES'] as $key => $value) {
												if (!isset($arElement['SKU_TREE_VALUES'][$propertyId][$value['ID']]))
													continue;

												$value['NAME'] = htmlspecialcharsbx($value['NAME']);

												?>
												<div class="product-item-scu-item product-item-scu-item-color-container<?
													if ($firstElement[$skuProperty['CODE']]['VALUE_ENUM_ID'] == $value['ID']) echo ' active'; ?>" data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>" data-onevalue="<?= $value['ID'] ?>">
														<div class="color-circle sku-prop-value<?if ($firstElement[$skuProperty['CODE']]['VALUE_ENUM_ID'] == $value['ID']) echo ' active'; ?>" data-title="<?= $value['NAME'] ?>"><?= $value['NAME'] ?></div>
													</div>
												<?
											}
											?>
										</div>
									</div>
								</div>
								<?
							}
							?>
							<div class="row">
								<a class="linkBtn urlDetailPage-<?= $arElement["ID"] ?> uk-button clear"
								href="<?= $url ?>"><?= GetMessage("CT_BCS_TPL_MESS_BTN_DETAIL"); ?></a>
							</div>
						</div>

                        <div class="itemlist_price_container">
							<? if (!is_array($arElement["OFFERS"]) || empty($arElement["OFFERS"])){?>
                                <? foreach ($arElement["PRICES"] as $code => $arPrice): ?>
                                    <? if ($arPrice["CAN_ACCESS"]): ?>
                                        <span class="item_price" id="itemPrice-<?=$arElement["ID"];?>"><?= $arPrice["PRINT_VALUE"] ?></span>
                                    <? endif; ?>
                                <? endforeach; ?>
							<?} else{ ?>
							<span class="item_price" id="itemPrice-<?=$arElement["ID"];?>"></span>
							<?}?>

                            <form action="<?= POST_FORM_ACTION_URI ?>" id="quantity_form_<? echo $arElement["ID"] ?>"
                                  style="display: inline-block;"
                                  method="post" enctype="multipart/form-data">

                                <input type="hidden" name="<? echo $arParams["ACTION_VARIABLE"] ?>" value="ADD2BASKET">
                                <input type="hidden" name="<? echo $arParams["PRODUCT_ID_VARIABLE"] ?>"
                                       value="<? echo $arElement["ID"] ?>">
                                <a id="itemButtonBasket-<? echo $arElement["ID"] ?>" data-offer="<? echo $arElement["ID"] ?>" data-product="<? echo $arElement["ID"] ?>" class="main_item_buy button_red_small" data-action="add_basket" ontouchstart="BX.toggleClass(this, 'active');" ontouchend="BX.toggleClass(this, 'active');" href="javascript:void(0)">
                                    <? echo GetMessage("CATALOG_BUY") ?>
                                </a>
                                <a class="section_key_cartlink button_yellow_small" href="/eshop_app/personal/cart/" rel="nofollow">В корзине</a>
                            </form>
                        </div>

                    </td>
                    <td class="section_items_item_quantity">
                        <select id="item_quantity_<? echo $arElement["ID"] ?>"
                                name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"] ?>">
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <div class="detail_item_buy_container">

                        </div>
                    </td>
                </tr>
            </table>
            </li>
        <? endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
        <?
        if ($_REQUEST["ajax_get_page"] == "Y") {
            require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
        }
        ?>
    </ul>
</div>

<script type="text/javascript">
	BX.message({
		SITE_ID: '<? echo SITE_ID; ?>',
		POST_ACTION_URI: '<? echo CUtil::JSEscape(POST_FORM_ACTION_URI);?>'
	});
</script>
<script type="text/javascript">
	var containerCatalogSection = new App.Shop.CatalogSection(<?php echo json_encode($arResult["JS_PARAMS"]);?>, <?php echo json_encode($arResult['IMPORTANT_OFFERS']);?>);
</script>

<script type="text/javascript">
    app.setPageTitle({"title": "<?=CUtil::JSEscape(htmlspecialcharsback($arResult["NAME"]))?>"});

    window.pagenNum = 1;
    window.onscroll = function () {
        var preloadCoefficient = 2;

        var clientHeight = document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
        var documentHeight = document.documentElement.scrollHeight ? document.documentElement.scrollHeight : document.body.scrollHeight;
        var scrollTop = window.pageYOffset ? window.pageYOffset : (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);

        if ((documentHeight - clientHeight * (1 + preloadCoefficient)) <= scrollTop) {
            getBottomItems();
        }
    }

    function getBottomItems() {
        if (!(<?=(empty($arResult["NAV_STRING"]) ? '0' : $arResult["NAV_STRING"])?> > <?=$arParams["PAGE_ELEMENT_COUNT"]?>* window.pagenNum))
        return;

        window.pagenNum++;

        BX.ajax({
            timeout: 30,
            method: 'POST',
            url: "<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>&ajax_get_page=Y&PAGEN_1=" + window.pagenNum,
            processData: false,
            onsuccess: function (sectionHTML) {
                var sectionDomObjCont = BX("new_items_container");

                if (!sectionDomObjCont) {
                    sectionDomObjCont = document.createElement("DIV");
                    sectionDomObjCont.id = "new_items_container";
                    sectionDomObjCont.style.display = "none";
                }
                sectionDomObjCont.innerHTML = sectionHTML;

                var sectionsObj = BX.findChildren(sectionDomObjCont, {tagName: "li"}, false);

                for (var i in sectionsObj) {
                    BX("section_items").appendChild(sectionsObj[i]);
                }
            },
            onfailure: function () {
            }
        });
    };

</script>

