<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поддержка пользователей");
?><div>
 <br>
</div>
<p>
	 Если у Вас возникли проблемы или вопросы при создании или оформлении заказа, а также при пользовании приложением, Вы можете написать нам, используя форму ниже. Мы обязательно Вам поможем. Опишите, пожалуйста, проблему в поле "Сообщение".<br>
</p>
<div>
 <br>
</div>
<div>
	 <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"EMAIL_TO" => "chervov@3110505.ru",
		"EVENT_MESSAGE_ID" => array("7"),
		"OK_TEXT" => "Спасибо, ваше обращение принято.",
		"REQUIRED_FIELDS" => array("NAME","EMAIL","MESSAGE"),
		"USE_CAPTCHA" => "Y"
	)
);?>
</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>