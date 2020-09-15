<?php

namespace App\Integration\Handlers;

use App\Integration;
use Bitrix\Main;
use Bitrix\Sale;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\OrderPropsValueTable;

Loc::loadMessages(__FILE__);

/**
 * Class Order.
 */
class Order
{
    /**
     * Вызывается после добавления заказа.
     *
     * @return bool
     *
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public static function onSaleOrderSaved(Main\Event $event)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('sale');

        /** @var Order $order */
        $order = $event->getParameter('ENTITY');
        $oldValues = $event->getParameter('VALUES');
        $isNew = $event->getParameter('IS_NEW');

        $result = OrderPropsValueTable::getList([
            'select' => ['ID', 'NAME', 'VALUE', 'CODE', 'ORDER_PROPS_ID'],
            'filter' => ['ORDER_ID' => $order->getId()],
         ]);
        while ($row = $result->fetch()) {
            $propertyValues[$row['CODE']] = $row;
		}

		// собираем по продукции
		$arrBaskets = [];
		foreach ($order->getBasket() as $basketItem) {
			$arrProperty = [];
			$basketPropRes = Sale\Internals\BasketPropertyTable::getList(array(
				'filter' => array(
				   "BASKET_ID" => $basketItem->getId(),
				),
			 ));
			 
			 while ($property = $basketPropRes->fetch()) {
				$arrProperty[$property['CODE']] = $property['VALUE'];
			 }

			$arrBaskets[] = [
				"id" => $basketItem->getField('ID'),
				"name" => $basketItem->getField('NAME'),
				"quantity" => $basketItem->getQuantity(),
				"price" => $basketItem->getPrice(),
				"sum" => $basketItem->getFinalPrice(),
				"offerId" => $basketItem->getProductId(),
				"property" => $arrProperty
			];	
		}

        // передача запроса в CRM
        $fields = [
			'id' => $order->getId(),
			'statusId' => $order->getField('STATUS_ID'),
			'price' => $order->getPrice(),
			'dateInsert' => $order->getDateInsert()->format('d.m.Y H:i:s'),
			'discoutPrice' => $order->getDiscountPrice(),
			'currency' => $order->getCurrency(),
			'basket' => $arrBaskets,
			'profile' => [
				'fullName' => $propertyValues['FIO']['VALUE'],
				'email' => $propertyValues['EMAIL']['VALUE'],
				'phone' => $propertyValues['PHONE']['VALUE'],
				'zip' => $propertyValues['ZIP']['VALUE'],
				'city' => $propertyValues['CITY']['VALUE'],
				'address' => $propertyValues['ADDRESS']['VALUE'],
			]
		];

        $endpoint = new Integration\Rest\Client\Crm();
		$response = $endpoint->order("add", $fields);
		p2f($response);

        return true;
    }
}
