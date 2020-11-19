<?php

namespace App\Integration\Services;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Section.
 */
class Section
{
    public function __construct()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
    }

    public function save($data)
    {
		\CModule::IncludeModule('iblock');
		
        $xmlId = $data['ID'];

        $bs = new \CIBlockSection();

		$parentSectionId = false;
        if (intval($data['IBLOCK_SECTION_ID']) > 0) {
            $rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'XML_ID' => $data['IBLOCK_SECTION_ID']]);
            while ($arSect = $rsSect->Fetch()) {
                $parentSectionId = $arSect['ID'];
            }

            if (! $parentSectionId) {
                $rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'ID' => $data['IBLOCK_SECTION_DATA']['XML_ID']]);
                while ($arSect = $rsSect->Fetch()) {
                    $parentSectionId = $arSect['ID'];
                }
            }

            if (! $parentSectionId) {
                $rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'], 'CODE' => $data['IBLOCK_SECTION_DATA']['CODE']]);
                while ($arSect = $rsSect->Fetch()) {
                    $parentSectionId = $arSect['ID'];
                }
            }
		}

        $arFields = [
            'ACTIVE' => $data['ACTIVE'],
            'XML_ID' => $data['XML_ID'],
            'IBLOCK_SECTION_ID' => $parentSectionId,
            'IBLOCK_ID' => $data['IBLOCK_EXTERNAL_ID'],
            'NAME' => $data['NAME'],
            'CODE' => $data['CODE'],
            'SORT' => $data['SORT'],
            'DESCRIPTION' => $data['DESCRIPTION'],
            'DESCRIPTION_TYPE' => $data['DESCRIPTION_TYPE'],
            "IPROPERTY_TEMPLATES" => $data['IPROPERTY_TEMPLATES'],
            'UF_NOTSHOWMENU' => $data['UF_NOTSHOWMENU'],
        ];

        if (strlen($data['PICTURE']) > 0) {
            $arFields['PICTURE'] = \CFile::MakeFileArray($data['PICTURE']);
		}

		$ID = 0;
		if(IntVal($data['ID']) > 0){
			$rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['XML_ID' => $data['XML_ID']]);
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

        $bs = new \CIBlockSection();

        $ID = 0;
        $rsSect = \CIBlockSection::GetList(['id' => 'asc'], ['XML_ID' => $data['XML_ID']]);
        while ($arSect = $rsSect->GetNext()) {
            $ID = $arSect['ID'];
		}

        if ($ID > 0) {
			\CIBlockSection::Delete($ID);
        }
		
		if(IntVal($ID) == 0){
			$xmlId = 0;
		}

        return $xmlId;
    }
}
