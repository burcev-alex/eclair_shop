


var Catalog = {

    toCart: function(link){
        var elementID = link.getAttribute("data-elementid");
        var elementPrice = link.getAttribute("data-price");
        $.post(
            '/local/ajax/addToCartAdding.php',
            {
                'id' : elementID,
                'price' : elementPrice,
            },
            function(data){
               if(data.result === 'success'){
                   Catalog.window.show('Товар добавлен в корзину');

                   //Аналитика yandex
                   ym(61642846,'reachGoal','add_to_cart');
                   roistat.event.send('add_tocart',{'id':elementID});

                   var id = $('.bx-basket-fixed').attr('id');

                   var fn = window[id];

                   if (typeof fn === "object") {
                       fn.refreshCart({});
                   }
                   // $('.bx-basket-fixed').empty().html(data.html);
               }
            },
            'json'
        );
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
            var html = Catalog.window.template(message);
            $(html).modal('show');
        }
    }
}