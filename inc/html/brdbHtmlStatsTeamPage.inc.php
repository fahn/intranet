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
 * A General Page for displaying Team Double game results
 * @author philipp
 *
 */
abstract class ABrdbHtmlStatsTeamPage extends BrdbHtmlPage {

	private $prgPatternElementStatsTable;
	private $tableTitle;

	public function __construct($tableTitle, $tableName) {
		parent::__construct();
		$this->prgPatternElementStatsTable= new PrgTeamStatsTablePattern($tableName, $this->brdb);
		$this->prgPattern->registerPrg($this->prgPatternElementStatsTable);
		$this->tableTitle = $tableTitle;
	}

	/**
	 * Implement this method to give some explanation of
	 * what the current table is all about.
	 */
	abstract protected function explainTable();


	protected function htmlBody() {
		$dataSet = array();
	  while ($data = $this->prgPatternElementStatsTable->fetchResultViewRow()) {
	    $dataSet[] = $data;
	  }

		$this->smarty->assign(array(
			'tableTitle' => $this->tableTitle,
			'players'    => $dataSet,
			'explain'    => $this->explainTable(),
		));

    $this->content = $this->smarty->fetch("ranking/StatsTeam.tpl");
    $this->smarty->assign('content', $this->content);

		$this->smarty->display('index.tpl');
	}

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
					<th colspan = "1">Team</th>
					<th colspan = "2">Player 1</th>
					<th colspan = "2">Player 2</th>
					<th colspan = "4">Games</th>
					<th colspan = "4">Sets</th>
					<th colspan = "4">Points</th>
				</tr>
				<tr>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Position"	, APrgStatsTablePattern::PRG_TABLE_CLM_POSITION); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Points"	, APrgStatsTablePattern::PRG_TABLE_CLM_RANK_POINTS); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Name"		, PrgTeamStatsTablePattern::PRG_TABLE_CLM_TEAM_NAME); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "First Name", PrgTeamStatsTablePattern::PRG_TABLE_CLM_PLAYER1_FIRST_NAME); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Last Name"	, PrgTeamStatsTablePattern::PRG_TABLE_CLM_PLAYER1_LAST_NAME); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "First Name", PrgTeamStatsTablePattern::PRG_TABLE_CLM_PLAYER2_FIRST_NAME); ?></th>
					<th><?php BrdbHtmlStatsTool::htmlGetSortLinkFor($this->prgPatternElementStatsTable, "Last Name"	, PrgTeamStatsTablePattern::PRG_TABLE_CLM_PLAYER2_LAST_NAME); ?></th>
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
					<td><?php echo $dataSet[PrgTeamStatsTablePattern::PRG_TABLE_CLM_TEAM_NAME]; ?></td>
					<td><?php echo $dataSet[PrgTeamStatsTablePattern::PRG_TABLE_CLM_PLAYER1_FIRST_NAME]; ?></td>
					<td><?php echo $dataSet[PrgTeamStatsTablePattern::PRG_TABLE_CLM_PLAYER1_LAST_NAME]; ?></td>
					<td><?php echo $dataSet[PrgTeamStatsTablePattern::PRG_TABLE_CLM_PLAYER2_FIRST_NAME]; ?></td>
					<td><?php echo $dataSet[PrgteamStatsTablePattern::PRG_TABLE_CLM_PLAYER2_LAST_NAME]; ?></td>
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
 * Class to display the results of Single Player Overall Scores
 * from games men vs men, girl vs. girl, men vs girl.
 * @author philipp
 *
 */
 class BrdbHtmlStatsTeamOverallPage extends ABrdbHtmlStatsTeamPage {
	public function __construct() {
		parent::__construct("Team Ranking Overall", "UserStatsTeamOverallPos");
	}

	protected function explainTable() {
		return "This is the Overall Team Ranking. All Double games played account into this table. Double Men against Men, Women, Mixed.";
	}
}

/**
 * Class to display the results of Double Team Discipline games - Double Men only
 * @author philipp
 *
 */
class BrdbHtmlStatsTeamDisciplineDoubleMenPage extends ABrdbHtmlStatsTeamPage {
	public function __construct() {
		parent::__construct("Team Ranking Discipline - Double Men", "UserStatsTeamDisciplineDoubleMenPos");
	}

	protected function explainTable() {
		return "This is the Discipline Team Ranking for Double Men games. Only Double Men games acount into this table.";
	}
}

/**
 * Class to display the results of Double Team Discipline games - Double Women only
 * @author philipp
 *
 */
class BrdbHtmlStatsTeamDisciplineDoubleWomenPage extends ABrdbHtmlStatsTeamPage {
	public function __construct() {
		parent::__construct("Team Ranking Discipline - Double Women", "UserStatsTeamDisciplineDoubleWomenPos");
	}

	protected function explainTable() {
		return "This is the Discipline Team Ranking for Double Women games. Only Double Women games acount into this table.";
	}
}

/**
 * Class to display the results of Double Team Discipline games - Double Mixed only
 * @author philipp
 *
 */
class BrdbHtmlStatsTeamDisciplineDoubleMixedPage extends ABrdbHtmlStatsTeamPage {
	public function __construct() {
		parent::__construct("Team Ranking Discipline - Double Mixed", "UserStatsTeamDisciplineDoubleMixedPos");
	}

	protected function explainTable() {
		return "This is the Discipline Team Ranking for Double Mixed games. Only Double Mixed games acount into this table.";
	}
}

?>
