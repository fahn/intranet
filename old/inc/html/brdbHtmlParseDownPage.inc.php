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
	
	protected function htmlBodyProtectedArea() {
	}

	protected function htmlBodyUnProtectedArea() {
		$mdfile = file_get_contents($this->markDownFile);
?>
	<div class = "justText">
<?php 
		$Parsedown = new Parsedown();
		echo $Parsedown->text($mdfile);
?>
	</div>
<?php 
	}
	
	protected function htmlBodyLogin() {
?>
	<div class = "goToLogin">
		<p>You are not logged in! Please log in <a href="<?php echo BrdbHtmlPage::PAGE_INDEX;?>">here</a>!</p>
	</div>
<?php 
	}
}
?>