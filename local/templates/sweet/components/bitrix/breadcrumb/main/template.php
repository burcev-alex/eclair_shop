<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';

//we can't use $APPLICATION->SetAdditionalCSS() here because we are inside the buffered function GetNavChain()
$css = $APPLICATION->GetCSSArray();
if(!is_array($css) || !in_array("/bitrix/css/main/font-awesome.css", $css))
{
	$strReturn .= '<link href="'.CUtil::GetAdditionalFileURL("/bitrix/css/main/font-awesome.css").'" type="text/css" rel="stylesheet" />'."\n";
}

$strReturn .= '<nav aria-label="breadcrumb">';
$strReturn .= '<ol class="breadcrumb">';

$itemSize = count($arResult);
for($index = 1; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	$arrow = ($index > 1? '<i class="fa fa-angle-right"></i>' : '');

	if($arResult[$index]["LINK"] <> "" )
	{
		$strReturn .= '
			 <li class="breadcrumb-item">
			  
				<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item">
					<span itemprop="name">'.$title.'</span>
				</a> 
			</li>';
	}
	else
	{
		$strReturn .= '
		  <li class="breadcrumb-item active" aria-current="page">
			 
				<span>'.$title.'</span>
			</li>';
	}
}

$strReturn .= ' </ol>';
$strReturn .= '</nav>';

return $strReturn;
