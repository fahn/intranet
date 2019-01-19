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

include_once __PFAD__ .'/inc/html/htmlLoginPage.inc.php';
include_once __PFAD__ .'/inc/logic/prgGame.inc.php';


class BrdbHtmlPage extends AHtmlLoginPage {
	public function __construct() {
		parent::__construct();
	}

    public function processPage() {
		// Call all prgs and process them all
		$this->prgPattern->processPRG();
		parent::processPage();
	}

}
