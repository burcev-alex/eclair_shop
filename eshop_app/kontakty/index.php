<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>


<div class="map">
    <script type="text/javascript" charset="utf-8" async
            src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A976c0dcbd5359f46c5b8ec10b56dc6eee358ce568ac071129804eab665b65164&amp;width=100%25&amp;height=450&amp;lang=ru_RU&amp;scroll=true"></script>
</div>

<div class="contacts">
    <h1 class="title">Контакты</h1>

    <div class="info__phone flex">
        <div class="info__into">
            <div class="info__number">
                <span>Телефон</span>
                <a href="tel:+73832090705">+7 383 209-07-05</a>
            </div>

            <div class="info__number">
                <span>E-mail</span>
                <a href="mailto:cofferoom@mail.ru">cofferoom@mail.ru</a>
            </div>
        </div>
    </div>

    <div class="info__map flex">
        <div class="info__into">
            <div class="info__address">
                <span>Новосибирск</span>
                <b>ул. Ядринцевская, 21</b>
            </div>
        </div>
    </div>

    <div class="info__work flex">
        <div class="info__into">
            <div class="info__address">
                <span>Работаем ежедневно</span>
                <b>с 9:00 до 21:00</b>
            </div>
        </div>
    </div>

    <p class="contacts__desc">Доставляем бесконтактно, безопасно</p>
    <p class="contacts__desc">и бесперебойно.</p>
    <p class="contacts__desc">Оплата наличными или картой курьеру</p>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>