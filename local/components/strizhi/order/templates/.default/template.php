<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.mask.js");
?>
<div class="order">
    <h1 class="order__title">Оформление заказа</h1>

    <div class="orderRangeMain">
        <ul class="orderRangeDisc flex">
            <li>
                <span class="orderRange__disc"></span>
            </li>

            <li>
                <span class="orderRange__not"></span>
            </li>

            <li>
                <span class="orderRange__not"></span>
            </li>

            <li>
                <span class="orderRange__not"></span>
            </li>
        </ul>

        <ul class="orderRange flex">
            <li class="orderRange__date active">
                <span class="orderRange__name">Данные клиента</span>
            </li>
        </ul>
    </div>


    <form action="?action=order2" class="orderForm" method="post">
        <h4 class="orderForm__title">Данные клиента</h4>

        <div class="orderForm__input">
            <label for="name">Ваше имя</label>
            <input placeholder="Введите имя" type="text" id="name" name="FIELDS[NAME]" value="<?=$arResult['DATA']['NAME']?>">
        </div>

        <div class="orderForm__input">
            <label for="email">E-mail</label>
            <input placeholder="Введите E-mail" type="text" id="email" name="FIELDS[EMAIL]" value="<?=$arResult['DATA']['EMAIL']?>">
        </div>

        <div class="orderForm__input">
            <label for="phone">Телефон</label>
            <input placeholder="+7 (000) 000-00-00" type="text" id="phone" name="FIELDS[PERSONAL_PHONE]" value="<?=$arResult['DATA']['PERSONAL_PHONE']?>">
        </div>

        <div class="input-row">
            <input id="checkbox-input-1" type="checkbox" value="myValue 1">
            <label class="input-helper input-helper--checkbox" for="checkbox-input-1" name="FIELDS[]" value="Y" <?if($arResult['DATA']['WHATSAPP']=="Y")echo 'checked';?>>Напишите мне в WhatsApp</label>
        </div>

        <hr class="orderForm__hr">

        <button type="submit" class="orderForm__btn">Продолжить</button>
    </form>
</div>

<script>
    $("#phone").mask("+7 (000) 000 00 00");
    </script>

