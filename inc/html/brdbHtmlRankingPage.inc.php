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

include_once '../inc/html/brdbHtmlPage.inc.php';
include_once '../inc/parsedown-1.6.0/Parsedown.php';


class BrdbHtmlRankingPage extends BrdbHtmlPage {

	public function __construct($markDownFile) {
		parent::__construct();
	}

    public function processPage() {
        parent::processPage();
    }


	protected function htmlBody() {

    $this->smarty->assign(array(
      'content' => '<h1>Bald hier mehr</h1>',
		));
		$this->smarty->display('index.tpl');
	}
}
?>
