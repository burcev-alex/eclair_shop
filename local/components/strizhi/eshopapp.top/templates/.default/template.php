<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
//CJSCore::Init(array("jquery"));
?>
<? if (count($arResult["ITEMS"]) > 0): ?>
<section class="itemBlock">
    <?
    $currentSection = false;
    foreach ($arResult["ITEMS"] as $key => $arItem):
        if ($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] == false)
            continue;
        if (is_array($arItem)) {
            $itr++;
            if ($itr == $page_size) {
                $itr = 0;
                $page++;
            }
            $bPicture = is_array($arItem["PREVIEW_IMG"]);

            ?>
            <? if ($currentSection == false || $arItem['IBLOCK_SECTION_ID'] != $currentSection): ?>
                <div class="itemBlockIn">
                <div class="itemBlock__title flex">
                    <span><?= $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] ?></span></div>
                <div class="content">
            <? endif; ?>
            <div class="item">
                <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="item__images">
                    <img src="<?= $arItem["PREVIEW_IMG"]["SRC"] ?>" alt="">
                </a>

                <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="item__title"><?= $arItem["NAME"] ?></a>
                <span class="item__gram">500 грамм</span>

                <? if ($arItem["CAN_BUY"]): ?>
                    <noindex>
                        <div class="itemPrice flex">
                            <?
                            $numPrices = count($arParams["PRICE_CODE"]);
                            foreach ($arItem["PRICES"] as $code => $arPrice):
                                if ($arPrice["CAN_ACCESS"]):?>
                                    <? if ($numPrices > 1): ?>
                                    <? endif ?>
                                    <? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
                                        <div class="itemPrice__price"><?= $arPrice["PRINT_VALUE"] ?></div>
                                    <? else: ?>
                                        <div class="itemPrice__price"><?= $arPrice["PRINT_VALUE"] ?></div>
                                    <?endif;
                                endif;
                            endforeach;
                            ?>

                            <button type="button" class="itemPrice__plus" onclick="
						BX.addClass(BX.findParent(this, {class : 'main_catalog_item'}, false), 'add2cart');//	setTimeout('BX.removeClass(obj, \'add2cart\')', 3000);
						return addItemToCart(this);" id="catalog_add2cart_link_<?= $arItem['ID'] ?>">+
                            </button>
                        </div>
                    </noindex>
                <? endif ?>

            </div>
    <?  } ?>
        <? $currentSection = $arItem['IBLOCK_SECTION_ID']; ?>
        <? if ($arResult["ITEMS"][$key + 1]['IBLOCK_SECTION_ID'] != $currentSection): ?>
        </div>
    </div>
    <? endif; ?>
    <? endforeach;?>
<? endif; ?>
