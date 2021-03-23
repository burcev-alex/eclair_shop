<?php

namespace Sprint\Migration;


class order_make20210323064217 extends Version
{
    protected $description = "";

    protected $moduleVersion = "3.25.1";

    public function up()
    {
        \CModule::IncludeModule("sale");
        $arFields = array(
            "FORMAT_STRING" => "# ₽",
            /*"FULL_NAME" => "Рубль",
            "DEC_POINT" => ".",
            "THOUSANDS_SEP" => "\xA0",
            "DECIMALS" => 2,
            "CURRENCY" => "RUB",
            "LID" => "ru"*/
        );

        $db_result_lang = \CCurrencyLang::GetByID("RUB", "ru");
        if ($db_result_lang)
            \CCurrencyLang::Update("RUB", "ru", $arFields);
        else
            \CCurrencyLang::Add($arFields);

        $names=['Имя','E-Mail','Телефон','Улица','Этаж','Подъезд','Квартира','Номер дома','Желаемая дата и время доставки','Количество приборов','Удобный способ связи'];
        $oprops=[
            'Имя'=>[
                'CODE'=>'FIO',
                'ACTIVE'=>'Y'
            ],
            'E-Mail'=>[
                'CODE'=>'EMAIL',
                'ACTIVE'=>'Y'
            ],
            'Телефон'=>[
                'CODE'=>'PHONE',
                'ACTIVE'=>'Y'
            ],
            'Улица'=>[
                'CODE'=>'STREET',
                'ACTIVE'=>'Y'
            ],
            'Этаж'=>[
                'CODE'=>'FLOOR',
                'ACTIVE'=>'Y'
            ],
            'Подъезд'=>[
                'CODE'=>'podezd',
                'ACTIVE'=>'Y'
            ],
            'Квартира'=>[
                'CODE'=>'FLAT',
                'ACTIVE'=>'Y'
            ],
            'Номер дома'=>[
                'CODE'=>'HOME',
                'ACTIVE'=>'Y'
            ],
            'Желаемая дата и время доставки'=>[
                'CODE'=>'TIME',
                'ACTIVE'=>'Y'
            ],
            'Количество приборов'=>[
                'CODE'=>'QUANTITY_WARE',
                'ACTIVE'=>'Y'
            ],
            'Удобный способ связи'=>[
                'CODE'=>'VIDSVYAZI',
                'NAME'=>'WhatsApp',
                'ACTIVE'=>'Y'
            ]
        ];
        $db_props = \CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(
                'NAME'=>$names
            )
        );
        $props=[];
        while($prop=$db_props->Fetch()){
            $props[$prop['NAME']]=$prop;
        }
        foreach($props as $name=>$ar){
            \CSaleOrderProps::Update($ar['ID'], $oprops[$name]);
        }
    }

    public function down()
    {
        \CModule::IncludeModule("sale");
        $arFields = array(
            "FORMAT_STRING" => "# руб.",
            /*"FULL_NAME" => "Рубль",
            "DEC_POINT" => ".",
            "THOUSANDS_SEP" => "\xA0",
            "DECIMALS" => 2,
            "CURRENCY" => "RUB",
            "LID" => "ru"*/
        );

        $db_result_lang = \CCurrencyLang::GetByID("RUB", "ru");
        if ($db_result_lang)
            \CCurrencyLang::Update("RUB", "ru", $arFields);

        $names=['WhatsApp'];
        $oprops=[
            'WhatsApp'=>[
                'CODE'=>'VIDSVYAZI',
                'NAME'=>'Удобный способ связи'
            ]
        ];
        $db_props = \CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(
                'NAME'=>$names
            )
        );
        $props=[];
        while($prop=$db_props->Fetch()){
            $props[$prop['NAME']]=$prop;
        }
        foreach($props as $name=>$ar){
            \CSaleOrderProps::Update($ar['ID'], $oprops[$name]);
        }
    }
}
