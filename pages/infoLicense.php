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
include_once __PFAD__ .'inc/html/brdbHtmlParseDownPage.inc.php';

$page = new BrdbHtmlParseDownPage(__PFAD__ .'/LICENSE.md');
$page->processPage();

?>
