<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="basket flex">
    <span></span>


    <div class="basketContent flex">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/icon9.png" alt="">
        <h4>Заказ оформлен</h4>
        <span>Мы получили ваш заказ и наши менеджеры</span>
        <span>скоро свяжутся с вами для уточнения всех </span>
        <span>деталей</span>
    </div>

    <button type="button" class="basket__btn flex">Вернуться на главную</button>
</div>
<script>
    $('.basket__btn').click(function(){
        window.location.href = '<?=SITE_DIR?>';
    });
</script>