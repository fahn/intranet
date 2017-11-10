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
include_once '../inc/logic/prgGame.inc.php';
include_once '../inc/logic/tools.inc.php';

class BrdbHtmlReportInsertGamePage extends BrdbHtmlPage {
	private $prgElementGame;
	private $vars;
  const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';

	public function __construct() {
		parent::__construct();
		$this->prgElementGame = new PrgPatternElementGame($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgElementGame);
	}

	public function processPage() {
		// Call all prgs and process them all
		parent::processPage();

	}

	protected function htmlBody() {

		$this->smarty->assign(array(
      'content' => $this->loadContent(),
    ));
		$this->smarty->display('index.tpl');
	}

	private function loadContent() {
		$this->smarty->assign(array(
			'vars'     => $this->vars,
			'players'  => $this->getAllPlayerDataList(),
			'reporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
		));

		return $this->smarty->fetch('ranking/insertgame.tpl');
	}

	private function getAllPlayerDataList() {
		$res = $this->brdb->selectAllPlayer();
		if (!$this->brdb->hasError()) {
			$data = array();
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= $dataSet['fullName'];
			}

			return $data;
		}

		return "";
	}
}

?>
