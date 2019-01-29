<?php
include($_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php');

use \Batranet\BRDB\BrankDB;

// We now can access Controller using only Controller namespace,
// not Some\Path\To\Controller
$controller = new BrankDB\BrankDB();

// Error, because, again, in current scope there is no such class
#$controller = new Controller();

?>
1
