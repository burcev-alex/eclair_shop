<?
require($_SERVER["DOCUMENT_ROOT"] . "/eshop_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("BodyClass", "main");
?>

    <div class="banner flex">
        <h4>Всё самое вкусное </h4>
        <span>с доставкой на дом</span>
    </div>

    <div class="selectBlock">
        <select class="select">
            <option value="1">Меню</option>
            <option value="2">Меню 2</option>
            <option value="3">Меню 3</option>
        </select>
    </div>

    <!--start itemBlock -->
    <section class="itemBlock">
    <div class="itemBlockIn">
        <div class="itemBlock__title flex"><span>Завтраки</span></div>
        <div class="content">
            <div class="item">
                <a href="#" class="item__images">
                    <img src="images/item.jpg" alt="">
                </a>

                <a href="#" class="item__title">Тропический смузи-боул</a>
                <span class="item__gram">500 грамм</span>

                <div class="itemPrice flex">
                    <div class="itemPrice__price">400 ₽</div>
                    <button type="button" class="itemPrice__plus">+</button>
                </div>
            </div>

        </div>
    </div>

    <div class="maincontent_component">
        <div class="main_button_component">

            <a href="javascript:void(0)" id="product_title"  class="current"
               onclick="BX('saleleaders_block').style.display='none'; BX('product_block').style.display='block';  BX.removeClass(BX('saleleader_title'), 'current'); BX.addClass(BX(this), 'current');">
                Каталог
                <span></span>
            </a>

            <a href="javascript:void(0)" id="saleleader_title"
               onclick="BX('saleleaders_block').style.display='block'; BX('product_block').style.display='none';BX.removeClass(BX('product_title'), 'current'); BX.addClass(BX(this), 'current');">
                Хиты
                продаж
                <span></span>
            </a>

            <a href="javascript:void(0)" class="main_button_catalog"
               onclick="openSectionList();">Меню<span></span></a>
            <div class="clb"></div>
        </div>

        <div id="saleleaders_block"  style="display: none;">
            <?
            $arrFilter = [
                '!SECTION_ID' => 27
            ];

            $APPLICATION->IncludeComponent("strizhi:eshopapp.top", ".default", array(
                "FILTER_NAME" => "arrFilter",
                "IBLOCK_TYPE_ID" => "catalog",
                "IBLOCK_ID" => "4",
                "ELEMENT_SORT_FIELD" => "sort",
                "ELEMENT_SORT_ORDER" => "asc",
                "ELEMENT_COUNT" => "4",
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

        <div id="product_block">
            <?
            $APPLICATION->IncludeComponent(
                "strizhi:eshopapp.top",
                ".default",
                Array(
                    "IBLOCK_TYPE_ID" => "catalog",
                    "IBLOCK_ID" => "4",
                    "ELEMENT_SORT_FIELD" => "SECTION_ID",
                    "ELEMENT_SORT_ORDER" => "asc",
                    "ELEMENT_COUNT" => "999",
//                    "FLAG_PROPERTY_CODE" => "NEWPRODUCT",
                    "FILTER_NAME" => "arrFilter",
                    "OFFERS_LIMIT" => "5",
                    "ACTION_VARIABLE" => "action",
                    "PRODUCT_ID_VARIABLE" => "id_top2",
                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "CATALOG_FOLDER" => SITE_DIR . "eshop_app/catalog/",
                    //"SECTION_ID_VARIABLE" => "SECTION_ID",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "180",
                    "CACHE_GROUPS" => "Y",
                    "DISPLAY_COMPARE" => "N",
                    "PRICE_CODE" => array(0 => "BASE",),
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
                )
            ); ?>
        </div>
    </div>

    <script type="text/javascript">
        app.setPageTitle({"title": "<?=htmlspecialcharsbx(COption::GetOptionString("main", "site_name", ""))?>"});

        function openSectionList() {
            app.openBXTable({
                url: '<?=SITE_DIR?>eshop_app/catalog/sections.php',
                TABLE_SETTINGS: {
                    cache: true,
                    use_sections: true,
                    searchField: false,
                    showtitle: true,
                    name: "Каталог",
                    button:
                        {
                            type: 'basket',
                            style: 'custom',
                            callback: function () {
                                app.openNewPage("<?=SITE_DIR?>eshop_app/personal/cart/");
                            }
                        }
                }
            });
        }
    </script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php") ?>