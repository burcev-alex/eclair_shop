<?php

use Bitrix\Main\Web;
use Bitrix\Sale;

class Sber
{

    private $gate_url = 'https://securepayments.sberbank.ru/payment/rest/';
    private $returnURL = 'https://eclair.delivery/sberbank/success/';
    private $login = 'P540421785956-api';
    private $password = 'fq8fs0PKdsTY';
    private $orderId;
    private $price;
    private $order;
    private $redirectURL = '';
    private $errors = [];
    private $message = [];
    private $orderIDField = 18;

    private $orderStatus = [
        'заказ зарегистрирован, но не оплачен',
        'предавторизованная сумма удержана (для двухстадийных платежей)',
        'проведена полная авторизация суммы заказа',
        'авторизация отменена',
        'по транзакции была проведена операция возврата',
        'инициирована авторизация через сервер контроля доступа банка-эмитента',
        'авторизация отклонена'
    ];

    public function __construct(){
        if(isset($_REQUEST["ORDER"])) {
            $this->orderId = $_REQUEST["ORDER"];
            $this->initOrder();
        }
        if(isset($_REQUEST["PRICE"])) {
            $this->price = $_REQUEST["PRICE"];
        }

        if(isset($_REQUEST["orderId"])) {
            $this->findOrder($_REQUEST["orderId"]);
        }
    }

    private function findOrder($orderId){
        $dbRes = \Bitrix\Sale\Order::getList([
            'select' => ['ID','PROPERTY.*'],
            'filter' => [
                'PROPERTY.ORDER_PROPS_ID' => $this->orderIDField,
                'PROPERTY.VALUE' => $orderId
            ]
        ]);

        if ($orderAr = $dbRes->fetch()) {
            //изменить статус заказа, добавить оплату
            if (isset($orderAr["ID"])) {

                $gateResponse = $this->checkByOrderNumber($orderId);

                if($gateResponse['orderStatus'] == 2){
                    $order = \Bitrix\Sale\Order::load($orderAr["ID"]);
                    $payments = true;
                    $paymentCollection = $order->getPaymentCollection();
                    foreach ($paymentCollection as $payment)
                    {
                        $resPay = $payment->setPaid('Y');
                        if (!$resPay->isSuccess())
                        {
                            $payments = false;
                             array_merge($this->errors,$resPay->getErrorMessages());
                        }
                    }

                    if($payments){
                        $reOrder = $order->setField('STATUS_ID', 'P'); // Оплачен
                        if (!$reOrder->isSuccess())
                        {
                          array_merge($this->errors,$reOrder->getErrorMessages());
                        }
                        $resultOrder = $order->save();
                        if (!$resultOrder->isSuccess())
                        {
                            array_merge($this->errors,$resultOrder->getErrorMessages());
                        }
                    }
                    $this->message[] = 'Ваш заказ успешно оплачен! Спасибо!';
                }
                else {
                    $this->errors[] = $this->orderStatus[$gateResponse['orderStatus']];
                }
            }

        }


    }

    public function registerOrder() {

        if($this->order instanceof Bitrix\Sale\Order) {
            if($this->order->isPaid()){
                $this->message[] = 'Заказ уже оплачен';
                return false;
            }
        }

        $passed = false;

        for ($i=0; $i < 30; $i++) {

            $data['orderNumber'] = $this->orderId . "_" . $i;
            //$data['orderNumber'] = $this->orderId;
            $data['returnURL'] = $this->returnURL;
            $data['amount'] = $this->price*100;


            $gateResponse = $this->checkByOrderNumber($this->orderId . "_" . $i,$this->orderId);

            if ($gateResponse['errorCode'] != 6) {

                continue;
            }

            $formUrl = '';
            if($gateResponse['errorCode'] == 6) {

                $method = 'register.do';
                $gateResponse = $this->getReq($method,$data);

                if(isset($gateResponse['orderId'])) {

                    if(isset($gateResponse['formUrl'])){

                        $this->redirectURL = $gateResponse['formUrl'];
                    }
                    if(isset($gateResponse['orderId'])){

                        $orderIdBank = $gateResponse['orderId'];
                        $propertyCollection = $this->order->getPropertyCollection();
                        $propertyCollection->getItemByOrderPropertyId($this->orderIDField)->setValue($orderIdBank);
                        $this->order->save();
                    }
                    $passed = true;
                }
                break;

            } else if($gateResponse['errorCode'] == 0 && $gateResponse['orderStatus'] == 0) {
                // return and build payment link already registered order from gate
                foreach ($gateResponse['merchantOrderParams'] as $key => $item) {
                    if($item['name'] == 'formUrl') {
                        $this->redirectURL = $item['value'];
                        $passed = true;
                        break;
                    }
                }

                break;
            } else if($gateResponse['errorCode'] == 0 && $gateResponse['orderStatus'] == 2) {
                // order allready payed

                $this->message[] = 'Заказ уже оплачен';

                break;
            }else if($gateResponse['errorCode'] == 0 && $gateResponse['orderStatus'] > 2) {

                $this->errors[] = $this->orderStatus[$gateResponse['orderStatus']];

                break;
            }
            else if($gateResponse['errorCode'] != 0) {
                break;
            }


        }
        return $passed;

    }

    private function initOrder(){
        if (!($this->order = Sale\Order::load($this->orderId)))  // Bitrix\Sale\Order
        {
            $this->errors[] = "Заказ с кодом ".$this->orderId." не найден";

        }
        else {
            $this->price = $this->order->getPrice();

            $this->registerOrder();
        }

    }

    private function checkByOrderNumber($orderId = null,$orderNumber = null){

        $data['login']= $this->login;
        $data['password']= $this->password;
       if($orderId) $data['orderId'] = $orderId;
        if($orderNumber)  $data['orderNumber'] = $orderNumber;
        $method = 'getOrderStatusExtended.do';

        $result = $this->getReq($method,$data);

        return $result;
    }

    public function getErrors(){
        return $this->errors;
    }
    public function getErrorsStr(){
       $errors = implode('<br/>',$this->errors);
        return $errors;
    }
    public function getMessages(){

        return $this->message;
    }
    public function getMessagesStr(){
        $message = implode('<br/>',$this->message);
        return $message;
    }

    public function getUrl(){
        return $this->redirectURL;
    }


    private function getReqCURL($method,$data) {

     //   global $APPLICATION;

      //  if (mb_strtoupper(SITE_CHARSET) != 'UTF-8') { $data = $APPLICATION->ConvertCharsetArray($data, 'windows-1251', 'UTF-8'); }
        $http = new Web\HttpClient();
        $http->setCharset("utf-8");
        $http->disableSslVerification();
        $http->post($this->gate_url . $method, $data);

        $response =  $http->getResult();

        if ($this->is_json($response)) {
            $response =  Web\Json::decode($response, true);
        } else {
            $response = array(
                'errorCode' => 999,
                'errorMessage' => 'Server not available',
            );
            //var_dump( $http->getError() );
            //var_dump( $http->getStatus() );
            //var_dump( $http->getHeaders() );
        }

      //  if (mb_strtoupper(SITE_CHARSET) != 'UTF-8') { $APPLICATION->ConvertCharsetArray($response, 'UTF-8', 'windows-1251'); }


        return $response;
    }


    private function getReq($fn,$data){
        $result = [];
        if(!empty($data)){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "{$this->gate_url}{$fn}?userName={$this->login}&password={$this->password}&orderNumber={$data['orderNumber']}&orderId={$data['orderId']}&amount={$data['amount']}&returnUrl={$data['returnURL']}");

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $output = curl_exec($ch);
            $result = json_decode($output, true);
            curl_close($ch);
        }

        return $result;
    }


    private function is_json($string,$return_data = false) {
        $data = json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
    }

}