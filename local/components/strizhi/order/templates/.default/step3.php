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
                <span class="orderRange__disc"></span>
            </li>

            <li>
                <span class="orderRange__not"></span>
            </li>
        </ul>

        <ul class="orderRange flex">
            <li class="orderRange__order active">
                <span class="orderRange__name">Способ оплаты</span>
            </li>
        </ul>
    </div>

    <button type="button" step="order1" class="orderForm__dateClient flex">Данные клиента</button>

    <button type="button" step="order2" class="orderForm__dateClient flex">Доставка</button>

    <form action="?action=order4" method="post" class="orderForm">
        <h4 class="orderForm__title">Оплата</h4>
        <?foreach($arResult['PAYS'] as $key=>$item){?>
            <div class="input-row">
                <input id="radio-input-<?=$key?>" type="radio" value="<?=$item['ID']?>" name="FIELDS[PAY]" <?if($key==0)echo 'checked';?> >
                <label class="input-helper input-helper--radio" for="radio-input-<?=$key?>"><span class="orderForm__name"><?=$item['NAME']?></span> </label>
            </div>
        <?}?>
             <hr class="orderForm__hr">

        <button type="submit" class="orderForm__btn">Продолжить</button>
    </form>
</div>
<script>
    $('.orderForm__dateClient').click(function(){
        window.location.href = '?action='+$(this).attr('step');
    });
</script>