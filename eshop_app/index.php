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