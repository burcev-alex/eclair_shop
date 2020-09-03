<?
require($_SERVER["DOCUMENT_ROOT"] . "/eshop_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("BodyClass", "page");
?>
<!--    <div class="about_item_description">-->
<!--        <h1>--><?//= htmlspecialcharsbx(COption::GetOptionString("main", "site_name", "")) ?><!--</h1>-->
<!--    </div>-->
    <div class="about_item_description">
        <div class="ordering_container">

            <div class="container ">

                <div class="row">
                    <div class="col-sm-12">
                        <h3>
                        Доставляем бесконтактно, безопасно и бесперебойно! <br><br>Вы можете оплатить заказ наличными
                        или картой курьеру.
                        </h3>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="title" field="title">Контакты</div>
                        <div class="descr" field="descr">
                            <a href="tel:+73833833346" style="" class="roistatphone">+7 383 383-33-46</a><br>cofferoom@mail.ru<br>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title" field="title2">Адрес</div>
                        <div class="descr" field="descr2">Новосибирск, ул.
                            Ядринцевская, д. 21
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title" field="title3">Время работы
                        </div>
                        <div class="descr" field="descr3">Принимаем заказы
                            ежедневно<br>с 9.00 до 21.00<br>Доставляем до 22.00
                        </div>
                    </div>
                </div>


                <div class="row top-margin20">
                    <div class="col-sm-12">
                        <div class="descr">ИП Рябкова Людмила
                            Геннадьевна<br>ИНН: 540421785956 <br> ОГРНИП: 317547600031828 <br></div>

                    </div>
                </div>


            </div>


        </div>
    </div>
    <script>
        app.setPageTitle({"title": "Контакты"});
    </script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>