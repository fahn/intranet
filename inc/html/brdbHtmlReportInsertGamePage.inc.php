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

		$this->variable['NameDate'] 			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_DATE);
		$this->variable['NameTime'] 			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_TIME);
		$this->variable['NamePlayerA1'] 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_A_1);
		$this->variable['NamePlayerB1'] 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_B_1);
		$this->variable['NamePlayerA2'] 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_A_2);
		$this->variable['NamePlayerB2'] 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_B_2);
		$this->variable['NameSetA1']			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_A_1);
		$this->variable['NameSetB1']			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_B_1);
		$this->variable['NameSetA2']			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_A_2);
		$this->variable['NameSetB2']			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_B_2);
		$this->variable['NameSetA3']			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_A_3);
		$this->variable['NameSetB3']			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_B_3);
		#$this->variable['NameWinner']			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_WINNER);

		$this->variable['NameDateValue'] 			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_DATE);
		$this->variable['NameTimeValue'] 			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_TIME);
		$this->variable['NamePlayerA1Value'] 		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_A_1);
		$this->variable['NamePlayerB1Value'] 		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_B_1);
		$this->variable['NamePlayerA2Value']		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_A_2);
		$this->variable['NamePlayerB2Value']		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_B_2);
		$this->variable['NameSetA1Value']			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_A_1);
		$this->variable['NameSetB1Value']			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_B_1);
		$this->variable['NameSetA2Value']			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_A_2);
		$this->variable['NameSetB2Value']			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_B_2);
		$this->variable['NameSetA3Value']			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_A_3);
		$this->variable['NameSetB3Value']			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_B_3);
		#$this->variable['NameWinnerValue']		    = $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_WINNER);

		$this->variable['NameAction'] 			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_ACTION);
		$this->variable['NameActionInsertGame'] 	= PrgPatternElementGame::FORM_GAME_ACTION_INSERT_GAME;
		/* $this->variable['NameWinnerSideA']        = PrgPatternElementGame::FORM_GAME_WINNER_SIDE_A;
		$this->variable['NameWinnerSideB']        = PrgPatternElementGame::FORM_GAME_WINNER_SIDE_B; */


		// Set date and time to a default value if they have been reported incorectly
		if ($this->variable['NameDateValue'] == "" or $this->variable['NameDateValue'] == NULL) {
			$this->variable['NameDateValue'] = date("Y-m-d");
		}

		if ($this->variable['NameTimeValue']== "" or $this->variable['NameTimeValue']== NULL) {
			$this->variable['NameTimeValue']= date("H:i:s");
		}
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
			'to'       => BrdbHtmlPage::PAGE_REPORT_INSERT_GAME,
			'variable' => $this->variable,
			'players'  => $this->getAllPlayerDataList(),
			'reporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
		));

		return $this->smarty->fetch('ranking/insertgame.tpl');
	}

	private function getAllPlayerDataList() {
		$res = $this->brdb->selectAllPlayer();
		$data = array();
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= array(
					'userId'   => $dataSet['userId'],
					'fullName' => $dataSet['fullName'],
				);
			}
		}

		return $data;
	}
}

?>
