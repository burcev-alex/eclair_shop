<?
header("Content-Type: application/x-javascript");
$hash = "bx_random_hash";
$config = array("appmap" =>
	array("main" => "demo_app",
		"left" => "/demo_app/left.php",
		"right" => "/demo_app/right.php",
		"settings" => "/demo_app/settings.php",
		"hash" => substr($hash, rand(1, strlen($hash)))
	)
);
echo json_encode($config);
?>