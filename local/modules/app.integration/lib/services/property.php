<?php

namespace App\Integration\Services;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Section.
 */
class Property
{
    public function __construct()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
    }

    public function save($data)
    {
        $xmlId = $data['ID'];

        $bs = new \CIBlockProperty();

        $arFields = [
            'ACTIVE' => $data['ACTIVE'],
            'XML_ID' => $data['ID'],
            'IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'],
            'NAME' => $data['NAME'],
            'CODE' => $data['CODE'],
            'SORT' => $data['SORT'],
            'PROPERTY_TYPE' => $data['PROPERTY_TYPE'],
            'USER_TYPE' => $data['USER_TYPE'],
            'IS_REQUIRED' => $data['IS_REQUIRED'],
            'MULTIPLE' => $data['MULTIPLE'],
            'VALUES' => $data['VALUES'],
        ];

        if (strlen($data['PICTURE']) > 0) {
            $arFields['PICTURE'] = \CFile::MakeFileArray($data['PICTURE']);
        }

		$ID = 0;
		if(IntVal($data['ID']) > 0){
			$rsSect = \CIBlockProperty::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'CODE' => $data['CODE']]);
			while ($arSect = $rsSect->Fetch()) {
				$ID = $arSect['ID'];
			}
		}

        if ($ID > 0) {
			$res = $bs->Update($ID, $arFields);
        } else {
			$ID = $bs->Add($arFields);
		}
		
		if(IntVal($ID) == 0){
			$xmlId = 0;
		}

        return $xmlId;
    }

    public function delete($data)
    {
        $xmlId = $data['ID'];

        $bs = new \CIBlockProperty();

        $ID = 0;
        $rsSect = \CIBlockProperty::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'CODE' => $data['CODE']]);
        while ($arSect = $rsSect->GetNext()) {
            $ID = $arSect['ID'];
		}

        if ($ID > 0) {
			\CIBlockProperty::Delete($ID);
        }
		
		if(IntVal($ID) == 0){
			$xmlId = 0;
		}

        return $xmlId;
    }
}
