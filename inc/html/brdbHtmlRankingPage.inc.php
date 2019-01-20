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

include_once __PFAD__ .'/inc/html/brdbHtmlPage.inc.php';
include_once __PFAD__ .'/inc/parsedown-1.6.0/Parsedown.php';


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
