<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss("/local/components/strizhi/catalog.gallery/templates/.default/fancybox/jquery.fancybox.min.css");
Asset::getInstance()->addJs( "/local/components/strizhi/catalog.gallery/templates/.default/fancybox/jquery.fancybox.min.js");
 ?>
<div class="container">

    <?php

    if(isset($arResult['PROPS']['PICTURE'])){
        if(isset($arParams['TITLE'])){
            ?>
            <span class="headingtitle"><?=$arParams['TITLE']?></span>
            <?
        }
        foreach($arResult['PROPS']['PICTURE'] as $picture){
            ?>
            <div class="col-md-6 col-sm-4 col-lg-4 col-xs-12 item">
               <a data-fancybox="gallery" href="<?=$picture['origin']?>">
                   <img src="<?=$picture['src']?>" />
               </a>
            </div>
            <?
        }
    }

    ?>

</div>