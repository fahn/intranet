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

include_once '../inc/db/game.inc.php';
include_once '../inc/html/brdbHtmlPage.inc.php';
include_once '../inc/logic/prgGame.inc.php';
include_once '../inc/logic/tools.inc.php';

class BrdbHtmlReportAllGamePage extends BrdbHtmlPage {

	private $prgPatternElementGame;

	const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';

	public function __construct() {
		parent::__construct();
		$this->prgPatternElementGame= new PrgPatternElementGame($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgPatternElementGame);
	}

	public function processPage() {
		$this->smarty->assign(array(
			'content' => '123',
		));

		// Call all prgs and process them all
		parent::processPage();

	}


	public function htmlBody() {
		$variableNameAdminMatchId		= $this->prgPatternElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_ADMIN_MATCH_ID);
		$variableNameAction 			= $this->prgPatternElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_ACTION);
		$variableNameActionUpdateGame 	= PrgPatternElementGame::FORM_GAME_ACTION_UPDATE_GAME;
		$variableNameActionDeleteGame 	= PrgPatternElementGame::FORM_GAME_ACTION_DELETE_GAME;

		// content
		$content = $this->loadContent();

		$this->smarty->assign(array(
			'content' => $content,
		));
		$this->smarty->display('index.tpl');
	}

	private function loadContent() {
		$this->smarty->assign(array(
			'games'      => $this->getMyGames(),
			'error'      => $this->brdb->getError(),
			'isReporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
		));

		return $this->smarty->fetch('ranking/mygames.tpl');
	}

	private function getMyGames() {
		$tmp = array();
		$res = $this->brdb->selectAllGames();
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				#$loopGame 		= new Game($dataSet);
				#$radioId 		= $variableNameAdminMatchId. "_" . $loopGame->matchId;
				$tmp[] = $dataSet;
			}
		}
		return $tmp;
	}
}

?>
