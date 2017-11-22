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
include_once '../inc/logic/prgPattern.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementTournament extends APrgPatternElement {

	private $brdb;

	const FORM_FORM_ACTION								= "formAction";
	// insert player to tournament
	const FORM_GAME_ACTION_INSERT_PLAYERS	= "Insert Players";
	const FORM_INPUT_PLAYER 							= "playerId";
	const FORM_INPUT_PARTNER 							= "partnerId";
	const FORM_INPUT_DISCIPLIN 						= "diziplin";

	// insert tournament
	const FORM_GAME_ACTION_INSERT_TOURNAMENT	= "Insert Tournament";
	const FORM_INPUT_TOURNAMENT_NAME          = "name";
	const FORM_INPUT_TOURNAMENT_PLACE         = "place";
	const FORM_INPUT_TOURNAMENT_STARTDATE     = "startdate";
	const FORM_INPUT_TOURNAMENT_ENDDATE       = "enddate";
	const FORM_INPUT_TOURNAMENT_DEADLINE      = "deadline";
	const FORM_INPUT_TOURNAMENT_LINK          = "link";
	const FORM_INPUT_TOURNAMENT_CLASS         = "class";
	const FORM_INPUT_TOURNAMENT_MODE          = "mode";




	// Errors that can be set by methods of this class
	const ERROR_GAME_MISSING_INFORMATION 		= "Please provide all required information!";
	const SUCCESS_GAME_INSERT 					    = "Succesfully inserted game!";
	const SUCCESS_UPDATING_GAME_READ 			  = "Succesfully read game (ID: %d) for updating!";
	const SUCCESS_GAME_UPDATED              = "Succesfully updated game!";
	const SUCCESS_GAME_DELETE 					    = "Succesfully deleted game!";
	const ERROR_GAME_FAILED_TO_GET_USER_ID  = "Could not identify user!";


	const ERROR_GAME_FAILED_TO_ADD_USER    = "Der Spieler konnte nicht hinzugefügt werden";

	protected $prgElementLogin;

	public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
		parent::__construct("tournament");
		$this->brdb = $brdb;
		$this->prgElementLogin = $prgElementLogin;

		// add player to Tournament
		$this->registerPostSessionVariable(self::FORM_FORM_ACTION);
		$this->registerPostSessionVariable(self::FORM_INPUT_PLAYER);
		$this->registerPostSessionVariable(self::FORM_INPUT_PARTNER);
		$this->registerPostSessionVariable(self::FORM_INPUT_DISCIPLIN);

		// add Torunament
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_NAME);
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_PLACE);
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE);
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE);
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE);
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_LINK);
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_CLASS);
		$this->registerPostSessionVariable(self::FORM_INPUT_TOURNAMENT_MODE);

	}

	/**
		Delete Player from Tournament
		@TODO: Check rights, and if Tournament exists
	*/
	public function deletePlayersFromTournamentId($tournamentId, $playerId) {
		$res = $this->brdb->deletePlayersFromTournamentId($tournamentId, $playerId);
		if ($this->brdb->hasError()) {
			$this->setFailedMessage('Der Spieler konnte nicht gelöscht werden');
		}
		$this->setSuccessMessage('Der Spieler wurde aus dem Turnier gelöscht');
		return;
		#$url = "https://rl.weinekind.de/pages/rankingTournament.php?action=details&id=". $tournamentId;
		#$this->customRedirect($url);
	}

	public function processPost() {
		$isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
		$isUserAdmin 	  = $this->prgElementLogin->getLoggedInUser()->isAdmin();
		$isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

		// Don't process the posts if no user is logged in!
		// otherwise well formed post commands could trigger database actions
		// without theoretically having access to it.
		if (!$this->prgElementLogin->isUserLoggedIn()) {
			return;
		}

		if ($this->issetPostVariable(self::FORM_FORM_ACTION)) {
			$loginAction = strval(trim($this->getPostVariable(self::FORM_FORM_ACTION)));
			if ($loginAction === self::FORM_GAME_ACTION_INSERT_PLAYERS) {
				$this->processPostInsertPlayerToTournament();
			} else if ($isUserReporter && ($loginAction === self::FORM_GAME_ACTION_INSERT_TOURNAMENT)) {
				$this->processPostInsertTournament();
			} #else if ($isUserReporter && ($loginAction === self::FORM_GAME_ACTION_UPDATE_GAME)) {
				#$this->processPostUpdateGame();
			  #}
		}
	}
	/**
		INSERT TOURNAMENT
	*/
	private function processPostInsertTournament() {
		if ($this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_NAME) &&
			$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_PLACE) &&
			$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE) &&
			$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE) &&
			$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE) &&
			$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_LINK) &&
			$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_CLASS) &&
			$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_MODE)) {

				if(empty($this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_NAME)) ||
				$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_PLACE) ||
				$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE) ||
				$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE) ||
				$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE) ||
				$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_LINK) ||
				$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_CLASS) ||
				$this->issetPostVariable(self::FORM_INPUT_TOURNAMENT_MODE)) {
					$this->setFailedMessage("Bitte füllen Sie das Formular aus");
				}

				$name      = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_NAME);
				$place     = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_PLACE);
				$startdate = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_STARTDATE)));
				$enddate   = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_ENDDATE)));
				$deadline  = date("Y-m-d", strtotime($this->getPostVariable(self::FORM_INPUT_TOURNAMENT_DEADLINE)));
				$link      = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_LINK);

				if(empty($name)) {
					$this->setFailedMessage("Bitte füllen Sie das Formular aus");
					return;
				}

				if(empty($place)) {
					$this->setFailedMessage("Bitte füllen Sie das Formular aus");
					return;
				}

				// insert tournament
				$this->brdb->insertTournament($name, $place, $startdate, $enddate, $deadline, $link);
				if ($this->brdb->hasError()) {
					$this->setFailedMessage($this->brdb->getError());
					return;
				}
				$insertid = $this->brdb->insert_id();
				$class = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_CLASS);
				$mode  = $this->getPostVariable(self::FORM_INPUT_TOURNAMENT_MODE);
				$mode_arr= array('HE', 'DE', 'HD', 'DD', 'MX');

				foreach($class as $key => $value) {
					$value_tmp = strtoupper($value);
					$mode_tmp  = strtoupper($mode[$key]);
					if(!empty($value_tmp) and !empty($mode_tmp)) {
						if ( !in_array($mode_tmp, $mode_arr)) {
							$this->setFailedMessage("Diziplin entspricht nicht dem array:". implode(",", $mode_arr));
							return;
						}
						$this->brdb->insertTournamentClass($insertid, $value_tmp, $mode_tmp);
						if ($this->brdb->hasError()) {
							$this->setFailedMessage($this->brdb->getError());
							return;
						}
					}
				}
				// insert class

				$this->setSuccessMessage("Turnier \"". $name ."\" wurde hinzugefügt.");
				return;

				die(print_r($_POST));
		} else {
			$this->setFailedMessage("Bitte füllen Sie das Formular aus");
			return;
		}
	}

	public function processPostInsertPlayerToTournament() {
		// Check that all information has been posted
		if ($this->issetPostVariable(self::FORM_INPUT_PLAYER) &&
			$this->issetPostVariable(self::FORM_INPUT_PARTNER) &&
			$this->issetPostVariable(self::FORM_INPUT_DISCIPLIN)) {

			$player 	  = $this->getPostVariable(self::FORM_INPUT_PLAYER);
			$partner 	  = $this->getPostVariable(self::FORM_INPUT_PARTNER);
			$disziplin 	= $this->getPostVariable(self::FORM_INPUT_DISCIPLIN);

			$reporterId = $this->prgElementLogin->getLoggedInUser()->getID();

			if(empty(array_filter($player))) {
				$this->setFailedMessage(self::ERROR_GAME_FAILED_TO_ADD_USER);
				return;
			}

			foreach ($player as $key => $value) {
				$tmp_disziplin = isset($disziplin[$key]) ? $disziplin[$key] : 0;
				$tmp_partner   = isset($partner[$key]) ? $partner[$key] : 0;
				$id = $_GET['id'];
				$this->brdb->insertPlayerToTournament($id, $value, $tmp_partner, $tmp_disziplin, $reporterId);
				if ($this->brdb->hasError()) {
					$this->setFailedMessage($this->brdb->getError());
					return;
				}
			}

			$this->setSuccessMessage("Der Spieler/Die Paarung wurde eingetragen");
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
			//$this->setSessionVariable(self::FORM_GAME_WINNER	, $game->winner);

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
		if (isset($_GET[self::FORM_FORM_ACTION])) {
			$formAction = strVal(Tools::escapeInput($_GET[self::FORM_FORM_ACTION]));
			if ($formAction == self::FORM_GAME_ACTION_NEW_GAME) {
				$this->setSessionVariable(self::FORM_GAME_ADMIN_MATCH_ID, self::FORM_GAME_ACTION_NEW_GAME);
				$this->clearSessionVariables();
			}
		}
	}
}
?>
