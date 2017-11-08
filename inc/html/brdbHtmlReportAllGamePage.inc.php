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
	
	public function __construct() {
		parent::__construct();
		$this->prgPatternElementGame= new PrgPatternElementGame($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgPatternElementGame);
	}
	
	const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';
	
	protected function htmlBodyProtectedArea() {
		$variableNameAdminMatchId		= $this->prgPatternElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_ADMIN_MATCH_ID);
		$variableNameAction 			= $this->prgPatternElementGame->getPrefixedName(PrgPatternElementGame::FORM_GAME_ACTION);
		$variableNameActionUpdateGame 	= PrgPatternElementGame::FORM_GAME_ACTION_UPDATE_GAME;
		$variableNameActionDeleteGame 	= PrgPatternElementGame::FORM_GAME_ACTION_DELETE_GAME;
?>
	<div id="formReportAllGames">
		<h3>Overview of All Played Games</h3>
		<p>Select a game for update or delete.</p>
		<hr/>
		<form>
			<table>
				<caption>Table of All Played Games.</caption>
				<thead>
					<tr>
						<th colspan="3">Game info</th>
						<th colspan="2">Team A</th>
						<th colspan="2">Team B</th>
						<th colspan="6">Set Points</th>
						<th colspan="1">Result</th>
					</tr>
					<tr>
						<th>Id</th>
						<th>Date</th>
						<th>Time</th>
						<th>Player 1</th>
						<th>Player 2</th>
						<th>Player 1</th>
						<th>Player 2</th>
						<th>A</th>
						<th>B</th>
						<th>A</th>
						<th>B</th>
						<th>A</th>
						<th>B</th>
						<th>Winner</th>
					</tr>
				</thead>
				<tbody>
<?php 
		$res = $this->brdb->selectAllGames();
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				$loopGame 		= new Game($dataSet);
				$radioId 		= $variableNameAdminMatchId. "_" . $loopGame->matchId;
?>
					<tr>
						<td>
							<input 
								type	= "radio" 
								id		= "<?php echo $radioId; ?>"
								name	= "<?php echo $variableNameAdminMatchId; ?>" 
								value	= "<?php echo $loopGame->matchId;?>"
							/>
							<label class = "radio" for = "<?php echo $radioId; ?>"><?php echo $loopGame->matchId; ?></label>
						</td>
						<td><?php echo $loopGame->getDate(); ?></td>
						<td><?php echo $loopGame->getTime(); ?></td>
						<td><?php echo $loopGame->playerA1; ?></td>
						<td><?php echo $loopGame->playerA2; ?></td>
						<td><?php echo $loopGame->playerB1; ?></td>
						<td><?php echo $loopGame->playerB2; ?></td>
						<td><?php echo $loopGame->setA1; ?></td>
						<td><?php echo $loopGame->setB1; ?></td>
						<td><?php echo $loopGame->setA2; ?></td>
						<td><?php echo $loopGame->setB2; ?></td>
						<td><?php echo $loopGame->setA3; ?></td>
						<td><?php echo $loopGame->setB3; ?></td>
						<td><?php echo $loopGame->winner; ?></td>
					</tr> 
<?php 
			}
		} else {
			echo "<p> Failed to get all Games from data base. Reason: " . $brdb->getError() . "</p>";
		}
?>
				</tbody>
			</table>
			<p>
<?php
		if ($this->prgPatternElementLogin->getLoggedInUser()->isReporter()) {
?>
				<input
					type		= "submit"
					name		= "<?php echo $variableNameAction; ?>"
					value		= "<?php echo $variableNameActionDeleteGame; ?>"
					formaction	= "<?php echo BrdbHtmlPage::PAGE_REPORT_ALL_GAME;?>"
					formmethod	= "post"
				/>
				<input
					type		= "submit"
					name		= "<?php echo $variableNameAction; ?>"
					value		= "<?php echo $variableNameActionUpdateGame; ?>"
					formaction	= "<?php echo BrdbHtmlPage::PAGE_REPORT_INSERT_GAME;?>"
					formmethod	= "post"
				/>
<?php
		}
?>
			</p>
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
}

?>