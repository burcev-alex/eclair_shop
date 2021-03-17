<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="order">
    <h1 class="order__title">Оформление заказа</h1>

    <div class="orderRangeMain">
        <ul class="orderRangeDisc flex">
            <li>
                <span class="orderRange__active"></span>
                <span class="orderRange__active_line"></span>
            </li>

            <li>
                <span class="orderRange__active"></span>
                <span class="orderRange__active_line"></span>
            </li>

            <li>
                <span class="orderRange__active"></span>
                <span class="orderRange__active_line"></span>
            </li>

            <li>
                <span class="orderRange__not"></span>
            </li>
        </ul>

        <ul class="orderRange orderRange__last flex">
            <li class="orderRange__confirmation active">
                <span class="orderRange__name">Подтверждение</span>
            </li>
        </ul>
    </div>

    <button type="button" step="order1" class="orderForm__dateClient flex">Данные клиента</button>

    <button type="button" step="order2" class="orderForm__dateClient flex">Доставка</button>

    <button type="button" step="order3" class="orderForm__dateClient flex">Оплата</button>

    <form action="?action=order5" class="orderForm" method="post">
        <h4 class="orderForm__title">Товары в заказе</h4>
<?foreach($arResult['BASKET'] as $item){?>
        <div class="orderFormOrder flex">
            <div class="orderFormOrder__left">
                <div class="orderFormOrder__title"><?=$item['NAME']?></div>
                <p class="orderFormOrder__desc"><?if($item['WEIGHT']>0)echo $item['WEIGHT'].' гр.'?> <?=$item['PREVIEW_TEXT']?></p>
               <?/* <p class="orderFormOrder__add">Добавки: Банан, малиновое варенье</p>*/?>
            </div>

            <div class="orderFormOrder__right">
                <div class="orderFormOrder__title"><?=CurrencyFormat($item['PRICE'], "RUB")?></div>
                <div class="orderFormOrder__desc">х<?=$item['QUANTITY']?></div>
            </div>
        </div>

        <hr class="orderForm__hr">
<?}?>
        <div class="orderFormOrderAmount flex">
            <div class="orderFormOrderAmount__title">Сумма заказа: <span><?=CurrencyFormat($arResult['SUM'], "RUB")?></span></div>
        </div>
    </form>
</div>
<div class="orderForm__amound">
    <button type="submit" class="orderForm__btn">Оформить заказ за <?=CurrencyFormat($arResult['SUM'], "RUB")?></button>
</div>
<script>
    $('.orderForm__dateClient').click(function(){
        window.location.href = '?action='+$(this).attr('step');
    });
    $('.orderForm__btn').click(function(){
        window.location.href = '?action=order5';
    });
</script>