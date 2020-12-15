<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul class="nav footer">
<?
foreach($arResult as $arItem):

?>
    <li  class="list_item"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
	
<?endforeach?>

</ul>
<?endif?>