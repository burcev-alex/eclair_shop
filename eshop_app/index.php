<?
require($_SERVER["DOCUMENT_ROOT"] . "/eshop_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("BodyClass", "main");
?>

    <div>
        <img width="100%" src="<?= SITE_TEMPLATE_PATH ?>/images/head_logo.png">
    </div>
<?

//$APPLICATION->IncludeComponent(
//	"bitrix:eshopapp.top",
//	"slider",
//	Array(
//		"IBLOCK_TYPE_ID" => "catalog",
//		"IBLOCK_ID" => "4",
//		"ELEMENT_SORT_FIELD" => "RAND",
//		"ELEMENT_SORT_ORDER" => "asc",
//		"ELEMENT_COUNT" => "2",
//		"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
//		"OFFERS_LIMIT" => "5",
//		"ACTION_VARIABLE" => "action",
//		"PRODUCT_ID_VARIABLE" => "id_top",
//		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
//		"PRODUCT_PROPS_VARIABLE" => "prop",
//		"CATALOG_FOLDER" => SITE_DIR."eshop_app/catalog/",
//		//"SECTION_ID_VARIABLE" => "SECTION_ID",
//		"CACHE_TYPE" => "A",
//		"CACHE_TIME" => "180",
//		"CACHE_GROUPS" => "Y",
//		"DISPLAY_COMPARE" => "N",
//		"PRICE_CODE" => array(0=>"BASE",),
//		"USE_PRICE_COUNT" => "N",
//		"SHOW_PRICE_COUNT" => "1",
//		"PRICE_VAT_INCLUDE" => "Y",
//		"DISPLAY_IMG_WIDTH" => "180",
//		"DISPLAY_IMG_HEIGHT" => "180",
//		"PRODUCT_PROPERTIES" => array(),
//		"BASKET_URL" => SITE_DIR."eshop_app/personal/cart/",
//		"CONVERT_CURRENCY" => "N",
//		"VARIABLE_ALIASES" => array(
//			"SECTION_ID" => "SECTION_ID",
//			"ELEMENT_ID" => "ELEMENT_ID",
//		)
//	)
//);

?>

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

            <a href="/eshop_app/catalog/" class="main_button_catalog">Меню<span></span></a>
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
