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

include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlPage.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/parsedown-1.6.0/Parsedown.php';


class BrdbHtmlParseDownPage extends BrdbHtmlPage {

	protected $markDownFile;

	public function __construct($markDownFile) {
		parent::__construct();
		$this->markDownFile = $markDownFile;
	}

    public function processPage() {
        parent::processPage();
    }


	protected function htmlBody() {
		$mdfile = file_get_contents($this->markDownFile);
		$Parsedown = new Parsedown();

    $this->smarty->assign(array(
      'content' => $Parsedown->text($mdfile),
    ));
		$this->smarty->display('index.tpl');
	}
}
?>
