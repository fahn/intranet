<?php
/********************************************************
 * This file belongs to the Badminton Ranking Project.	*
 *														*
 * Copyright 2017										*
 *														*
 * All Rights Reserved									*
 *														*
 * Copying, distribution, usage in any form is not 		*
 * allowed without written permit.						*
 *														*
 * Philipp M. Fischer (phil.m.fischer@googlemail.com)	*
 *														*
 ********************************************************/

include_once '../inc/db/brdb.inc.php';
include_once '../inc/db/user.inc.php';
include_once '../inc/db/game.inc.php';
include_once '../inc/logic/prgPattern.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementGame extends APrgPatternElement {
	
	private $brdb;

	const FORM_GAME_MATCH_ID_NEW_GAME     = 0;
	
	const FORM_GAME_ADMIN_MATCH_ID 	= "gameAdminMatchId";
	const FORM_GAME_MATCH_ID 		= "gameMatchId";
	const FORM_GAME_DATE	 		= "gameDate";
	const FORM_GAME_TIME 			= "gameTime";
	const FORM_GAME_PLAYER_A_1 		= "gameUserA1Id";
	const FORM_GAME_PLAYER_B_1 		= "gameUserB1Id";
	const FORM_GAME_PLAYER_A_2 		= "gameUserA2Id";
	const FORM_GAME_PLAYER_B_2 		= "gameUserB2Id";
	const FORM_GAME_SET_A_1 		= "gameSetA1Points";
	const FORM_GAME_SET_B_1 		= "gameSetB1Points";
	const FORM_GAME_SET_A_2 		= "gameSetA2Points";
	const FORM_GAME_SET_B_2 		= "gameSetB2Points";
	const FORM_GAME_SET_A_3 		= "gameSetA3Points";
	const FORM_GAME_SET_B_3 		= "gameSetB3Points";
	const FORM_GAME_WINNER 			= "gameWinner";
	
	const FORM_GAME_WINNER_SIDE_A = "Side A";
	const FORM_GAME_WINNER_SIDE_B = "Side B";
	
	const FORM_GAME_ACTION 						= "formAction";
	const FORM_GAME_ACTION_NEW_GAME				= "NewGame";
	const FORM_GAME_ACTION_INSERT_GAME			= "Insert Game";
	const FORM_GAME_ACTION_UPDATE_GAME			= "Update Game";
	const FORM_GAME_ACTION_DELETE_GAME			= "Delete Game";
	
	
	// Errors that can be set by methods of this class
	const ERROR_GAME_MISSING_INFORMATION 		= "Please provide all required information!";
	const SUCCESS_GAME_INSERT 					= "Succesfully inserted game!";
	const SUCCESS_UPDATING_GAME_READ 			= "Succesfully read game (ID: %d) for updating!";
	const SUCCESS_GAME_UPDATED                  = "Succesfully updated game!";
	const SUCCESS_GAME_DELETE 					= "Succesfully deleted game!";
	const ERROR_GAME_FAILED_TO_GET_USER_ID 		= "Could not identify user!";
	
	protected $prgElementLogin;
	
	public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
		parent::__construct("userRegister");
		$this->brdb = $brdb;
		$this->prgElementLogin = $prgElementLogin;
		$this->registerPostSessionVariable(self::FORM_GAME_MATCH_ID);
		$this->registerPostSessionVariable(self::FORM_GAME_DATE);
		$this->registerPostSessionVariable(self::FORM_GAME_TIME);
		$this->registerPostSessionVariable(self::FORM_GAME_PLAYER_A_1);
		$this->registerPostSessionVariable(self::FORM_GAME_PLAYER_B_1);
		$this->registerPostSessionVariable(self::FORM_GAME_PLAYER_A_2);
		$this->registerPostSessionVariable(self::FORM_GAME_PLAYER_B_2);
		$this->registerPostSessionVariable(self::FORM_GAME_SET_A_1);
		$this->registerPostSessionVariable(self::FORM_GAME_SET_B_1);
		$this->registerPostSessionVariable(self::FORM_GAME_SET_A_2);
		$this->registerPostSessionVariable(self::FORM_GAME_SET_B_2);
		$this->registerPostSessionVariable(self::FORM_GAME_SET_A_3);
		$this->registerPostSessionVariable(self::FORM_GAME_SET_B_3);
		$this->registerPostSessionVariable(self::FORM_GAME_WINNER);
	}
	
	public function processPost() {
		$isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
		$isUserAdmin 	= $this->prgElementLogin->getLoggedInUser()->isAdmin();
		$isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();
		
		// Don't process the posts if no user is logged in!
		// otherwise well formed post commands could trigger database actions
		// without theoretically having access to it.
		if (!$this->prgElementLogin->isUserLoggedIn()) {
			return;
		}
		
		if ($this->issetPostVariable(self::FORM_GAME_ACTION)) {
			$loginAction = strval(trim($this->getPostVariable(self::FORM_GAME_ACTION)));
			if ($isUserReporter && ($loginAction === self::FORM_GAME_ACTION_INSERT_GAME)) {
				$this->processPostInsertGame();
			} else if ($isUserReporter && ($loginAction === self::FORM_GAME_ACTION_DELETE_GAME)) {
				$this->processPostDeleteGame();
			} else if ($isUserReporter && ($loginAction === self::FORM_GAME_ACTION_UPDATE_GAME)) {
				$this->processPostUpdateGame();
			} 
		}
	}
	
	public function processPostDeleteGame() {
		// Check that all information has been posted
		if ($this->issetPostVariable(self::FORM_GAME_ADMIN_MATCH_ID)) {
			
			// get the selected Id of the Match
			$matchId	= intval($this->getPostVariable(self::FORM_GAME_ADMIN_MATCH_ID));
			
			// now use the checked information and add the game to the db
			$this->brdb->deleteGame($matchId);
			if ($this->brdb->hasError()) {
				$this->setFailedMessage($this->brdb->getError());
				return;
			}
			$this->setSuccessMessage(self::SUCCESS_GAME_DELETE);
			return;
		} else {
			$this->setFailedMessage(self::ERROR_GAME_MISSING_INFORMATION);
		}
	}
	
	public function processPostInsertGame() {
		// Check that all information has been posted
		if ($this->issetPostVariable(self::FORM_GAME_DATE) &&
			$this->issetPostVariable(self::FORM_GAME_TIME) &&
			$this->issetPostVariable(self::FORM_GAME_PLAYER_A_1) &&
			$this->issetPostVariable(self::FORM_GAME_PLAYER_B_1) &&
			$this->issetPostVariable(self::FORM_GAME_PLAYER_A_2) &&
			$this->issetPostVariable(self::FORM_GAME_PLAYER_B_2) &&
			$this->issetPostVariable(self::FORM_GAME_SET_A_1) &&
			$this->issetPostVariable(self::FORM_GAME_SET_B_1) &&
			$this->issetPostVariable(self::FORM_GAME_SET_A_2) &&
			$this->issetPostVariable(self::FORM_GAME_SET_B_2) &&
			$this->issetPostVariable(self::FORM_GAME_SET_A_3) &&
			$this->issetPostVariable(self::FORM_GAME_SET_B_3) &&
			$this->issetPostVariable(self::FORM_GAME_WINNER) &&
			$this->issetSessionVariable(self::FORM_GAME_ADMIN_MATCH_ID)) {
			
			$date	 	= strval(trim($this->getPostVariable(self::FORM_GAME_DATE)));
			$time 		= strval(trim($this->getPostVariable(self::FORM_GAME_TIME)));
			$playerA1 	= strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_A_1)));
			$playerB1 	= strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_B_1)));
			$playerA2 	= strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_A_2)));
			$playerB2 	= strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_B_2)));
			$setA1 		= intval(trim($this->getPostVariable(self::FORM_GAME_SET_A_1)));
			$setB1 		= intval(trim($this->getPostVariable(self::FORM_GAME_SET_B_1)));
			$setA2 		= intval(trim($this->getPostVariable(self::FORM_GAME_SET_A_2)));
			$setB2 		= intval(trim($this->getPostVariable(self::FORM_GAME_SET_B_2)));
			$setA3 		= intval(trim($this->getPostVariable(self::FORM_GAME_SET_A_3)));
			$setB3 		= intval(trim($this->getPostVariable(self::FORM_GAME_SET_B_3)));
			$winner 	= strval(trim($this->getPostVariable(self::FORM_GAME_WINNER)));

			$matchId    = $this->getSessionVariable(self::FORM_GAME_ADMIN_MATCH_ID);
			
			$dateTime 	= $date . " " . $time;
			
			$playerA1Id = $this->getUserIdByFullName($playerA1);
			$playerB1Id = $this->getUserIdByFullName($playerB1);
			$playerA2Id = $this->getUserIdByFullName($playerA2);
			$playerB2Id = $this->getUserIdByFullName($playerB2);
			
			if ($playerA1 != "" && $playerA1Id == 0) {
				$this->setFailedMessage(ERROR_GAME_FAILED_TO_GET_USER_ID . " " . $playerA1);
				return;
			}
			if ($playerB1 != "" && $playerB1Id == 0) {
				$this->setFailedMessage(ERROR_GAME_FAILED_TO_GET_USER_ID . " " . $playerB1);
				return;
			}
			if ($playerA2 != "" && $playerA2Id == 0) {
				$this->setFailedMessage(ERROR_GAME_FAILED_TO_GET_USER_ID . " " . $playerA12);
				return;
			}
			if ($playerB2 != "" && $playerB2Id == 0) {
				$this->setFailedMessage(ERROR_GAME_FAILED_TO_GET_USER_ID . " " . $playerB2);
				return;
			}
			
			// now use the checked information and add the game to the db
			$this->brdb->insertGame($matchId, $dateTime,
					$playerA1Id, $playerB1Id,
					$playerA2Id, $playerB2Id,
					$setA1, $setB1,
					$setA2, $setB2,
					$setA3, $setB3,
					$winner);
			if ($this->brdb->hasError()) {
				$this->setFailedMessage($this->brdb->getError());
				return;
			}
			$this->setSuccessMessage(($matchId == self::FORM_GAME_MATCH_ID_NEW_GAME) ? self::SUCCESS_GAME_INSERT : self::SUCCESS_GAME_UPDATED);
			return;
		} else {
			$this->setFailedMessage(self::ERROR_GAME_MISSING_INFORMATION);
		}
	}
	
	/**
	 * Returns the id of the suer for a given full name 
	 * @param BrankDB $brdb the brdb to be used for this function
	 * @param $string $fullName the full name as a string
	 * @return the user id as integer or 0 in case the suer was not found -1 in case of error
	 */
	private function getUserIdByFullName($fullName) {
		$res = $this->brdb->getUserIdByFullName($fullName);
		if ($this->brdb->hasError()) {
			$this->setFailedMessage(self::ERROR_GAME_FAILED_TO_GET_USER_ID . " " . $fullName);
			return -1;
		}
		$dataSet = $res->fetch_assoc();
		if ($dataSet) {
			return intval($dataSet[User::USER_CLM_ID]);
		}
		return 0;
	}

	/**
	 * This method tells if the PRG is currently in Update or insert state
	 * @return boolean true in case the ADMIN Match ID is set
	 */
	public function isUpdategame() {
		if ($this->issetPostVariable(self::FORM_GAME_ADMIN_MATCH_ID)) {
			
			// get the admin ID and try to read the corresponding game from the
			// data base, process the rror in case of
			$adminMatchId = intval(trim($this->getPostVariable(self::FORM_GAME_ADMIN_MATCH_ID)));
			return $adminMatchId > FORM_GAME_MATCH_ID_NEW_GAME;
		}
	}
	
	/**
	 * This post method just rpocesses if the admin match id is set.
	 * If it is the emthod asks the DB for a given game and reads it.
	 * It also stores the game information into the session, hence the 
	 * insert game page will show the details.
	 */
	public function processPostUpdateGame() {
		// Check that all information has been posted
		if ($this->issetPostVariable(self::FORM_GAME_ADMIN_MATCH_ID)) {
			
			// get the admin ID and try to read the corresponding game from the
			// data base, process the rror in case of
			$adminMatchId = intval(trim($this->getPostVariable(self::FORM_GAME_ADMIN_MATCH_ID)));
			$res = $this->brdb->selectGameById($adminMatchId);
			if ($this->brdb->hasError()) {
				$this->setFailedMessage($this->brdb->getError());
				return;
			}
			
			// if no error occurred than read the game and write the
			// results to the session of the server
			$game = new Game($res->fetch_assoc());
			
			$this->setSessionVariable(self::FORM_GAME_ADMIN_MATCH_ID, $adminMatchId);
			
			$this->setSessionVariable(self::FORM_GAME_MATCH_ID	, $game->matchId);
			$this->setSessionVariable(self::FORM_GAME_DATE    	, $game->getDateHTML());
			$this->setSessionVariable(self::FORM_GAME_TIME		, $game->getTime());
			$this->setSessionVariable(self::FORM_GAME_PLAYER_A_1, $game->playerA1);
			$this->setSessionVariable(self::FORM_GAME_PLAYER_B_1, $game->playerB1);
			$this->setSessionVariable(self::FORM_GAME_PLAYER_A_2, $game->playerA2);
			$this->setSessionVariable(self::FORM_GAME_PLAYER_B_2, $game->playerB2);
			$this->setSessionVariable(self::FORM_GAME_SET_A_1	, $game->setA1);
			$this->setSessionVariable(self::FORM_GAME_SET_B_1	, $game->setB1);
			$this->setSessionVariable(self::FORM_GAME_SET_A_2	, $game->setA2);
			$this->setSessionVariable(self::FORM_GAME_SET_B_2	, $game->setB2);
			$this->setSessionVariable(self::FORM_GAME_SET_A_3	, $game->setA3);
			$this->setSessionVariable(self::FORM_GAME_SET_B_3	, $game->setB3);
			$this->setSessionVariable(self::FORM_GAME_WINNER	, $game->winner);
			
			$this->setSuccessMessage(sprintf(self::SUCCESS_UPDATING_GAME_READ, $adminMatchId));
			return;
		} else {
			$this->setFailedMessage(self::ERROR_GAME_MISSING_INFORMATION);
		}
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see IPrgPatternElement::processGet()
	 */
	public function processGet() {
		// Check that all information has been posted
		if (isset($_GET[self::FORM_GAME_ACTION])) {
			$formAction = strVal(Tools::escapeInput($_GET[self::FORM_GAME_ACTION]));				
			if ($formAction == self::FORM_GAME_ACTION_NEW_GAME) {
				$this->setSessionVariable(self::FORM_GAME_ADMIN_MATCH_ID, self::FORM_GAME_ACTION_NEW_GAME);
				$this->clearSessionVariables();
			}
		}
	}
}
?>