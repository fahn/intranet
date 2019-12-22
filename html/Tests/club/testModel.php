<?php


require_once(BASE_DIR .'/inc/model/club.inc.php');

$data = array('clubName' => 'FC Schwalbe Hannover', 'clubNr' => '110');
$newClub = new Club($data);
echo $newClub;

?>
