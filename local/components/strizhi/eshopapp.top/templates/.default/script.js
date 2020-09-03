BX.ready(function () {
    let pageV = 1;
    let inProgress = false;

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200 && !inProgress) {
            inProgress = true;
            $('.page_catalog_'+pageV).show();
            pageV++;
            inProgress = false;
        }


    });

});