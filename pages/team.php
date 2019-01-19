<?php


include_once __PFAD__ .'/inc/html/brdbHtmlTeam.inc.php';

session_start();

$page = new BrdbHtmlTeam();
$page->processPage();
?>
