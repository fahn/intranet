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

/**
 * This class implements a user object to simplyfy access to the data base
 * @author philipp
 *
 */
class Game {

	// Constants for the User table in the database
	const GAME_CLM_MATCH_ID = "matchId";
	const GAME_CLM_DATETIME = "datetime";

	const GAME_CLM_PLAYER_A1 = "playerA1";
	const GAME_CLM_PLAYER_B1 = "playerB1";
	const GAME_CLM_PLAYER_A2 = "playerA2";
	const GAME_CLM_PLAYER_B2 = "playerB2";

	const GAME_CLM_SET_A1 = "setA1";
	const GAME_CLM_SET_B1 = "setB1";
	const GAME_CLM_SET_A2 = "setA2";
	const GAME_CLM_SET_B2 = "setB2";
	const GAME_CLM_SET_A3 = "setA3";
	const GAME_CLM_SET_B3 = "setB3";

	const GAME_CLM_WINNER = "side";

	public $matchId;
	public $dateTime;
	public $playerA1;
	public $playerB1;
	public $playerA2;
	public $playerB2;

	public $setA1;
	public $setB1;
	public $setA2;
	public $setB2;
	public $setA3;
	public $setB3;

	public $winner;

	/**
	 * Conmstructor that knows how to retrieve all fields from a given data set
	 * @param array $dataSet a data set prefrably directly from an SQL statement
	 */
	public function __construct($dataSet = null) {
		if ($dataSet != null) {
			$this->matchId 		= (int) ($dataSet[self::GAME_CLM_MATCH_ID]);
			$this->dateTime	 	= strtotime($dataSet[self::GAME_CLM_DATETIME]);
			$this->playerA1 	= (string) $dataSet[self::GAME_CLM_PLAYER_A1];
			$this->playerB1 	= (string) $dataSet[self::GAME_CLM_PLAYER_B1];
			$this->playerA2 	= (string) $dataSet[self::GAME_CLM_PLAYER_A2];
			$this->playerB2 	= (string) $dataSet[self::GAME_CLM_PLAYER_B2];
			$this->setA1		= (int) ($dataSet[self::GAME_CLM_SET_A1]);
			$this->setB1		= (int) ($dataSet[self::GAME_CLM_SET_B1]);
			$this->setA2		= (int) ($dataSet[self::GAME_CLM_SET_A2]);
			$this->setB2		= (int) ($dataSet[self::GAME_CLM_SET_B2]);
			$this->setA3		= (int) ($dataSet[self::GAME_CLM_SET_A3]);
			$this->setB3		= (int) ($dataSet[self::GAME_CLM_SET_B3]);
			$this->winner		= (string) $dataSet[self::GAME_CLM_WINNER];
		} else {
			$this->matchId 		= 0;
			$this->dateTime	 	= strtotime("1-1-2017 00:00:00");
			$this->playerA1 	= "N/A";
			$this->playerB1 	= "N/A";
			$this->playerA2 	= "N/A";
			$this->playerB2 	= "N/A";
			$this->setA1		= 0;
			$this->setB1		= 0;
			$this->setA2		= 0;
			$this->setB2		= 0;
			$this->setA3		= 0;
			$this->setB3		= 0;
			$this->winner		= "N/A";
		}
	}

	public function getDateHTML():string
	{
		$date = date("Y-m-d", $this->dateTime);
		return $date;
	}

	public function getDate():string
	{
		$date = date("d.m.Y", $this->dateTime);
		return $date;
	}

	public function getTime():string
	{
		$time = date("H:i:s", $this->dateTime);
		return $time;
	}
}
?>
