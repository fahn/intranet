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
#require_once($_SERVER['BASE_DIR'] .'/inc/exception/badtra.exception.php');

#set_exception_handler(array('BadtraException', 'exception_handler'));

#throw new Exception('DOH!!');

require_once '_options.php';
include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlPage.inc.php';

$page = new BrdbHtmlPage();
$page->processPage();

?>
