<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php");

require_once ($_SERVER["DOCUMENT_ROOT"]."/local/lib/IblockElementPropertyTable.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/local/lib/CSms4bBase.php");

define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'].'/log.txt');

require_once 'app_init.php';

if (! function_exists('p')) {
    function p($arr, $admin = false)
    {
        global $USER;
        $message = '<pre style="font-size: 10pt; background-color: #fff; color: #000; margin: 10px; padding: 10px; border: 1px solid red; text-align: left; max-width: 800px; max-height: 600px; overflow: scroll">'.print_r($arr, true).'</pre>';
        if ($admin) {
            if ($USER->IsAdmin()) {
                echo $message;
            }
        } else {
            echo $message;
        }
    }
}

if (! function_exists('p2f')) {
    function p2f($obj, $admOnly = true)
    {
        global $USER;

        if (! is_object($USER)) {
            $USER = new \CUser();
        }

        if ($USER->IsAdmin() || $admOnly === false) {
            if ($admOnly) {
                $userID = 1;
            } else {
                $userID = $USER->GetID();
            }
            if (IntVal($userID) == 0) {
                $userID = 'none';
            }
            $dump = "<pre style='font-size: 11px; font-family: tahoma;'>".print_r($obj, true).'</pre>';
            $files = $_SERVER['DOCUMENT_ROOT'].'/'.$userID.'-dump.html';
            $fp = fopen($files, 'a+');
            fwrite($fp, $dump);
            fclose($fp);
        }
    }
}

if (! function_exists('p2log')) {
    function p2log($obj, $key = '')
    {
        if (empty($key)) {
            $key = 'main';
        }

        $dump = print_r($obj, true)."\r\n";
        $files = $_SERVER['DOCUMENT_ROOT'].'/upload/log/'.$key.'.log';
        $fp = fopen($files, 'a+');
        fwrite($fp, $dump);
        fclose($fp);
    }
}

if (! function_exists('pr')) {
    /**
     * Выводит информацию об объекте в стилизованном, удобочитаемом виде.
     * C выводом отображения строки откуда вызывана.
     * @param $o
     * @param bool $option true: показывать стек вызовов
     * @param bool $stack true: var_dump, false: print_r
     */
    function pr($o, $option = false, $stack = false)
    {
        $bt = debug_backtrace();
        $bt = $bt[0];
        $dRoot = $_SERVER['DOCUMENT_ROOT'];
        $dRoot = str_replace('/', '\\', $dRoot);
        $bt['file'] = str_replace($dRoot, '', $bt['file']);
        $dRoot = str_replace('\\', '/', $dRoot);
        $bt['file'] = str_replace($dRoot, '', $bt['file']);

        if (php_sapi_name() != 'cli') {
            ?>
			<div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
				<div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: <?= $bt['file'] ?>
					[<?= $bt['line'] ?>]
				</div>
				<?php if ($option) { ?>
					<pre style='padding:10px;'><?php var_dump(! $stack ? $o : debug_backtrace()); ?></pre>
				<?php } else { ?>
					<pre style='padding:10px;'><?php print_r(! $stack ? $o : debug_backtrace()); ?></pre>
				<?php } ?>

			</div>
		<?php
        } else {
            fprintf("File: %s \n ______________________ \n Count: %s", $bt['file']);
            print_r($o);
        } ?>
		<?php
    }
}

AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAddHandler");

function OnAfterIBlockElementAddHandler($fields) {

    if($fields["IBLOCK_ID"] == 7){
        global $USER;
        $res = \CIBlockElement::GetList([],["ID"=>$fields["ID"]],false,false,["ID", "IBLOCK_ID",'PROPERTY_PHONE','PROPERTY_FILE','PROPERTY_TYPE']);
        if($ar_res = $res->GetNext())
            $arFields = $ar_res;
            $arFields['FILE'] = '';
            $arFields['PHONE'] = $arFields["PROPERTY_PHONE_VALUE"];
            if($arFields["PROPERTY_FILE_VALUE"]){
                $arFields['FILE'] = '<a href="https://sweet-eclair.ru'.CFile::GetPath($arFields["PROPERTY_FILE_VALUE"]).'">Ссылка на файл</a>';
            }

        $arFields= array_merge($arFields,$fields);
        CEvent::Send("NEW_ORDER_SWEET", "s2", $arFields);
    }

}

function sendByWhatsApp($phone, $msg)
{
    $phone = trim($phone, '+');
    $phone[0] = 7;

    $msg = str_replace('"', '\'', $msg);
    // Отправка текста
    $url = 'https://new39066241.wazzup24.com/api/v1.1/send_message';
    // echo $msg;
   $data = "
    {
    \"transport\": \"whatsapp\",
    \"from\": \"79659994622\",
    \"to\": \"$phone\",
    \"text\": \"$msg\"
    }";


    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => $data,
            'header' => "Content-type: application/json\r\n" .
                "Authorization: 400811b478d64879af044f086e7ba021\r\n"
        )
    );
    $context  = stream_context_create( $options );
    $result = file_get_contents( $url, false, $context );
    $response = json_decode( $result );

  /*  $headers = array(
        "Content-type: application/json",
        "Authorization: 400811b478d64879af044f086e7ba021"

    );
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $html = curl_exec($ch);

    curl_close($ch);
    var_dump($html);*/
}
function sendSMS($phone,$price){
        $LOGIN = 'Abramova';
        $PASSWORD = '1cbit-abramova';

        $messagePers = 'Новый заказ на сайте Eclair Cafe';
        $SMS4B = new \Csms4bBase($LOGIN,$PASSWORD);

        if(strlen($phone) > 0){
              $messageclient = 'Ваш заказ принят. Сумма заказа '.$price.'. Мы свяжемся с вами в ближайшее время';
            $smsResult = $SMS4B->SendSMS($messageclient,$phone,'ECLAIR CAFE');
          
        }

        $smsResult = $SMS4B->SendSMS($messagePers,"79833235321",'ECLAIR CAFE');
        $smsResult = $SMS4B->SendSMS($messagePers,"79831242541",'ECLAIR CAFE');
        $smsResult = $SMS4B->SendSMS($messagePers,"79130190362",'ECLAIR CAFE');
        $smsResult = $SMS4B->SendSMS($messagePers,"79134761496",'ECLAIR CAFE');
        $smsResult = $SMS4B->SendSMS($messagePers,"79529053533",'ECLAIR CAFE');
}


function generate_string($strength = 16) {
    $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    return $random_string;
}

function sendByWA($phone,$price){


        $messagePers = 'Новый заказ на сайте Eclair Cafe';


        if(strlen($phone) > 0){
              $messageclient = 'Ваш заказ принят. Сумма заказа '.$price.'. Мы свяжемся с вами в ближайшее время';
            sendByWhatsApp($phone,$messageclient);

        }
    sendByWhatsApp("79833235321",$messagePers.' '.generate_string(2));
    sendByWhatsApp("79831242541",$messagePers.' '.generate_string(2));
    sendByWhatsApp("79130190362",$messagePers.' '.generate_string(2));
    sendByWhatsApp("79134761496",$messagePers.' '.generate_string(2));
    sendByWhatsApp("79529053533",$messagePers.' '.generate_string(2));
    sendByWhatsApp("79132465068",$messagePers.' '.generate_string(2));
}

function show($arr){
    print '<pre>';
        print_r($arr);
    print '</pre>';
}