<?php

use Bitrix\Main;
use Bitrix\Main\Localization;
use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBitrixPersonalOrder extends CBitrixComponent
{

	public function executeComponent()
	{
		$this->useModules();

        $basketcount = CSaleBasket::GetList(false, array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", 'DELAY' => 'N'), false, false, array("ID"))->SelectedRowsCount();
        if ($basketcount < 1)
            LocalRedirect(SITE_DIR."/personal/cart/");

        $this->templatefile='';
        $this->arResult['DATA']=$_SESSION['ORDER']['USER'];
        if(empty($this->arResult['DATA']) && $GLOBALS['USER']->IsAuthorized()) {
            $this->arResult['DATA'] = CUser::GetList(($by = "id"), ($order = "asc"), [$GLOBALS['USER']->GetID()],['FIELDS'=>['SECOND_NAME','LAST_NAME','NAME','EMAIL','PERSONAL_PHONE']])->fetch();
            $this->arResult['DATA']['NAME']=$this->cname($this->arResult['DATA']['NAME'],$this->arResult['DATA']['LAST_NAME'],$this->arResult['DATA']['SECOND_NAME']);
        }
		$this->getRequest();

		$this->includeComponentTemplate($this->templatefile);
	}
	public function useModules(){
        CModule::IncludeModule("sale");
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
    }
    protected function getRequest()
    {
        $action=$this->request->get('action');
        if (is_callable(array($this, $action."Action")))
        {
            $this->fieldlist=array();
            return call_user_func(
                array($this, $action."Action")
            );
        }

    }
    private function loadBasket()
    {
        $registry = Bitrix\Sale\Registry::getInstance(Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);

        /** @var Sale\Basket $basketClassName */
        $basketClassName = $registry->getBasketClassName();

        return $basketClassName::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), SITE_ID);
    }
    private function getPropertyByCode($propertyCollection, $code)  {
        foreach ($propertyCollection as $property)
        {
            if($property->getField('CODE') == $code || $property->getField('ID') == $code)
                return $property;
        }
    }
    protected function order5Action(){
        $siteId = \Bitrix\Main\Context::getCurrent()->getSite();

        $currencyCode = Bitrix\Main\Config\Option::get('sale', 'default_currency', 'RUB');

        Bitrix\Sale\DiscountCouponsManager::init();
        $user=$GLOBALS['USER']->GetID();
        if(empty($user))
            $user=\CSaleUser::GetAnonymousUserID();
        $order = Bitrix\Sale\Order::create($siteId, $user);

        $order->setPersonTypeId(1);
        $basket = Bitrix\Sale\Basket::loadItemsForFUser(\CSaleBasket::GetBasketUserID(), $siteId)->getOrderableItems();

        $order->setBasket($basket);

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem(
            Bitrix\Sale\Delivery\Services\Manager::getObjectById($_SESSION['ORDER']['DELIVERY']['DELIVERY'])
        );

        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        foreach ($basket as $basketItem)
        {
            $item = $shipmentItemCollection->createItem($basketItem);
            $item->setQuantity($basketItem->getQuantity());
        }

        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem(
            Bitrix\Sale\PaySystem\Manager::getObjectById($_SESSION['ORDER']['PAYS']['PAY'])
        );
        $payment->setField("SUM", $order->getPrice());
        $payment->setField("CURRENCY", $order->getCurrency());

        $order->doFinalAction(true);
        $propertyCollection = $order->getPropertyCollection();

        $Property = $this->getPropertyByCode($propertyCollection, 'FIO');
        $Property->setValue($_SESSION['ORDER']['USER']['NAME']);
        $Property = $this->getPropertyByCode($propertyCollection, 'EMAIL');
        $Property->setValue($_SESSION['ORDER']['USER']['EMAIL']);
        $Property = $this->getPropertyByCode($propertyCollection, 'PHONE');
        $Property->setValue($_SESSION['ORDER']['USER']['PERSONAL_PHONE']);
        if(!empty($_SESSION['ORDER']['DELIVERY']['STREET'])) {
            $Property = $this->getPropertyByCode($propertyCollection, 'STREET');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['STREET']);
        }
        if(!empty($_SESSION['ORDER']['DELIVERY']['FLOOR'])) {
            $Property = $this->getPropertyByCode($propertyCollection, 'FLOOR');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['FLOOR']);
        }
        if(!empty($_SESSION['ORDER']['DELIVERY']['podezd'])) {
            $Property = $this->getPropertyByCode($propertyCollection, 'podezd');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['ENTRANCE']);
        }
        if(!empty($_SESSION['ORDER']['DELIVERY']['FLAT'])) {
            $Property = $this->getPropertyByCode($propertyCollection, 'FLAT');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['FLAT']);
        }
        if(!empty($_SESSION['ORDER']['DELIVERY']['HOME'])) {
            $Property = $this->getPropertyByCode($propertyCollection, 'HOME');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['HOME']);
        }

        if($_SESSION['ORDER']['DELIVERY']['TIME']=='N')
            $time='Ближайшее время';
        else
            $time=$_SESSION['ORDER']['DELIVERY']['TIMESELECT'].' '.$_SESSION['ORDER']['DELIVERY']['TIMESELECT2'];

        $Property = $this->getPropertyByCode($propertyCollection, 'TIME');
        $Property->setValue($time);

        if($_SESSION['ORDER']['DELIVERY']['CULTERY']=='N')
            $cultery=0;
        else
            $cultery=$_SESSION['ORDER']['DELIVERY']['CULTERYSELECT'];
        $Property = $this->getPropertyByCode($propertyCollection, 'QUANTITY_WARE');
        $Property->setValue($cultery);
        if($_SESSION['ORDER']['USER']['WHATSAPP']=='Y')
            $vidsvyazi=1;
        else
            $vidsvyazi=0;
        $Property = $this->getPropertyByCode($propertyCollection, 'VIDSVYAZI');
        $Property->setValue($vidsvyazi);

        $order->setField('CURRENCY', $currencyCode);
        $order->setField('USER_DESCRIPTION', $_SESSION['ORDER']['DELIVERY']['COMMENT']);

        $order->save();

        $this->templatefile='step5';
    }

    protected function order4Action(){
        if(!empty($_POST)){
            $_SESSION['ORDER']['PAYS']=$_POST['FIELDS'];
        }
        $this->templatefile='step4';
        $this->arResult['BASKET'] = [];

        $basketStorage = \Bitrix\Sale\Basket\Storage::getInstance(\Bitrix\Sale\Fuser::getId(), SITE_ID);
        $fullBasket = $basketStorage->getBasket();
        if (!$fullBasket->isEmpty())
        {
            $orderableBasket = $basketStorage->getOrderableBasket();

            foreach ($fullBasket as $item)
            {
                if ($item->canBuy() && !$item->isDelay())
                {
                    $item = $orderableBasket->getItemByBasketCode($item->getBasketCode());
                }
                $propertyCollection = $item->getPropertyCollection();
                $basketId = $item->getBasketCode();
                $properties=[];
                foreach ($propertyCollection->getPropertyValues() as $property)
                {
                    if ($property['CODE'] == 'CATALOG.XML_ID' || $property['CODE'] == 'PRODUCT.XML_ID' || $property['CODE'] == 'SUM_OF_CHARGE')
                        continue;

                    $property = array_filter($property, ['CSaleBasketHelper', 'filterFields']);
                    $property['BASKET_ID'] = $basketId;

                    $properties[] = $property;
                }
                $basketItem = $item->getFieldValues();
                $basketItem['PROPS']=$properties;
                foreach($basketItem['PROPS'] as $key=>$val){
                    if($val['CODE']=='CML2_LINK')
                        unset($basketItem['PROPS'][$key]);
                    else
                        $basketItem['PROPS'][$key]=$val['VALUE'];
                }
                $basketItem['QUANTITY'] = $item->getQuantity();

                $basketItem['WEIGHT'] = (float)$basketItem['WEIGHT'];
                $basketItem['WEIGHT_FORMATED'] = roundEx($basketItem['WEIGHT'] / $this->weightKoef, SALE_WEIGHT_PRECISION).' '.$this->weightUnit;

                $basketItem['PRICE'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['PRICE']);
                $basketItem['PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($basketItem['PRICE'], $basketItem['CURRENCY'], true);

                $basketItem['FULL_PRICE'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['BASE_PRICE']);
                $basketItem['FULL_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($basketItem['FULL_PRICE'], $basketItem['CURRENCY'], true);

                $basketItem['DISCOUNT_PRICE'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['DISCOUNT_PRICE']);
                $basketItem['DISCOUNT_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($basketItem['DISCOUNT_PRICE'], $basketItem['CURRENCY'], true);

                $basketItem['SUM_VALUE'] = $basketItem['PRICE'] * $basketItem['QUANTITY'];
                $basketItem['SUM'] = CCurrencyLang::CurrencyFormat($basketItem['SUM_VALUE'], $basketItem['CURRENCY'], true);

                $basketItem['SUM_FULL_PRICE'] = $basketItem['FULL_PRICE'] * $basketItem['QUANTITY'];
                $basketItem['SUM_FULL_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($basketItem['SUM_FULL_PRICE'], $basketItem['CURRENCY'], true);

                $basketItem['SUM_DISCOUNT_PRICE'] = $basketItem['DISCOUNT_PRICE'] * $basketItem['QUANTITY'];
                $basketItem['SUM_DISCOUNT_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($basketItem['SUM_DISCOUNT_PRICE'], $basketItem['CURRENCY'], true);

                $basketItem['PRICE_VAT_VALUE'] = $basketItem['VAT_VALUE']
                    = ($basketItem['PRICE'] * $basketItem['QUANTITY'] / ($basketItem['VAT_RATE'] + 1)) * $basketItem['VAT_RATE'] / $basketItem['QUANTITY'];

                $basketItem['DISCOUNT_PRICE_PERCENT'] = 0;
                if ($basketItem['CUSTOM_PRICE'] !== 'Y')
                {
                    $basketItem['DISCOUNT_PRICE_PERCENT'] = \Bitrix\Sale\Discount::calculateDiscountPercent(
                        $basketItem['FULL_PRICE'],
                        $basketItem['DISCOUNT_PRICE']
                    );
                    if ($basketItem['DISCOUNT_PRICE_PERCENT'] === null)
                        $basketItem['DISCOUNT_PRICE_PERCENT'] = 0;
                }
                $this->arResult['BASKET'][$item->getId()] = $basketItem;
            }
        }
        $this->arResult['DELIVERYIFS']=$this->deliveryifs();
        if(!empty($_SESSION['ORDER']['DELIVERY']['DELIVERY'])){
            $delivery = CSaleDelivery::GetList(
                array(
                    "SORT" => "ASC",
                    "NAME" => "ASC"
                ),
                array(
                    "LID" => SITE_ID,
                    "ACTIVE" => "Y",
                    "ID"=>$_SESSION['ORDER']['DELIVERY']['DELIVERY']
                )
            )->fetch();
            if($delivery['NAME']!='Самовывоз')
                $delivery['NAME']=$delivery['NAME'].'. Бесплатно от '.$this->arResult['DELIVERYIFS'][$delivery['ID']]['FREE_PRICE'].' ₽';
            $this->arResult['DELIVERY']=$delivery;
        }
        $this->arResult['SUM']=0;
        foreach( $this->arResult['BASKET'] as $key=>$val){
            $this->arResult['SUM']=$this->arResult['SUM']+$val['SUM_VALUE'];
        }
    }
    protected function order3Action(){
        if(!empty($_POST)){
            $_SESSION['ORDER']['DELIVERY']=$_POST['FIELDS'];
        }

        $this->arResult['DATA']=$_SESSION['ORDER']['PAYS'];
        if(empty($this->arResult['DATA']['PAY']))
            $this->arResult['DATA']['PAY']=1;
        $this->templatefile='step3';
        $db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("LID"=>SITE_ID, "ACTIVE"=>"Y"));
        $this->arResult['PAYS']=[];
        while ($ptype = $db_ptype->Fetch())
        {
            $this->arResult['PAYS'][]=$ptype;
        }
    }
    protected function order2Action(){
        if(!empty($_POST)){
            $_SESSION['ORDER']['USER']=$_POST['FIELDS'];
        }
        $this->arResult['DATA']=$_SESSION['ORDER']['DELIVERY'];
        $this->templatefile='step2';
        $db_dtype = CSaleDelivery::GetList(
            array(
                "SORT" => "ASC",
                "NAME" => "ASC"
            ),
            array(
                "LID" => SITE_ID,
                "ACTIVE" => "Y",
            )
        );
        while($ar_dtype = $db_dtype->Fetch())
        {
            if($ar_dtype['NAME']=='Самовывоз' || $ar_dtype['NAME']=='До станции метро')
                $this->arResult['PICKUP'][]=$ar_dtype;
            else
                $this->arResult['DELIVERIS'][]=$ar_dtype;
        }
        $this->arResult['DATES']=[$this->createdate(date('j',strtotime("now")),date('n',strtotime("now")))];

        $this->arResult['TIMES']=[];
        foreach (range(1, 9) as $number) {
            $this->arResult['DATES'][]=$this->createdate(date('j',strtotime("+".$number." day")),date('n',strtotime("+".$number." day")));
        }
        $this->arResult['CURTIME']= $this->ceildec(date('H'));
        $this->arResult['CURTIMES']='';
        foreach (range(0, 23) as $number) {
            if($number>=$this->arResult['CURTIME'])
                $this->arResult['CURTIMES'].='<option '.($this->arResult['DATA']['TIMESELECT2']==$number.':00'?'selected':'').'>'.$number.':00</option>';
            $this->arResult['TIMES'][]=$number;
        }
        $this->arResult['DELIVERYIFS']=$this->deliveryifs();
    }
    function ceildec($val){
        return ceil($val/10) * 10;
    }
    public function createdate($d,$m){
        $arr = [
            'январь',
            'февраль',
            'март',
            'апрель',
            'май',
            'июнь',
            'июль',
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь'
        ];

        $m = $arr[$m-1];
	    return $d.' '.$m;
    }
    protected function deliveryifs(){
        return  [
            23 => ['FREE_PRICE'=>2000,'PRICE'=>200], //Дзержинский
            25 => ['FREE_PRICE'=>2000,'PRICE'=>200], //ЖД
            21 => ['FREE_PRICE'=>1500,'PRICE'=>200], //Заельц
            22 => ['FREE_PRICE'=>2000,'PRICE'=>200], //Калинин
            28 => ['FREE_PRICE'=>3000,'PRICE'=>350], //КИровский
            27 => ['FREE_PRICE'=>2500,'PRICE'=>250], //Ленинский
            26 => ['FREE_PRICE'=>2000,'PRICE'=>200], //Окт
            29 => ['FREE_PRICE'=>5000,'PRICE'=>500], //Первомайский
            30 => ['FREE_PRICE'=>5000,'PRICE'=>500], //Советский
            24 => ['FREE_PRICE'=>1500,'PRICE'=>200] //ЦЕнтральный
        ];
    }
    public function cname($name,$lastname,$secondname){
        if(!empty($lastname)){
            $result=$name.' '.$lastname.' '.$secondname;
        }elseif(!empty($secondname)){
            $result=$name.' '.$secondname;
        }else{
            $result=$name;
        }
        $result=trim($result);
        return $result;
    }
}