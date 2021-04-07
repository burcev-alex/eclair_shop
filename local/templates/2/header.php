<?
$CurrPage = $APPLICATION->GetCurPage(true);
$HomePage = "/index.php";
$mainPage = $CurrPage == $HomePage? true : false;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="2.png">
    <link rel="apple-touch-icon" sizes="76x76" href="2.png">
    <link rel="apple-touch-icon" sizes="152x152"  href="2.png">
    <link rel="apple-touch-startup-image"  href="2.png">
    <?
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/styles.css", true);
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css", true);
    $APPLICATION->SetAdditionalCSS("/bitrix/css/main/bootstrap.css", true);
    $APPLICATION->AddHeadScript($APPLICATION->GetTemplatePath('js/jquery.min.js'));

$APPLICATION->AddHeadScript($APPLICATION->GetTemplatePath('js/general.js'));
$APPLICATION->AddHeadScript($APPLICATION->GetTemplatePath('js/bootstrap.js'));
    ?>

    <?$APPLICATION->ShowHead();?>
    <title><?$APPLICATION->ShowTitle()?></title>
</head>
<body>

<?$APPLICATION->ShowPanel()?>
<div class="cart-block" >
    <?$APPLICATION->IncludeComponent(
        "bitrix:sale.basket.basket.line",
        "",
        Array(
            "HIDE_ON_BASKET_PAGES" => "Y",
            "PATH_TO_AUTHORIZE" => "",
            "PATH_TO_BASKET" => SITE_DIR."personal/cart/",
            "PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
            "PATH_TO_PERSONAL" => SITE_DIR."personal/",
            "PATH_TO_PROFILE" => SITE_DIR."personal/",
            "PATH_TO_REGISTER" => SITE_DIR."login/",
            "POSITION_FIXED" => "Y",
            "POSITION_HORIZONTAL" => "right",
            "POSITION_VERTICAL" => "top",
            "SHOW_AUTHOR" => "N",
            "SHOW_DELAY" => "N",
            "SHOW_EMPTY_VALUES" => "Y",
            "SHOW_IMAGE" => "Y",
            "SHOW_NOTAVAIL" => "N",
            "SHOW_NUM_PRODUCTS" => "Y",
            "SHOW_PERSONAL_LINK" => "N",
            "SHOW_PRICE" => "Y",
            "SHOW_PRODUCTS" => "Y",
            "SHOW_REGISTRATION" => "N",
            "SHOW_SUMMARY" => "Y",
            "SHOW_TOTAL_PRICE" => "Y"
        )
    );?>
</div>
<div id="nav176887217" class="container-fluid header positionfixed ">
    <div class="row">
        <div class="leftside   col-6 col-sm-6 col-md-2 col-lg-2 ">

                <a href="/" style="color:#ffffff;">
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png" class="t228__imglogo t228__imglogomobile"
                         imgfield="img" style="max-width: 140px;width: 140px; height: auto; display: block;" alt="">
                </a>

        </div>
        <div class="col-xs-6  hidden-md   hidden-lg ">
            <div class="toggler" onclick="eclair.toggler()">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="centercontainer  col-lg-7 col-md-7 hidden-xs">

            <?$APPLICATION->IncludeComponent("bitrix:menu", "landing", array(
                "ROOT_MENU_TYPE" => "top",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => Array(),
                "MAX_LEVEL" => "2",
                "CHILD_MENU_TYPE" => "show_add",
                "USE_EXT" => "Y",
                "ALLOW_MULTI_SELECT" => "N"
            ),
                false
            );?>
        </div>
        <div class=" rightside   col-lg-3 col-md-3 hidden-xs">
                <div class="right_descr" style="color:#ffffff;font-size:24px;font-weight:600;">
                    <div class="phone" style="line-height:24px;" >+7 383 311-05-57<br>
                        <span style="font-size: 16px;">
                            <span style="font-weight: 400;" data-redactor-style="font-weight: 400;">
                            <span style="font-size: 14px;" data-redactor-tag="span">г. Новосибирск, Ядринцевская, 21</span>
                            </span>
                        </span>
                    </div>
                </div>
        </div>
    </div>
</div>
<div id="rec176888153" class=" container-fluid heading hidden-xs">
        <!-- cover -->
        <div class="cover col-sm-12" >
            <div class="cover__carrier loaded">
            </div>
            <div class="cover__filter" style="">
            </div>
            <div class="" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;">
                <div class="wrapper" style="height:50vh;">
                    <div class="avatar">
                    </div>
                </div>
            </div>
        </div>
        <div class="author col-sm-12">
            <div class="content">
                <h1 class="title" style="color:#ffffff;" >
                   Всё самое вкусное с доставкой на дом
           </h1>
            </div>
        </div>
</div>

<div class="container mainpart" style="padding-bottom: 65px;">
    <? if(!$mainPage){
        ?>
        <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "main", Array(
            "PATH" => "",	// Путь, для которого будет построена навигационная цепочка (по умолчанию, текущий путь)
            "SITE_ID" => "s1",	// Cайт (устанавливается в случае многосайтовой версии, когда DOCUMENT_ROOT у сайтов разный)
            "START_FROM" => "0",	// Номер пункта, начиная с которого будет построена навигационная цепочка
        ),
            false
        );?>

        <?

        if(!strstr($CurrPage,'catalog')){
            ?>
            <h1><?$APPLICATION->ShowTitle(true)?></h1>
            <?
        }
    }

    ?>

