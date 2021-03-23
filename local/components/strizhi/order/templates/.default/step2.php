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
                <span class="orderRange__disc"></span>
            </li>

            <li>
                <span class="orderRange__not"></span>
            </li>

            <li>
                <span class="orderRange__not"></span>
            </li>
        </ul>

        <ul class="orderRange flex">
            <li class="orderRange__deliver active">
                <span class="orderRange__name">Доставка</span>
            </li>
        </ul>
    </div>

    <button type="button" step="order1" class="orderForm__dateClient flex">Данные клиента</button>

    <form action="?action=order3" method="post" class="orderForm">
        <h4 class="orderForm__title">Доставка</h4>

        <div class="orderForm__desc">В разные районы разная минимальная сумма чека для бесплатной доставки.
            В ином случае стоимость доставки будет составлять 500 рублей.
        </div>
        <?$checked=false;?>
        <?foreach($arResult['PICKUP'] as $key=>$item){?>
            <div class="input-row">
                <input id="radio-input-<?=$key+1?>" type="radio" value="<?=$item['ID']?>" name="FIELDS[DELIVERY]" <?if($arResult['DATA']['DELIVERY']==$item['ID']){$checked=true;echo 'checked';}?>>
                <label class="input-helper input-helper--radio" for="radio-input-<?=$key+1?>"><span class="orderForm__name"><?=$item['NAME']?>
              </span> <span class="orderForm__free"><?=$item['DESCRIPTION']?>
              </span> </label>
            </div>
        <?}?>
        <div class="input-row">
            <input id="radio-input-0" type="radio" value="SELECT" name="FIELDS[DELIVERY]" <?if(!$checked)echo 'checked';?>>
            <label class="input-helper input-helper--radio" for="radio-input-0"><span class="orderForm__name">Доставка курьером</span>
                <span class="orderForm__price"></span> </label>
        </div>

        <hr class="orderForm__hr">

        <div class="orderForm__select" id="deliveryselect">
            <div class="selectBlock__title">Выберите район доставки</div>
            <select class="selectdelivery" name="FIELDS[DELIVERY]">
                <?foreach($arResult['DELIVERIS'] as $key=>$item){?>
                    <option value="<?=$item['ID']?>" price="<?=$item['PRICE']?>" <?if($arResult['DATA']['DELIVERY']==$item['ID'])echo 'selected';?>><?=$item['NAME']?></option>
                <?}?>
            </select>
        </div>

<div id="addresfields">
    <span class="orderForm__many flex">Бесплатно от&nbsp;<span id="deliveryPriceFree">2500</span>&nbsp;₽</span>
    <hr class="orderForm__hr">
        <div class="orderForm__input">
            <label for="street">Улица</label>
            <input placeholder="Улица" type="text" id="street" name="FIELDS[STREET]" value="<?=$arResult['DATA']['STREET']?>">
        </div>

        <div class="orderForm__input">
            <label for="home">Дом</label>
            <input placeholder="Дом" type="text" id="home" name="FIELDS[HOME]" value="<?=$arResult['DATA']['HOME']?>">
        </div>

        <div class="orderForm__input">
            <label for="flat">Квартира</label>
            <input placeholder="Квартира" type="text" id="flat" name="FIELDS[FLAT]" value="<?=$arResult['DATA']['FLAT']?>">
        </div>

        <div class="orderForm__input">
            <label for="entrance">Подъезд</label>
            <input placeholder="Подъезд" type="text" id="entrance" name="FIELDS[ENTRANCE]" value="<?=$arResult['DATA']['ENTRANCE']?>">
        </div>

        <div class="orderForm__input">
            <label for="floor">Этаж</label>
            <input placeholder="Этаж" type="text" id="floor" name="FIELDS[FLOOR]" value="<?=$arResult['DATA']['FLOOR']?>">
        </div>
    <hr class="orderForm__hr">
</div>

        <div class="selectBlock__title">Выберите время получения</div>

        <div class="input-row">
            <input id="radio-input-4" type="radio" value="N" name="FIELDS[TIME]" <?if($arResult['DATA']['TIME']=='N'){echo 'checked';}?>>
            <label class="input-helper input-helper--radio" for="radio-input-4"><span class="orderForm__name">Ближайшее
      </span> </label>
        </div>

        <div class="input-row input-row__modif">
            <input id="radio-input-5" type="radio" value="Y" name="FIELDS[TIME]" <?if($arResult['DATA']['TIME']=='Y'){echo 'checked';}?>>
            <label class="input-helper input-helper--radio" for="radio-input-5"><span class="orderForm__name">Выбрать время</span></label>
        </div>

        <div class="orderForm__select" id="dateselect">
            <select class="" name="FIELDS[TIMESELECT]">
                <?foreach($arResult['DATES'] as $key=>$data){?>
                    <option key="<?=$key?>" <?if($arResult['DATA']['TIMESELECT']==$data){echo 'selected';}?>><?=$data?></option>
                <?}?>
            </select>
        </div>

        <div class="orderForm__select" id="timeselect">
            <select class="select" name="FIELDS[TIMESELECT2]">
                <?if($arResult['DATA']['TIMESELECT']==$arResult['DATES'][0]){
                    echo $arResult['CURTIMES'];
                }else{
                    foreach (range(0, 23) as $number){
                        echo '<option '.($arResult['DATA']['TIMESELECT2']==$number.':00'?'selected':'').'>'.$number.':00</option>';
                    }
                }?>
            </select>
        </div>

        <hr class="orderForm__hr">

        <div class="orderForm__textarea">
            <div class="selectBlock__title">Комментарий к заказу</div>
            <textarea placeholder="Введите коментарий" name="FIELDS[COMMENT]"><?=$arResult['DATA']['COMMENT']?></textarea>
        </div>


        <hr class="orderForm__hr">


        <div class="selectBlock__title">Столовые приборы</div>

        <div class="input-row">
            <input id="radio-input-6" type="radio" value="N" name="FIELDS[CULTERY]" <?if($arResult['DATA']['CULTERY']=='N'){echo 'checked';}?>>
            <label class="input-helper input-helper--radio" for="radio-input-6"><span class="orderForm__name">Без приборов
      </span> </label>
        </div>

        <div class="input-row input-row__modif">
            <input id="radio-input-7" type="radio" value="Y" name="FIELDS[CULTERY]" <?if($arResult['DATA']['CULTERY']=='Y'){echo 'checked';}?>>
            <label class="input-helper input-helper--radio" for="radio-input-7"><span class="orderForm__name">Нужны приборы</span></label>
        </div>

        <div class="orderForm__select" id="culteryselect">
            <select class="select" name="FIELDS[CULTERYSELECT]">
                <option value="2" <?if($arResult['DATA']['CULTERYSELECT']==2){echo 'selected';}?>>На 2 персоны</option>
                <option value="3" <?if($arResult['DATA']['CULTERYSELECT']==3){echo 'selected';}?>>На 3 персоны</option>
                <option value="4" <?if($arResult['DATA']['CULTERYSELECT']==4){echo 'selected';}?>>На 4 персоны</option>
            </select>
        </div>

        <hr class="orderForm__hr">

        <button type="submit" class="orderForm__btn">Продолжить</button>
    </form>
</div>
<script>
    window.deliveryPrices = <?=CUtil::PhpToJSObject($arResult['DELIVERYIFS'])?>;
    function setDateTimeField(){
        if($('#radio-input-5').is(':checked')){
            $('#timeselect,#dateselect').show();
            $('#timeselect,#dateselect').attr('disabled',false);
            if($('#dateselect option:contains("'+$('#dateselect select').val()+'")').attr('key')==0){
                $('#timeselect select').html('<?=$arResult['CURTIMES']?>')
            }else{
                $('#timeselect select').html('<?foreach (range(0, 23) as $number)echo '<option>'.$number.':00</option>'?>')
            }
            $('#timeselect select').multipleSelect('refresh')
        }else{
            $('#timeselect,#dateselect').hide();
            $('#timeselect,#dateselect').attr('disabled','disabled');
        }
    }
    function setCulteryField(){
        if($('#radio-input-7').is(':checked')){
            $('#culteryselect').show();
            $('#culteryselect select').attr('disabled',false);
            $('#culteryselect select').multipleSelect('refresh');
        }else{
            $('#culteryselect').hide();
            $('#culteryselect select').attr('disabled','disabled');
        }
    }

    function setDeliveryField(){
        if($('#radio-input-0').is(':checked')){
            $('#deliveryselect').show();
            $('#street,#home').attr('required','required');
            $('#street,#home,#flat,#entrance,#floor,#deliveryselect select').attr('disabled',false);
            $('.selectdelivery').multipleSelect('refresh')
            $('#addresfields').show();
            setDeliveryPrice();
        }else{
            $('#deliveryselect').hide();
            $('.orderForm__price').html('');
            $('#street,#home').attr('required',false);
            $('#street,#home,#flat,#entrance,#floor,#deliveryselect select').attr('disabled','disabled');
            $('#addresfields').hide();
        }
    }
    function setDeliveryPrice(){
            $('.orderForm__price').html('+ '+$('#deliveryselect option[value="'+$('.selectdelivery').val()+'"]').attr('price')+' ₽');
            $('#deliveryPriceFree').html(window.deliveryPrices[$('.selectdelivery').val()].FREE_PRICE);
    }

    $('#radio-input-5,#radio-input-4').change(function(){
        setDateTimeField();
    });
    $('input[name="FIELDS[DELIVERY]"]').change(function(){
        setDeliveryField();
    });
    $('input#radio-input-7,#radio-input-6').change(function(){
        setCulteryField();
    });
    $(document).ready(function() {
        $('#dateselect select').multipleSelect({
            onClick: function (view) {
                $('#dateselect select').val(view.text);
            },

        });
        $('.selectdelivery').multipleSelect({
            onClick: function (view) {
                $('select.selectdelivery').val(view.value);
                setDeliveryPrice();
            },

        });
        setDeliveryPrice();
        setDateTimeField();
        setDeliveryField();
        setCulteryField();
    });
    $('#dateselect select').change(function(){
        setDateTimeField();
    });
    $('.orderForm__dateClient').click(function(){
        window.location.href = '?action='+$(this).attr('step');
    });
    </script>