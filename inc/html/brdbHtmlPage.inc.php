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
if( ! defined("__PFAD__") ) {
	define("__PFAD__", dirname(__FILE__) .'/../');
}

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
