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
        if(empty($_GET['ORDER_ID'])) {
            $basketcount = CSaleBasket::GetList(false, array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", 'DELAY' => 'N'), false, false, array("ID"))->SelectedRowsCount();
            if ($basketcount < 1)
                LocalRedirect("/personal/cart/");
        }
        $this->templatefile='';
        $this->arResult['DATA']=$_SESSION['ORDER']['USER'];
        if(empty($this->arResult['DATA']['USER']) && $GLOBALS['USER']->IsAuthorized()) {
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
	    if(empty($_GET['ORDER_ID'])){
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
            $Property = $this->getPropertyByCode($propertyCollection, 'STREET');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['STREET']);
            $Property = $this->getPropertyByCode($propertyCollection, 'FLOOR');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['FLOOR']);
            $Property = $this->getPropertyByCode($propertyCollection, 'podezd');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['ENTRANCE']);
            $Property = $this->getPropertyByCode($propertyCollection, 'FLAT');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['FLAT']);
            $Property = $this->getPropertyByCode($propertyCollection, 'HOME');
            $Property->setValue($_SESSION['ORDER']['DELIVERY']['HOME']);

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
                $vidsvyazi=2;
            else
                $vidsvyazi=1;
            $Property = $this->getPropertyByCode($propertyCollection, 'VIDSVYAZI');
            $Property->setValue($vidsvyazi);

            $order->setField('CURRENCY', $currencyCode);
            $order->setField('USER_DESCRIPTION', $_SESSION['ORDER']['DELIVERY']['COMMENT']);

            $order->save();

            $orderId = $order->GetId();
            LocalRedirect("?action=order5&ORDER_ID=".$orderId);
        }
        $this->templatefile='step5';
    }

    protected function order4Action(){
        if(!empty($_POST)){
            $_SESSION['ORDER']['PAYS']=$_POST['FIELDS'];
        }
        $this->templatefile='step4';
        $this->arResult['BASKET'] = [];

        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL",
                "DELAY"=>'N'
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE","PRODUCT_ID", "QUANTITY", "PRICE", "WEIGHT")
        );
        $prods=[];
        while ($arItems = $dbBasketItems->Fetch())
        {
            if (strlen($arItems["CALLBACK_FUNC"]) > 0)
            {
                CSaleBasket::UpdatePrice($arItems["ID"],
                    $arItems["CALLBACK_FUNC"],
                    $arItems["MODULE"],
                    $arItems["PRODUCT_ID"],
                    $arItems["QUANTITY"]);
                $arItems = CSaleBasket::GetByID($arItems["ID"]);
            }
            $prods[]=$arItems['PRODUCT_ID'];
            $this->arResult['BASKET'][] = $arItems;
        }
        $res = \Bitrix\Catalog\ProductTable::getList(array(
            'filter' => array('=ID'=>$prods),
            'select' => array('ID','NAME'=>'IBLOCK_ELEMENT.NAME','PREVIEW_TEXT'=>'IBLOCK_ELEMENT.PREVIEW_TEXT'),
        ));

        $prods=[];
        while($ob = $res->fetch())
        {
            $prods[$ob['ID']]=$ob['NAME'];
        }
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
            $delivery['QUANTITY']=1;

            $this->arResult['BASKET'][]=$delivery;
        }
        $this->arResult['SUM']=0;
        foreach( $this->arResult['BASKET'] as $key=>$val){
            $this->arResult['SUM']=$this->arResult['SUM']+$val['PRICE'];
            if(!empty($prods[$val['PRODUCT_ID']]))
                $this->arResult['BASKET'][$key]['NAME']=$prods[$val['PRODUCT_ID']];
        }
    }
    protected function order3Action(){
        if(!empty($_POST)){
            $_SESSION['ORDER']['DELIVERY']=$_POST['FIELDS'];
        }

        $this->arResult['DATA']=$_SESSION['ORDER']['PAYS'];
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
                $this->arResult['CURTIMES'].='<option>'.$number.':00</option>';
            $this->arResult['TIMES'][]=$number;
        }
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