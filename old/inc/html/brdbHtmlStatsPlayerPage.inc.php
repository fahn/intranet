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
include_once '../inc/html/brdbHtmlStatsTools.inc.php';
include_once '../inc/logic/prgTable.inc.php';
include_once '../inc/logic/prgStatsTable.inc.php';
include_once '../inc/logic/tools.inc.php';

/**
 * A General Page for displaying Player / Single Game Results
 * @author philipp
 *
 */
abstract class ABrdbHtmlStatsPlayerPage extends BrdbHtmlPage {
	
	private $prgPatternElementStatsTable;
	private $tableTitle;
	
	public function __construct($tableTitle, $tableName) {
		parent::__construct();
		$this->prgPatternElementStatsTable= new PrgPlayerStatsTablePattern($tableName, $this->brdb);
		$this->prgPattern->registerPrg($this->prgPatternElementStatsTable);
		$this->tableTitle = $tableTitle;
	}
	
	/**
	 * Implement this method to give some explanation of
	 * what the current table is all about.
	 */
	abstract protected function explainTable();
	
	protected function htmlBodyProtectedArea() {
		?>
	<div id="tableRanking">
		<h3><?php echo $this->tableTitle; ?></h3>
		<p><?php echo $this->explainTable(); ?></p>
		<hr/>
		<table>
			<caption>Ranking Table</caption>
			<thead>
				<tr>
					<th colspan = "2">Rank</th>
					<th colspan = "2">Player</th>
					<th colspan = "4">Games</th>
					<th colspan = "4">Sets</th>
					<th colspan = "4">Points</th>
				</tr>
				<tr>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Position"	, APrgStatsTablePattern::PRG_TABLE_CLM_POSITION); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Points"	, APrgStatsTablePattern::PRG_TABLE_CLM_RANK_POINTS); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "First Name", PrgPlayerStatsTablePattern::PRG_TABLE_CLM_FIRST_NAME); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Last Name"	, PrgPlayerStatsTablePattern::PRG_TABLE_CLM_LAST_NAME); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Played"	, APrgStatsTablePattern::PRG_TABLE_CLM_GAMES); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Won"		, APrgStatsTablePattern::PRG_TABLE_CLM_GAMES_WON); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Lost"		, APrgStatsTablePattern::PRG_TABLE_CLM_GAMES_LOST); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Ratio"		, APrgStatsTablePattern::PRG_TABLE_CLM_GAMES_RATIO); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Played"	, APrgStatsTablePattern::PRG_TABLE_CLM_SETS); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Won"		, APrgStatsTablePattern::PRG_TABLE_CLM_SETS_WON); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Lost"		, APrgStatsTablePattern::PRG_TABLE_CLM_SETS_LOST); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Ratio"		, APrgStatsTablePattern::PRG_TABLE_CLM_SETS_RATIO); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Played"	, APrgStatsTablePattern::PRG_TABLE_CLM_POINTS); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Won"		, APrgStatsTablePattern::PRG_TABLE_CLM_POINTS_WON); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Lost"		, APrgStatsTablePattern::PRG_TABLE_CLM_POINTS_LOST); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Ratio"		, APrgStatsTablePattern::PRG_TABLE_CLM_POINTS_RATIO); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
		while ($dataSet = $this->prgPatternElementStatsTable->fetchResultViewRow()) {
?>
				<tr>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_POSITION]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_RANK_POINTS]; ?></td>
					<td><?php echo $dataSet[PrgPlayerStatsTablePattern::PRG_TABLE_CLM_FIRST_NAME]; ?></td>
					<td><?php echo $dataSet[PrgPlayerStatsTablePattern::PRG_TABLE_CLM_LAST_NAME]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_GAMES]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_GAMES_WON]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_GAMES_LOST]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_GAMES_RATIO]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_SETS]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_SETS_WON]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_SETS_LOST]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_SETS_RATIO]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_POINTS]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_POINTS_WON]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_POINTS_LOST]; ?></td>
					<td><?php echo $dataSet[APrgStatsTablePattern::PRG_TABLE_CLM_POINTS_RATIO]; ?></td>
				</tr> 
<?php 
		}
?>
			</tbody>
		</table>
		<p>Click a column to sort it. Click again to change from ascending to descending order.</p>
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

/**
 * Class to display the results of Single Player Alltime Scores
 * @author philipp
 *
 */
class BrdbHtmlStatsPlayerAlltimePage extends ABrdbHtmlStatsPlayerPage {
	public function __construct() {
		parent::__construct("Player Ranking Alltime", "UserStatsPlayerAlltimePos");
	}
	
	protected function explainTable() {
		return "This is the Alltime Player Ranking. All games ever played account into this table. No matter if it is a Double Men, a Single Women or playing with three persons or a Single Women again Single Men";
	}
}

/**
 * Class to display the results of Single Player Overall Scores
 * from games men vs men, girl vs. girl, men vs girl.
 * @author philipp
 *
 */
 class BrdbHtmlStatsPlayerOverallPage extends ABrdbHtmlStatsPlayerPage {
	public function __construct() {
		parent::__construct("Player Ranking Overall", "UserStatsPlayerOverallPos");
	}
	
	protected function explainTable() {
		return "This is the Overall Player Ranking. All Single games played account into this table. Single Men or Women or Men against Women games.";
	}
}

/**
 * Class to display the results of Single Player Discipline games - Single Men only
 * @author philipp
 *
 */
class BrdbHtmlStatsPlayerDisciplineSingleMenPage extends ABrdbHtmlStatsPlayerPage {
	public function __construct() {
		parent::__construct("Player Ranking Discipline - Single Men", "UserStatsPlayerDisciplineSingleMenPos");
	}
	
	protected function explainTable() {
		return "This is the Discipline Player Ranking for Single Men games. Only Single Men games acount into this table.";
	}
}

/**
 * Class to display the results of Single Player Discipline games - Single Women only
 * @author philipp
 *
 */
 class BrdbHtmlStatsPlayerDisciplineSingleWomenPage extends ABrdbHtmlStatsPlayerPage {
	public function __construct() {
		parent::__construct("Player Ranking Discipline - Single Women", "UserStatsPlayerDisciplineSingleWomenPos");
	}
	
	protected function explainTable() {
		return "This is the Discipline Player Ranking for Single Women games. Only Single Women games acount into this table.";
	}
}
?>