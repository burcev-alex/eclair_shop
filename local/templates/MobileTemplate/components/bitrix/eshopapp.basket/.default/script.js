// BX.ready(function(){
//
//
//     $('.quantity_input').change(function() {
//         calcBasket();
//     });
// });

function calcBasket () {
    var data_form = {}, form = BX('basket_form');
    for(var i = 0; i< form.elements.length; i++)
    {
        if (form[i].name != 'BasketOrder')
            data_form[form[i].name] = form[i].value;
    }
    ajaxInCart('/eshop_app/personal/cart/', data_form);
}