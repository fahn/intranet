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

	public function __construct() {
		parent::__construct();
		$this->prgElementGame = new PrgPatternElementGame($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgElementGame);
	}

	protected function showProtectedArea() {
		return $this->prgPatternElementLogin->getLoggedInUser()->isReporter();
	}

	const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';

	protected function htmlBodyProtectedArea() {
		$variableNameDate 			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_DATE);
		$variableNameTime 			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_TIME);
		$variableNamePlayerA1 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_A_1);
		$variableNamePlayerB1 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_B_1);
		$variableNamePlayerA2 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_A_2);
		$variableNamePlayerB2 		= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_PLAYER_B_2);
		$variableNameSetA1			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_A_1);
		$variableNameSetB1			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_B_1);
		$variableNameSetA2			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_A_2);
		$variableNameSetB2			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_B_2);
		$variableNameSetA3			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_A_3);
		$variableNameSetB3			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_SET_B_3);
		$variableNameWinner			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_WINNER);

		$variableNameDateValue 			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_DATE);
		$variableNameTimeValue 			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_TIME);
		$variableNamePlayerA1Value 		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_A_1);
		$variableNamePlayerB1Value 		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_B_1);
		$variableNamePlayerA2Value		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_A_2);
		$variableNamePlayerB2Value		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_PLAYER_B_2);
		$variableNameSetA1Value			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_A_1);
		$variableNameSetB1Value			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_B_1);
		$variableNameSetA2Value			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_A_2);
		$variableNameSetB2Value			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_B_2);
		$variableNameSetA3Value			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_A_3);
		$variableNameSetB3Value			= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_SET_B_3);
		$variableNameWinnerValue		= $this->prgElementGame->getSessionVariable(PrgPatternElementGame::FORM_GAME_WINNER);

		$variableNameAction 			= $this->prgElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_ACTION);

		$variableNameActionInsertGame 	= PrgPatternElementGame::FORM_GAME_ACTION_INSERT_GAME;
		$variableNameWinnerSideA        = PrgPatternElementGame::FORM_GAME_WINNER_SIDE_A;
		$variableNameWinnerSideB        = PrgPatternElementGame::FORM_GAME_WINNER_SIDE_B;

		$checkedAttributeIsWinnerSideA 	= ($variableNameWinnerValue == $variableNameWinnerSideA) ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";
		$checkedAttributeIsWinnerSideB  = ($variableNameWinnerValue == $variableNameWinnerSideB) ? self::INPUT_RADIO_ATTRIBUTE_CHECKED : "";

		// Set date and time to a default value if they have been reported incorectly
		if ($variableNameDateValue == "" or $variableNameDateValue == NULL) {
			$variableNameDateValue = date("Y-m-d");
		}

		if ($variableNameTimeValue== "" or $variableNameTimeValue== NULL) {
			$variableNameTimeValue= date("H:i:s");
		}
?>
	<div id="formReportInsertGame">
		<h3>Report a Game for Badminton Ranking</h3>
		<p>Tell the date, players, set points and winner of the played game.</p>
		<hr/>
		<form>
			<label for = "<?php echo $variableNameDate;?>">Date and Time:</label>
		    <div class = "radioGroup">
		    <div class = "radioRow">
		    <div class = "radioCell">
			<input
		    	type		= "date"
		    	id			= "<?php echo $variableNameDate;?>"
		    	name		= "<?php echo $variableNameDate;?>"
		    	placeholder	= "2017-05-01"
		    	value		= "<?php echo $variableNameDateValue;?>"
		    />
		    </div>
		    <div class = "radioCell">
		    <input
		    	type		= "time"
		    	id			= "<?php echo $variableNameDate;?>"
		    	name		= "<?php echo $variableNameTime;?>"
		    	placeholder	= "20:00:00"
		    	value		= "<?php echo $variableNameTimeValue;?>"
		    />
		    </div>
		    </div>
		    </div>
		    <div class = "radioGroup">
		    <div class = "radioRow">
		    <div class = "radioCell">
				<label for = "<?php echo $variableNamePlayerA1;?>">Team A Player 1:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNamePlayerA1;?>"
			    	name		= "<?php echo $variableNamePlayerA1;?>"
			    	placeholder	= "Philipp Fischer"
			    	value		= "<?php echo $variableNamePlayerA1Value;?>"
			    	list		= "allPlayerList"
			    />
			    <?php $this->getAllPlayerDataList(); ?>
				<label for = "<?php echo $variableNamePlayerA2;?>">Team A Player 2:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNamePlayerA2;?>"
			    	name		= "<?php echo $variableNamePlayerA2;?>"
			    	placeholder	= "Carsten Borchert"
			    	value		= "<?php echo $variableNamePlayerA2Value;?>"
			    	list		= "allPlayerList"
			    />
			    <?php $this->getAllPlayerDataList(); ?>
				<label for = "<?php echo $variableNameSetA1;?>">Team A Set 1 Points:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNameSetA1;?>"
			    	name		= "<?php echo $variableNameSetA1;?>"
			    	placeholder	= "21"
			    	value		= "<?php echo $variableNameSetA1Value;?>"
			    />
				<label for = "<?php echo $variableNameSetA2;?>">Team A Set 2 Points:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNameSetA2;?>"
			    	name		= "<?php echo $variableNameSetA2;?>"
			    	placeholder	= "21"
			    	value		= "<?php echo $variableNameSetA2Value;?>"
			    />
				<label for = "<?php echo $variableNameSetA3;?>">Team A Set 3 Points:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNameSetA3;?>"
			    	name		= "<?php echo $variableNameSetA3;?>"
			    	placeholder	= "0"
			    	value		= "<?php echo $variableNameSetA3Value;?>"
			    />
		    </div>
		    <div class = "radioCell">
		    <label for = "<?php echo $variableNamePlayerB1;?>">Team B Player 1:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNamePlayerB1;?>"
			    	name		= "<?php echo $variableNamePlayerB1;?>"
			    	placeholder	= "Jan Sippli"
			    	value		= "<?php echo $variableNamePlayerB1Value;?>"
			    	list		= "allPlayerList"
			    />
			    <?php $this->getAllPlayerDataList(); ?>
				<label for = "<?php echo $variableNamePlayerB2;?>">Team B Player 2:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNamePlayerB2;?>"
			    	name		= "<?php echo $variableNamePlayerB2;?>"
			    	placeholder	= "Isabel Adam"
			    	value		= "<?php echo $variableNamePlayerB2Value;?>"
			    	list		= "allPlayerList"
			    />
			    <?php $this->getAllPlayerDataList(); ?>
				<label for = "<?php echo $variableNameSetB1;?>">Team B Set 1 Points:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNameSetB1;?>"
			    	name		= "<?php echo $variableNameSetB1;?>"
			    	placeholder	= "16"
			    	value		= "<?php echo $variableNameSetB1Value;?>"
			    />
				<label for = "<?php echo $variableNameSetB2;?>">Team B Set 2 Points:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNameSetB2;?>"
			    	name		= "<?php echo $variableNameSetB2;?>"
			    	placeholder	= "18"
			    	value		= "<?php echo $variableNameSetB2Value;?>"
			    />
				<label for = "<?php echo $variableNameSetB3;?>">Team B Set 3 Points:</label>
			    <input
			    	type		= "text"
			    	id			= "<?php echo $variableNameSetB3;?>"
			    	name		= "<?php echo $variableNameSetB3;?>"
			    	placeholder	= "0"
			    	value		= "<?php echo $variableNameSetB3Value;?>"
			    />
		    </div>
		    </div>
		    </div>
		    <label>Winner:</label>
		    <div class = "radioGroup">
		    <div class = "radioRow">
		    <div class = "radioCell">
		    <input
		    	type		= "radio"
		    	id			= "<?php echo $variableNameWinnerSideA;?>"
		    	name		= "<?php echo $variableNameWinner;?>"
		    	value		= "<?php echo $variableNameWinnerSideA;?>"
				<?php echo $checkedAttributeIsWinnerSideA . rn;  ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameWinnerSideA;?>">Winner A</label>
		    </div>
		    <div class = "radioCell">
		    <input
		    	type		= "radio"
		    	id			= "<?php echo $variableNameWinnerSideB;?>"
		    	name		= "<?php echo $variableNameWinner;?>"
		    	value		= "<?php echo $variableNameWinnerSideB;?>"
		    	<?php echo $checkedAttributeIsWinnerSideB. rn;  ?>
		    />
		    <label class = "radio" for = "<?php echo $variableNameWinnerSideB;?>">Winner B</label>
		    </div>
		    </div>
		    </div>
		    <input
				type		= "submit"
				name		= "<?php echo $variableNameAction;?>"
				value		= "<?php echo $variableNameActionInsertGame;?>"
				formaction	= "<?php echo BrdbHtmlPage::PAGE_REPORT_INSERT_GAME;?>"
				formmethod	= "post"
			/>
		</form>
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

	private function getAllPlayerDataList() {
		$res = $this->brdb->selectAllPlayer();
		if (!$this->brdb->hasError()) {
?>
		<datalist id="allPlayerList">
<?php
			while ($dataSet = $res->fetch_assoc()) {
				$fullName 		= $dataSet['fullName'];
?>
			<option value="<?php echo $fullName; ?>" />
<?php

			}
?>
		</datalist>
<?php
		}
	}
}

?>
