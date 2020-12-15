<?php
namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;
use Bitrix\Main\Web\Json;

class CustomHandler extends Base
{
    public static function getClassTitle()
    {
        return 'Доставка курьером Eclair';
    }

    public static function getClassDescription()
    {
        return 'Доставка заказа собственным курьером';
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
    {
        $order = $shipment->getCollection()->getOrder();
        $orderprice = $order->getPrice();
        $result = new CalculationResult();

        $priceList = $this->config["RAYON"];
        $sumList = $this->config["ORDER_SUM_FREE"];
        $priceListOr = $this->getConfigStructure();
        $price = 0;
        $choosenID = 'METRO';
        if(isset($_COOKIE['eclairDelivery'])){
            $deliveryChoosen = Json::decode($_COOKIE['eclairDelivery']);
            $price =   $deliveryChoosen['value'];
            $choosenID = $deliveryChoosen['delivery'];
        }
        if($sumList[$choosenID] <= $orderprice){ // если сумма заказа больше или равно указанному лимиту
            $price = 0;
        }
        $description= "<div class=''  id='select_rayon_delivery'><label>Выберите район доставки</label>";

        foreach($priceListOr["RAYON"]["ITEMS"] as $key=>$priceItem){
            $selected = '';
            if($key == $choosenID)$selected = "checked='checked'";
            $pricel = $priceList[$key];
            if($sumList[$key] <= $orderprice){
                $pricel = 0;
            }
            $description .= "<div ><label><input value='".$priceList[$key]."' data-id='".$key."' data-name='".$priceItem['NAME']."' type='radio' ".$selected." name='rayon' />".$priceItem['NAME']." (".$pricel." руб.)</label></div>";
        }

        $description .= "</div>";
        $description .= "<div class='block-rayons-free-delivery col-lg-12 col-sm-12 col-xs-12 ' style='display: none'><b>".$priceListOr["ORDER_SUM_FREE"]["TITLE"]."</b>";

        foreach($priceListOr["ORDER_SUM_FREE"]["ITEMS"] as $ordKey=>$orderFree){
            $description .= "<div class=''>".$orderFree["NAME"].": от ".$sumList[$ordKey]." руб</div>";
        }



        $description .= "</div>";

        $result->setDescription($description);
        $result->setDeliveryPrice($price);
        return $result;
    }

    protected function getConfigStructure()
    {
        return array(
            "RAYON" => array(
                "TITLE" => 'Районы доставки',
                "DESCRIPTION" => 'Список',
                "ITEMS" => array(
                    "METRO" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => '100 м от станции метро'
                    ),
                    "KALININ" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Калининский район'
                    ),
                    "ZAELZ" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Заельцовский район'
                    ),
                    "DZERZH" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Дзержинский район'
                    ),
                    "ZHELDOR" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Железнодорожный район'
                    ),
                    "OKT" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Октябрьский район'
                    ),
                    "CENTR" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Центральный район'
                    ),
                    "LENIN" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Ленинский район'
                    ),
                    "KIROV" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Кировский район'
                    ),
                    "PERVOMAY" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Первомайский район'
                    ),
                    "SOWET" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Советский район'
                    ),
                )
            ),
            "ORDER_SUM_FREE" => [

                "TITLE" => 'Сумма заказа для бесплатной доставки по районам',
                "DESCRIPTION" => 'Список',
                "ITEMS" => array(
                    "KALININ" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Калининский район'
                    ),
                    "ZAELZ" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Заельцовский район'
                    ),
                    "DZERZH" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Дзержинский район'
                    ),
                    "ZHELDOR" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Железнодорожный район'
                    ),
                    "OKT" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Октябрьский район'
                    ),
                    "CENTR" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Центральный район'
                    ),
                    "LENIN" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Ленинский район'
                    ),
                    "KIROV" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Кировский район'
                    ),
                    "PERVOMAY" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Первомайский район'
                    ),
                    "SOWET" => array(
                        "TYPE" => "NUMBER",
                        "MIN" => 0,
                        "NAME" => 'Советский район'
                    )
                )
            ]
        );
    }

    public function isCalculatePriceImmediately()
    {
        return true;
    }

    public static function whetherAdminExtraServicesShow()
    {
        return true;
    }
}
?>