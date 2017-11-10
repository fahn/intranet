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
