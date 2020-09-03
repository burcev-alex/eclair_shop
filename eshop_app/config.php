<?
header("Content-Type: application/x-javascript");
$hash = "12345678";
$config = array(
    "appmap" => array(
        "main"=>"/eshop_app/",
        "left"=>"/eshop_app/left.php",
        "settings" => "/eshop_app/settings.php",
        "hash" => substr($hash, rand(1, strlen($hash)))
    ));
echo json_encode($config);
?>