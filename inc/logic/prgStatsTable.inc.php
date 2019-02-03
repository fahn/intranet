<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2019
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/

include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/db/user.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgTable.inc.php';

/**
 * This class registers the columns of a general result view
 * @author philipp
 *
 */
abstract class APrgStatsTablePattern extends APrgSqlTablePattern {

	const PRG_TABLE_CLM_POSITION 		= "position";
	const PRG_TABLE_CLM_RANK_POINTS 	= "rankPoints";
	const PRG_TABLE_CLM_RANK_TYPE 		= "rankType";
	const PRG_TABLE_CLM_GAMES 			= "games";
	const PRG_TABLE_CLM_GAMES_WON 		= "gamesWon";
	const PRG_TABLE_CLM_GAMES_LOST 		= "gamesLost";
	const PRG_TABLE_CLM_GAMES_RATIO 	= "gamesRatio";
	const PRG_TABLE_CLM_SETS 			= "sets";
	const PRG_TABLE_CLM_SETS_WON 		= "setsWon";
	const PRG_TABLE_CLM_SETS_LOST 		= "setsLost";
	const PRG_TABLE_CLM_SETS_RATIO 		= "setsRatio";
	const PRG_TABLE_CLM_POINTS 			= "points";
	const PRG_TABLE_CLM_POINTS_WON 		= "pointsWon";
	const PRG_TABLE_CLM_POINTS_LOST 	= "pointsLost";
	const PRG_TABLE_CLM_POINTS_RATIO 	= "pointsRatio";

	public function __construct($viewName, BrankDB $brdb) {
		parent::__construct($viewName, $brdb);

		$this->registerColumn(self::PRG_TABLE_CLM_POSITION);
		$this->registerColumn(self::PRG_TABLE_CLM_RANK_POINTS);
		$this->registerColumn(self::PRG_TABLE_CLM_RANK_TYPE);
		$this->registerColumn(self::PRG_TABLE_CLM_GAMES);
		$this->registerColumn(self::PRG_TABLE_CLM_GAMES_WON);
		$this->registerColumn(self::PRG_TABLE_CLM_GAMES_LOST);
		$this->registerColumn(self::PRG_TABLE_CLM_GAMES_RATIO);
		$this->registerColumn(self::PRG_TABLE_CLM_SETS);
		$this->registerColumn(self::PRG_TABLE_CLM_SETS_WON);
		$this->registerColumn(self::PRG_TABLE_CLM_SETS_LOST);
		$this->registerColumn(self::PRG_TABLE_CLM_SETS_RATIO);
		$this->registerColumn(self::PRG_TABLE_CLM_POINTS);
		$this->registerColumn(self::PRG_TABLE_CLM_POINTS_WON);
		$this->registerColumn(self::PRG_TABLE_CLM_POINTS_LOST);
		$this->registerColumn(self::PRG_TABLE_CLM_POINTS_RATIO);
	}
}

/**
 * This class registers additional columns regarding the user result views
 * @author philipp
 *
 */
class PrgPlayerStatsTablePattern extends APrgStatsTablePattern {

	const PRG_TABLE_CLM_USER_ID 	= "userId";
	const PRG_TABLE_CLM_FIRST_NAME 	= "firstName";
	const PRG_TABLE_CLM_LAST_NAME	= "lastName";

	public function __construct($viewName, BrankDB $brdb) {
		parent::__construct($viewName, $brdb);

		$this->registerColumn(self::PRG_TABLE_CLM_USER_ID);
		$this->registerColumn(self::PRG_TABLE_CLM_FIRST_NAME);
		$this->registerColumn(self::PRG_TABLE_CLM_LAST_NAME);
	}
}

/**
 * This class registeres additional columns regarding the team result views
 * @author philipp
 *
 */
class PrgTeamStatsTablePattern extends APrgStatsTablePattern{

	const PRG_TABLE_CLM_TEAM_ID 			= "teamId";
	const PRG_TABLE_CLM_TEAM_NAME 			= "teamName";
	const PRG_TABLE_CLM_PLAYER1_ID 			= "player1Id";
	const PRG_TABLE_CLM_PLAYER1_FIRST_NAME 	= "player1FirstName";
	const PRG_TABLE_CLM_PLAYER1_LAST_NAME	= "player1LastName";
	const PRG_TABLE_CLM_PLAYER2_ID 			= "player2Id";
	const PRG_TABLE_CLM_PLAYER2_FIRST_NAME 	= "player2FirstName";
	const PRG_TABLE_CLM_PLAYER2_LAST_NAME	= "player2LastName";

	public function __construct($viewName, BrankDB $brdb) {
		parent::__construct($viewName, $brdb);

		$this->registerColumn(self::PRG_TABLE_CLM_TEAM_ID);
		$this->registerColumn(self::PRG_TABLE_CLM_TEAM_NAME);
		$this->registerColumn(self::PRG_TABLE_CLM_PLAYER1_ID);
		$this->registerColumn(self::PRG_TABLE_CLM_PLAYER1_FIRST_NAME);
		$this->registerColumn(self::PRG_TABLE_CLM_PLAYER1_LAST_NAME);
		$this->registerColumn(self::PRG_TABLE_CLM_PLAYER2_ID);
		$this->registerColumn(self::PRG_TABLE_CLM_PLAYER2_FIRST_NAME);
		$this->registerColumn(self::PRG_TABLE_CLM_PLAYER2_LAST_NAME);
	}
}
?>
