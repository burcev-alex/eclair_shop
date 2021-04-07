<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
 ?>

<div class="catalog-section bx-blue">
    <div class="row product-item-list">
        <?php

        if(!empty($arResult["SECTIONS"])){
            foreach($arResult["SECTIONS"] as $section){
                if(isset($arParams["USE_SEO"]) && $arParams["USE_SEO"] == "Y"){
                if(isset($section["SEO"]) && !empty($section["SEO"])){
                    if ($section["SEO"]['SECTION_META_TITLE'] != false) {
                        $APPLICATION->SetPageProperty("title", $section["SEO"]['SECTION_META_TITLE']);
                    }
                    if ($section["SEO"]['SECTION_META_KEYWORDS'] != false) {
                        $APPLICATION->SetPageProperty("keywords", $section["SEO"]['SECTION_META_KEYWORDS']);
                    }
                    if ($section["SEO"]['SECTION_META_DESCRIPTION'] != false) {
                        $APPLICATION->SetPageProperty("description", $section["SEO"]['SECTION_META_DESCRIPTION']);
                    }
                }
                }
                if(!empty($section["ELEMENTS"])){?>
                    <div class="col-xs-12 col-sm-12 col-md-12  col-lg-12 ">
                        <a href="<?=$section["URL"]?>"><h2><?=$section["NAME"]?></h2></a>
                        <?
                        if(strlen($section["DESCRIPTION"]) > 0){
                            ?>
                            <span class="header-desc"><?=$section["DESCRIPTION"]?></span>
                            <?
                        }
                        ?>
                        <div class="row product-list">
                                <?
                                foreach($section["ELEMENTS"] as $element){
                                $preprice = '';
                                $offer = false;
                                if ($element["OFFERID"] != $element["ID"]) {
                                    $preprice = 'от ';
                                    $offer = true;
                                }

                                ?>
                            <div class="col-sm-4 product-item-big-card">
                                <div class="col-md-12">
                                    <div class="product-item-container">
                                        <div class="product-item">
                                            <a class="product-item-image-wrapper" href="<?= $element["URL"] ?>"
                                               title="<?= $element["NAME"] ?>" data-entity="image-wrapper">
                                                            <span class="product-item-image-original" id=""
                                                                  style="background-image: url('<?= $element["PICTURE"]["src"] ?>'); ">
		                                                    </span>
                                            </a>
                                            <div class="product-item-title">
                                                <a href="<?= $element["URL"] ?>"
                                                   title="<?= $element["NAME"] ?>"><?= $element["NAME"] ?></a>
                                            </div>
                                            <div class="product-item-info-container product-item-price-container">
                                                <span class="product-item-price-current"><?= $preprice . $element["PRICE"] ?> руб.</span>
                                            </div>
                                            <div class="product-item-info-container product-item-hidden"
                                                 data-entity="buttons-block">
                                                <div class="product-item-button-container">
                                                    <?
                                                    if ($offer) {
                                                    ?>
                                                    <a class="btn btn-default btn-md" href="<?= $element["URL"] ?>"
                                                        >
                                                        В корзину
                                                    </a>
                                                    <?
                                                    }
                                                else {
                                                        ?>
                                                    <a class="btn btn-default btn-md" href="javascript:void(0)"
                                                       data-elementid="<?= $element["OFFERID"] ?>"
                                                       data-price="<?= $element["PRICE_FORMAT"] ?>" rel="nofollow"
                                                       onclick="Catalog.toCart(this);return false;">
                                                        В корзину
                                                    </a>
                                                    <?
                                                    }
                                                                ?>
                                                                  <? if((int)$element["ADDINGS"] > 0 && $element["ADDINGS"] == 38){ // 38 = свойство товара применять добавки
                                                                    ?>
                                                                    <a class="btn btn-default btn-md" href="<?=$element["URL"]?>" rel="nofollow">Выбрать добавки</a>
                                                                    <?
                                                                }
                                                                ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?
                                }
                                ?>
                        </div>
                    </div>
                    <?
            }
            }
        }

        ?>
    </div>
</div>
