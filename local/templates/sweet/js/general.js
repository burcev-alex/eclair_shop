$(function(){
    eclair.init();
});

var eclair = {
    init: function(){

    },
    toggler: function(){
       $('.centercontainer').toggleClass('hidden-xs');
       $('.centercontainer').toggleClass('active');

       $('.toggler').toggleClass('closing');
    },
    totop: function(){
        $("html, body").animate({scrollTop: 0}, 1000);
    }
};