<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2019
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

require_once '_options.php';
include_once $_SERVER['BASE_DIR'] .'inc/html/brdbHtmlParseDownPage.inc.php';

$page = new BrdbHtmlParseDownPage($_SERVER['BASE_DIR'] .'/doc/Impressum.md');
$page->processPage();

?>
