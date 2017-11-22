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
include_once '../inc/logic/prgUser.inc.php';


class BrdbHtmlUserInformation extends BrdbHtmlPage {
	private $prgElementUser;
	private $vars;

	public function __construct() {
		parent::__construct();

		$this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgElementUser);
	}

  public function processPage() {
		parent::processPage();
  }


	protected function htmlBody() {
    $id = $this->getGetVariable('id');
		$content = $this->loadContent($id);

    $this->smarty->assign(array(
			'content' => $content,
		));

		$this->smarty->display('index.tpl');
	}


	private function loadContent($id) {
		if(!isset($id) or !is_numeric($id)) {
			return "";
		}

    $user = $this->brdb->selectUserById($id)->fetch_assoc();
		$club = $this->brdb->selectGetClubById($user['clubId'])->fetch_assoc();
		$this->smarty->assign(array(
			'user'  => $user,
			'club'  => $club,
		));

		return $this->smarty->fetch('user.tpl');
	}


	public function getGetVariable($variableName) {
		return Tools::escapeInput($_GET[$variableName]);
	}
}
?>
