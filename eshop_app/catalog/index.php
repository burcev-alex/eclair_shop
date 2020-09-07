<?php
require $_SERVER['DOCUMENT_ROOT'].'/eshop_app/headers.php';
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';
$APPLICATION->SetTitle('');
?><div>
	 <?$APPLICATION->IncludeComponent(
    'bitrix:catalog',
    'mobile',
    [
        'ACTION_VARIABLE' => 'action',
        'ADD_ELEMENT_CHAIN' => 'N',
        'ADD_PROPERTIES_TO_BASKET' => 'Y',
        'ADD_SECTIONS_CHAIN' => 'Y',
        'AJAX_MODE' => 'N',
        'AJAX_OPTION_ADDITIONAL' => '',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_STYLE' => 'Y',
        'ALSO_BUY_ELEMENT_COUNT' => '3',
        'ALSO_BUY_MIN_BUYES' => '2',
        'BASKET_URL' => SITE_DIR.'eshop_app/personal/cart/',
        'CACHE_FILTER' => 'N',
        'CACHE_GROUPS' => 'Y',
        'CACHE_TIME' => '36000000',
        'CACHE_TYPE' => 'N',
        'COMPATIBLE_MODE' => 'Y',
        'CONVERT_CURRENCY' => 'N',
        'DETAIL_BACKGROUND_IMAGE' => '-',
        'DETAIL_BROWSER_TITLE' => '-',
        'DETAIL_CHECK_SECTION_ID_VARIABLE' => 'N',
        'DETAIL_META_DESCRIPTION' => '-',
        'DETAIL_META_KEYWORDS' => '-',
        'DETAIL_OFFERS_FIELD_CODE' => ['', ''],
        'DETAIL_PROPERTY_CODE' => [
			0 => 'MANUFACTURER', 
			1 => 'MATERIAL', 
			2 => 'COLOR', 
			3 => 'WIDTH', 
			4 => 'LENGHT', 
			5 => 'SIZE', 
			6 => 'STORAGE_COMPARTMENT', 
			7 => 'HEIGHT', 
			8 => 'DEPTH', 
			9 => 'SHELVES', 
			10 => 'CORNER', 
			11 => 'SEATS', 
			12 => 'MORE_PHOTO'
		],
        'DETAIL_SET_CANONICAL_URL' => 'N',
        'DETAIL_SET_VIEWED_IN_COMPONENT' => 'N',
        'DETAIL_STRICT_SECTION_CHECK' => 'N',
        'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'N',
        'DISPLAY_TOP_PAGER' => 'N',
        'ELEMENT_SORT_FIELD' => 'sort',
        'ELEMENT_SORT_FIELD2' => 'id',
        'ELEMENT_SORT_ORDER' => 'asc',
        'ELEMENT_SORT_ORDER2' => 'desc',
        'FIELDS' => ['', ''],
        'FILTER_FIELD_CODE' => ['NAME', ''],
        'FILTER_NAME' => '',
        'FILTER_OFFERS_FIELD_CODE' => ['', ''],
        'FILTER_OFFERS_PROPERTY_CODE' => ['', ''],
        'FILTER_PRICE_CODE' => ['BASE'],
        'FILTER_PROPERTY_CODE' => ['', ''],
        'FORUM_ID' => '',
        'GIFTS_DETAIL_BLOCK_TITLE' => 'Выберите один из подарков',
        'GIFTS_DETAIL_HIDE_BLOCK_TITLE' => 'N',
        'GIFTS_DETAIL_PAGE_ELEMENT_COUNT' => '4',
        'GIFTS_DETAIL_TEXT_LABEL_GIFT' => 'Подарок',
        'GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE' => 'Выберите один из товаров, чтобы получить подарок',
        'GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE' => 'N',
        'GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT' => '4',
        'GIFTS_MESS_BTN_BUY' => 'Выбрать',
        'GIFTS_SECTION_LIST_BLOCK_TITLE' => 'Подарки к товарам этого раздела',
        'GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE' => 'N',
        'GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT' => '4',
        'GIFTS_SECTION_LIST_TEXT_LABEL_GIFT' => 'Подарок',
        'GIFTS_SHOW_DISCOUNT_PERCENT' => 'Y',
        'GIFTS_SHOW_IMAGE' => 'Y',
        'GIFTS_SHOW_NAME' => 'Y',
        'GIFTS_SHOW_OLD_PRICE' => 'Y',
        'HIDE_NOT_AVAILABLE' => 'N',
        'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
        'IBLOCK_ID' => '4',
        'IBLOCK_TYPE' => 'catalog',
        'INCLUDE_SUBSECTIONS' => 'Y',
        'LINE_ELEMENT_COUNT' => '1',
        'LINK_ELEMENTS_URL' => 'link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#',
        'LINK_IBLOCK_ID' => '',
        'LINK_IBLOCK_TYPE' => '',
        'LINK_PROPERTY_SID' => '',
        'LIST_BROWSER_TITLE' => '-',
        'LIST_META_DESCRIPTION' => '-',
        'LIST_META_KEYWORDS' => '-',
        'LIST_OFFERS_FIELD_CODE' => ['', ''],
        'LIST_OFFERS_LIMIT' => '5',
        'LIST_PROPERTY_CODE' => [
			0 => 'MANUFACTURER',
			1 => 'MATERIAL', 
			2 => 'COLOR', 
			3 => 'WIDTH', 
			4 => 'LENGHT', 
			5 => 'SIZE', 
			6 => 'STORAGE_COMPARTMENT', 
			7 => 'HEIGHT', 
			8 => 'DEPTH', 
			9 => 'LIGHTS', 
			10 => 'SHELVES', 
			11 => 'CORNER', 
			12 => 'SEATS', 
			13 => 'WEIGHT', 
			14 => 'CRUST'
		],
        'MAIN_TITLE' => 'Наличие на складах',
        'MESSAGES_PER_PAGE' => '10',
        'MESSAGE_404' => '',
        'MIN_AMOUNT' => '10',
        'OFFERS_SORT_FIELD' => 'sort',
        'OFFERS_SORT_FIELD2' => 'id',
        'OFFERS_SORT_ORDER' => 'asc',
        'OFFERS_SORT_ORDER2' => 'desc',
        'PAGER_BASE_LINK_ENABLE' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000000',
        'PAGER_SHOW_ALL' => 'N',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_TEMPLATE' => 'arrows',
        'PAGER_TITLE' => 'Товары',
        'PAGE_ELEMENT_COUNT' => '25',
        'PARTIAL_PRODUCT_PROPERTIES' => 'N',
        'PATH_TO_SMILE' => '/bitrix/images/forum/smile/',
        'POST_FIRST_MESSAGE' => 'N',
        'PRICE_CODE' => ['BASE'],
        'PRICE_VAT_INCLUDE' => 'Y',
        'PRICE_VAT_SHOW_VALUE' => 'N',
        'PRODUCT_ID_VARIABLE' => 'id',
        'PRODUCT_PROPS_VARIABLE' => 'prop',
        'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
        'REVIEW_AJAX_POST' => 'Y',
        'SECTION_BACKGROUND_IMAGE' => '-',
        'SECTION_COUNT_ELEMENTS' => 'N',
        'SECTION_ID_VARIABLE' => 'SECTION_ID',
        'SECTION_TOP_DEPTH' => '1',
        'SEF_MODE' => 'N',
        'SET_LAST_MODIFIED' => 'N',
        'SET_STATUS_404' => 'Y',
        'SET_TITLE' => 'N',
        'SHOW_404' => 'N',
        'SHOW_DEACTIVATED' => 'N',
        'SHOW_EMPTY_STORE' => 'Y',
        'SHOW_GENERAL_STORE_INFORMATION' => 'N',
        'SHOW_LINK_TO_FORUM' => 'Y',
        'SHOW_PRICE_COUNT' => '1',
        'SHOW_TOP_ELEMENTS' => 'N',
        'STORES' => ['', ''],
        'STORE_PATH' => '/store/#store_id#',
        'URL_TEMPLATES_READ' => '',
        'USER_CONSENT' => 'N',
        'USER_CONSENT_ID' => '0',
        'USER_CONSENT_IS_CHECKED' => 'Y',
        'USER_CONSENT_IS_LOADED' => 'N',
        'USER_FIELDS' => ['', ''],
        'USE_ALSO_BUY' => 'Y',
        'USE_CAPTCHA' => 'Y',
        'USE_COMPARE' => 'N',
        'USE_ELEMENT_COUNTER' => 'Y',
        'USE_FILTER' => 'Y',
        'USE_GIFTS_DETAIL' => 'Y',
        'USE_GIFTS_MAIN_PR_SECTION_LIST' => 'Y',
        'USE_GIFTS_SECTION' => 'Y',
        'USE_MAIN_ELEMENT_SECTION' => 'N',
        'USE_MIN_AMOUNT' => 'Y',
        'USE_PRICE_COUNT' => 'N',
        'USE_PRODUCT_QUANTITY' => 'Y',
        'USE_REVIEW' => 'Y',
        'USE_STORE' => 'Y',
        'USE_STORE_PHONE' => 'N',
        'USE_STORE_SCHEDULE' => 'N',
        'VARIABLE_ALIASES' => [
			'ELEMENT_ID' => 'ELEMENT_ID', 
			'SECTION_ID' => 'SECTION_ID'
		],
		"OFFER_TREE_PROPS" => array(
			"KRUPA",
			"BUTTER",
			"PREPARED_ON",
			"SOUCE",
			"FILLING",
			"TASTE",
			"COMPLECT",
			"EGG",
			"COFFEE",
			"FILL",
			"GAS",
			"CAKES_SET",
			"PIROGI_SET",
		),
		"DETAIL_OFFER_TREE_PROPS" => array(
			"KRUPA",
			"BUTTER",
			"PREPARED_ON",
			"SOUCE",
			"FILLING",
			"TASTE",
			"COMPLECT",
			"EGG",
			"COFFEE",
			"FILL",
			"GAS",
			"CAKES_SET",
			"PIROGI_SET",
		),
		"OFFERS_CART_PROPERTIES" => array(
			"KRUPA",
			"BUTTER",
			"PREPARED_ON",
			"SOUCE",
			"FILLING",
			"TASTE",
			"COMPLECT",
			"EGG",
			"COFFEE",
			"FILL",
			"GAS",
			"CAKES_SET",
			"PIROGI_SET",
		),
		"LIST_OFFERS_FIELD_CODE" => array(),
		"LIST_OFFERS_PROPERTY_CODE" => array(
			"KRUPA",
			"BUTTER",
			"PREPARED_ON",
			"SOUCE",
			"FILLING",
			"TASTE",
			"COMPLECT",
			"EGG",
			"COFFEE",
			"FILL",
			"GAS",
			"CAKES_SET",
			"PIROGI_SET",
			"MORE_PHOTO",
		),
		"PRODUCT_DISPLAY_MODE" => "Y",
		"PRODUCT_PROPERTIES" => array(),
    ]
); ?>
</div>
<div>
 <br>
</div>
<div>
</div><?require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php'; ?>