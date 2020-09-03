<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
$componentFolder = '/local/components/strizhi/catalog.element.addings';
$this->addExternalJs('script.js');
$this->addExternalJs($componentFolder.'/js/jquery.bxslider.js');
$this->addExternalCss($componentFolder.'/css/jquery.bxslider.css');

?>

<div class="slider">
    <?
    foreach($arResult["ADDINGS"] as $adding){
        ?>
        <div><?=$adding["NAME"]?></div>
        <?
    }
    ?>
</div>
<pre>
    <?=print_r($arResult["ADDINGS"])?>
</pre>