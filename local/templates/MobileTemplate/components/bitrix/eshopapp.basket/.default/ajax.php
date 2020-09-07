<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

set_time_limit(0);

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('APP_EVENT_HANDLERS_DISABLED', true);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

global $USER_FIELD_MANAGER, $USER, $DB, $APPLICATION;

use Bitrix\Sale;

CModule::IncludeModule('sale');

header('Content-Type: application/json; charset=utf-8');


$result = [
	'status' => 'error'
];

$entityBasket = new Sale\Internals\BasketTable();
$entityProperty = new Sale\Internals\BasketPropertyTable();

if($_POST['ACTION'] == 'change'){
	// изменение ТП по товару корзины
	$result['status'] = 'ok';

	$basketRes = Sale\Internals\BasketTable::getList(array(
		'filter' => array(
			'FUSER_ID' => Sale\Fuser::getId(), 
			'ID' => $_POST['ID']
		)
	));
	
	while ($item = $basketRes->fetch()) {
		$result['BASE'] = $item;

		$arFields = [
			'PRODUCT_ID' => $_POST['PRODUCT_ID'],
			'PRICE' => $_POST['PRICE']
		];
		$entityBasket->Update($item['ID'], $arFields);
		
		$basketPropRes = Sale\Internals\BasketPropertyTable::getList(array(
			'filter' => array(
			   "BASKET_ID" => $item['ID'],
			),
		 ));
		 
		while ($property = $basketPropRes->fetch()) {
			foreach($_POST['PROPS'] as $code => $value){
				if($code == $property['CODE']){
					$arFields = [
						'VALUE' => $value
					];
					$entityProperty->Update($property['ID'], $arFields);
				}
			}
		}

	}
}

echo json_encode($result);