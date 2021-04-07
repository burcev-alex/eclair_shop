<?php
use Bitrix\Sale\Delivery\Restrictions;
use Bitrix\Sale\Internals\Entity;

class MyDeliveryRestriction extends Restrictions\Base
{
    public static function getClassTitle()
    {
        return 'Сумма заказа';
    }

    //https://mrcappuccino.ru/blog/post/delivery-restrictions-bitrix-d7


    public static function getClassDescription()
    {
        return 'Бесплатные зоны доставки при сумме заказа';
    }

    public static function check($ShipmentParams, array $restrictionParams, $deliveryId = 0)
    {
        $res = true;

        foreach($restrictionParams as $key=>$param){
            if($ShipmentParams['zone'] == $key){
                if($ShipmentParams['sum'] >=$param){
                    $res = false;
                }
            }
        }

        return $res;
    }
    protected static function extractParams(Bitrix\Sale\Shipment $shipment)
    {
        $ShipmentParams = array();
        $sum = 0;
        // Получаем товары в корзине:
        foreach ($shipment->getShipmentItemCollection() as $shipmentItem) {
            /** @var \Bitrix\Sale\BasketItem $basketItem - запись в корзине*/
            $basketItem = $shipmentItem->getBasketItem();
            $sum += $basketItem->getFinalPrice();
        }

        $price = $_COOKIE['yaPrice'];
        $yaStopAddress = $_COOKIE['yaStopAddress'];
        $yaZone = $_COOKIE['yaZone'];

        $ShipmentParams['zone'] = $yaZone;
        $ShipmentParams['sum'] = $sum;

      /*  print $price;
        print $sum;
        print $yaStopAddress;
        die();*/

        return $ShipmentParams;
    }
    public static function getParamsStructure($entityId = 0)
    {
        return array(
            "tsentralnyy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "1500",
                'LABEL' => 'Центральный'
            ),
            "zheleznodorozhnyy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "1500",
                'LABEL' => 'Железнодорожный'
            ),
            "oktyabrskiy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "2000",
                'LABEL' => 'Октябрьский'
            ),
            "dzerzhinskiy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "2000",
                'LABEL' => 'Дзержинский'
            ),
            "kalininskiy-rayon" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "2000",
                'LABEL' => 'Калининский'
            ),
            "zaeltsovskiy-rayon" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "2000",
                'LABEL' => 'Заельцовский'
            ),
            "leninskiy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "1500",
                'LABEL' => 'Ленинский'
            ),
            "kirovskiy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "3000",
                'LABEL' => 'Кировский'
            ),
            "sovetskiy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "5000",
                'LABEL' => 'Советский'
            ),
            "pervomayskiy" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "5000",
                'LABEL' => 'Первомайский'
            ),
        );
    }
}