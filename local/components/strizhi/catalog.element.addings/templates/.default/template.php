<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
$componentAddingFolder = '/local/components/strizhi/catalog.element.addings/templates/.default/';

?>
<script src="<?=$componentAddingFolder?>slider.js"></script>
<div id="obName" style="display: none" data-obname="<?=$arParams["OBNAME"]?>"></div>
<button type="button" class="btn btn-primary" style="font-size: 16px;font-weight: bold;" data-toggle="modal" data-target="#exampleModal">
    Дополнить блюдо добавками
</button>

<div class="mess-addings hidden"></div>
<!-- Modal -->
<div class="modal " id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавки</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="adding-slider-section adding_first">
                        <?
                        foreach($arResult["FIRST_ADDINGS"] as $adding){
                            ?>

                                <div class="adding-slider-item">

                                    <div><?=$adding["NAME"]?></div>
                                    <div><?=number_format($adding["PRICE"]["PRICE"],0)?> руб.</div>
                                    <div class="product-item-button-container"  >
                                        <a class="btn btn-default"
                                           data-id="<?=$adding["ID"]?>"
                                           data-xmlid="<?=$adding["PRODUCT_XML_ID"]?>"
                                           data-price="<?=number_format($adding["PRICE"]["PRICE"],0)?>"
                                           data-currency="<?=$adding["PRICE"]["CURRENCY"]?>"
                                           data-lid="<?=$adding["LID"]?>"
                                           data-name="<?=$adding["NAME"]?>"
                                           href="javascript:void(0)" rel="nofollow" onclick="slider.addtobasket(this);return false;" >
                                            Добавить
                                        </a>
                                    </div>
                                </div>
                                <?
                        }
                        ?>

                </div>
                <div class="adding-slider-section adding_second">
                        <?
                        foreach($arResult["SECOND_ADDINGS"] as $addingSection){
                            ?>

                        <?
                            foreach($addingSection as $adding){
                                ?>
                                <div class="adding-slider-item">

                                    <div><?=$adding["NAME"]?></div>
                                    <div><?=number_format($adding["PRICE"]["PRICE"],0)?> руб.</div>
                                    <div class="product-item-button-container"  >
                                        <a class="btn btn-default"
                                           data-id="<?=$adding["ID"]?>"
                                           data-xmlid="<?=$adding["PRODUCT_XML_ID"]?>"
                                           data-price="<?=number_format($adding["PRICE"]["PRICE"],0)?>"
                                           data-currency="<?=$adding["PRICE"]["CURRENCY"]?>"
                                           data-lid="<?=$adding["LID"]?>"
                                           data-name="<?=$adding["NAME"]?>"
                                           href="javascript:void(0)" rel="nofollow" onclick="slider.addtobasket(this);return false;" >
                                            Добавить
                                        </a>
                                    </div>
                                </div>
                                <?
                            }
                        }
                        ?>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>

            </div>
        </div>
    </div>
</div>

