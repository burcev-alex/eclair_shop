$(function(){

slider.init();
    $('#exampleModal').on('hidden.bs.modal', function (e) {
        $(e.target).find('span').empty();
    });
    $('.product-item-detail-buy-button').on('click',function(e){
        slider.addtocart(this);
        e.preventDefault();
    });
});

var slider = {
    priceParentTextObject: {},
    ajaxPath: '/local/ajax/refreshCart.php',
 //   obname: {},
    init: function(){

    },
    sku : {
        /*
        * Удалить добавку
         */
        remove: function(span){
            var parDIV = $(span).parent();
            $(parDIV).remove();
            var parentSum =$('.product-item-detail-buy-button').attr('data-price-selected');
            slider.sku.checkPRices(parentSum);
        },
        checkPRices: function(price){

            if(typeof price !== 'undefined'){
                var sum = 0;
                var els = $('.addingdtodish').find('.addingAdded');


                if(els.length > 0){
                    $(els).each(function(e,v){
                        sum += parseFloat($(v).attr('data-price'))*parseFloat($(v).attr('data-qnt'));
                    });
                }
                if(sum > 0){
                    price = parseFloat(price)+parseFloat(sum);
                }


            }

            $('.product-item-detail-price-current').html(price+ ' руб.');
        }
    },
    addtobasket: function(button){
        var id = $(button).attr("data-id");
        var price = parseFloat($(button).attr("data-price"));
        var currency = $(button).attr("data-currency");
        var lid = $(button).attr("data-lid");
        var name = $(button).attr("data-name");
        var found = false;
        var qnt = 0;
        var priceParent = slider.priceParent;
        var idParent = $('.product-item-detail-buy-button').attr('data-productid');
        $(button).next("span").remove();
        if($('#adding-'+id).length > 0){
            var Block = $('#adding-'+id);
            qnt = parseFloat($(Block).attr('data-qnt'))+1 ;
            found = true;
        }
        else {
            var Block = $('.addingAdded-clone').clone();
            $(Block).removeClass('addingAdded-clone');
            $(Block).addClass('addingAdded');
            $(Block).attr('id','adding-'+id);
            qnt = 1;
        }
        $(Block).attr('data-id',id);
        $(Block).attr('data-price',price*qnt);
        $(Block).attr('data-qnt',qnt);
        $(Block).attr('data-currency',currency);
        $(Block).attr('data-lid',lid);
        $(Block).attr('data-name',name);
        $(Block).find('.addingTitle').html(name);
        $(Block).find('.addingPrice').html(price*qnt+' руб. ('+qnt+' порц.)');
        var priceTotal = priceParent+ price*qnt;
        var priceHtml = priceTotal+' руб.';
       // $('.product-item-detail-price-current').html(priceHtml);
        if(!found){
            $('.addingdtodish').append(Block);
        }
        $(button).after("<span>Добавлено</span>");
        var parentSum =$('.product-item-detail-buy-button').attr('data-price-selected');
        slider.sku.checkPRices(parentSum);


    },
    addtocart: function(button){

        var productid = $(button).attr('data-productid');
        var productPrice = $(button).attr('data-price-selected');
        var els = $('.addingdtodish').find('.addingAdded');
        var addings = [];
        if(els.length > 0){
            $(els).each(function(e,v){
                addings[e] = {
                    price: $(v).attr('data-price'),
                    id: $(v).attr('data-id'),
                    qnt: $(v).attr('data-qnt'),
                    currency: $(v).attr('data-currency'),
                    name: $(v).attr('data-name'),
                    lid: $(v).attr('data-lid'),
                };
            });
        }

        $.post('/local/ajax/addToCartAdding.php',{
            id: productid,
            price: productPrice,
            addings: addings
        }, function(result){
           if(result.result === 'success'){

               slider.window.show('Товар добавлен в корзину');
               //Аналитика yandex
               ym(61642846,'reachGoal','add_to_cart');
               roistat.event.send('add_tocart');

                var id = $('.bx-basket-fixed').attr('id');

                var fn = window[id];

               if (typeof fn === "object") {
                   fn.refreshCart({});
               }
              // $('.bx-basket-fixed').html(result.html);



           }
        } ,"json");

    },
    refreshCart: function(){
        BX.ajax({
            url: this.ajaxPath,
            method: 'POST',
            dataType: 'html',
            data: {},
            onsuccess: this.refreshCartHtml
        });
    },
    refreshCartHtml: function(data){

        $('.bx-basket-fixed').html(data);
    },

    window: {
        template: function(message){
            var html = "<div class=\"modal\" tabindex=\"-1\" role=\"dialog\">\n" +
                "  <div class=\"modal-dialog\">\n" +
                "    <div class=\"modal-content\">\n" +
                "      <div class=\"modal-header\">\n" +
                "        <h5 class=\"modal-title\">Добавление в корзину</h5>\n" +
                "        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\n" +
                "          <span aria-hidden=\"true\">&times;</span>\n" +
                "        </button>\n" +
                "      </div>\n" +
                "      <div class=\"modal-body\">\n" +
                "        <p>"+message+"</p>\n" +
                "      </div>\n" +
                "      <div class=\"modal-footer\">\n" +
                "        <a href='#' rel='nofollow' class=\"btn btn-secondary\" data-dismiss=\"modal\">Продолжить покупки</a>\n" +
                "        <a href='/personal/cart/' class=\"btn btn-secondary\"  rel='nofollow'  >Оформить заказ</a>\n" +
                "      </div>\n" +
                "    </div>\n" +
                "  </div>\n" +
                "</div>";
            return html;
        },
        show: function(message){
            var html = slider.window.template(message);
            $(html).modal('show');
        }
    }
}