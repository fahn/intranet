<?php 

if (!defined("BASE_DIR")) {
  define("BASE_DIR", "/var/www/html/");
}


// stage
$stage = getenv('INTRANET_STAGE', true) ?: getenv('INTRANET_STAGE');


$url = "/pages";
header('Location: '. $url);
exit();

?>