<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.	*
 *														*
 * Copyright 2017										*
 *														*
 * All Rights Reserved									*
 *														*
 * Copying, distribution, usage in any form is not 		*
 * allowed without  written permit.						*
 *														*
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)	*
 *														*
 ********************************************************/

include_once '../inc/html/brdbHtmlStatsTeamPage.inc.php';

session_start();

$page = new BrdbHtmlStatsTeamDisciplineDoubleMenPage();
$page->processPage();
?>