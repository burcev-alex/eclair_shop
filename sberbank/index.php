<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Оплата");

 require_once ('Sber.php');

 $payment = new Sber();
 if(!empty($payment->getErrors())){
     ?>
        <div class="alert alert-danger">
         <?
         print $payment->getErrorsStr();
         ?>
        </div>
     <?
 }
 else {
     $payment->registerOrder();
     if(strlen($payment->getUrl()) > 0){

         LocalRedirect($payment->getUrl(),true);
     }
     else {

         if(!empty($payment->getErrors())){
             ?>
             <div class="alert alert-danger">
                 <?
                 print $payment->getErrorsStr();
                 ?>
             </div>
             <?
         }
         if(!empty($payment->getMessages())){
             ?>
             <div class="alert alert-success">
                 <?
                 print $payment->getMessagesStr();
                 ?>
             </div>
             <?
         }
     }
 }
 ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>