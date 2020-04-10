<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

// start session();
session_start();

// load BASE_DIR
#$directory=dirname(dirname(__FILE__));
#include_once($directory .'/inc/config.php');

if (!defined("BASE_DIR")) {
    define("BASE_DIR", "/var/www/html/"); 
}

?>