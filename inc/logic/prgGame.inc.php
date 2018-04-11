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

  const FORM_GAME_DATE	 		      = "gameDate";
  const FORM_GAME_TIME 			      = "gameTime";
  const FORM_GAME_PLAYER_A_1 		  = "gamePlayerA1";
  const FORM_GAME_PLAYER_B_1 		  = "gamePlayerB1";
  const FORM_GAME_PLAYER_A_2 		  = "gamePlayerA2";
  const FORM_GAME_PLAYER_B_2 		  = "gamePlayerB2";
  const FORM_GAME_SET_1 	  	    = "gameSet1";
  const FORM_GAME_SET_2   		    = "gameSet2";
  const FORM_GAME_SET_3   		    = "gameSet3";
  #const FORM_GAME_WINNER 			= "gameWinner";

  #const FORM_GAME_WINNER_SIDE_A = "Side A";
  #const FORM_GAME_WINNER_SIDE_B = "Side B";
  const  FORM_GAME_ADMIN_MATCH_ID    = "gameAdminMatchId";
  const FORM_GAME_ACTION 						 = "formAction";
  const FORM_GAME_ACTION_NEW_GAME	   = "NewGame";
  const FORM_GAME_ACTION_INSERT_GAME = "Insert Game";
  const FORM_GAME_ACTION_UPDATE_GAME = "Update Game";
  const FORM_GAME_ACTION_DELETE_GAME = "Delete Game";


  // Errors that can be set by methods of this class
  const ERROR_GAME_MISSING_INFORMATION 		= "Please provide all required information!";
  const SUCCESS_GAME_INSERT 						  = "Succesfully inserted game!";
  const SUCCESS_UPDATING_GAME_READ 			  = "Succesfully read game (ID: %d) for updating!";
  const SUCCESS_GAME_UPDATED              = "Succesfully updated game!";
  const SUCCESS_GAME_DELETE 				     	= "Succesfully deleted game!";
  const ERROR_GAME_FAILED_TO_GET_USER_ID 	= "Could not identify user!";

  protected $prgElementLogin;

  private $tools;

  public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
    parent::__construct("ranking");
    $this->brdb            = $brdb;
    $this->prgElementLogin = $prgElementLogin;


    $this->registerPostSessionVariable(self::FORM_GAME_DATE);
    $this->registerPostSessionVariable(self::FORM_GAME_TIME);
    $this->registerPostSessionVariable(self::FORM_GAME_PLAYER_A_1);
    $this->registerPostSessionVariable(self::FORM_GAME_PLAYER_B_1);
    $this->registerPostSessionVariable(self::FORM_GAME_PLAYER_A_2);
    $this->registerPostSessionVariable(self::FORM_GAME_PLAYER_B_2);
    $this->registerPostSessionVariable(self::FORM_GAME_SET_1);
    $this->registerPostSessionVariable(self::FORM_GAME_SET_2);
    $this->registerPostSessionVariable(self::FORM_GAME_SET_3);


    $this->tools = new Tools();
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


  /**
   *
   * {@inheritDoc}
   * @see IPrgPatternElement::processGet()
   */
  public function processGet() {
    $action = $this->tools->get("action");
    $id     = $this->tools->get("id");
    switch ($action) {
      case 'delete':
        $this->processGetDeleteGame($id);
        break;

      default:
        # code...
        break;
    }
    // Check that all information has been posted
    if (isset($_GET[self::FORM_GAME_ACTION])) {
      $formAction = strVal(Tools::escapeInput($_GET[self::FORM_GAME_ACTION]));
      if ($formAction == self::FORM_GAME_ACTION_NEW_GAME) {
        $this->setSessionVariable(self::FORM_GAME_ADMIN_MATCH_ID, self::FORM_GAME_ACTION_NEW_GAME);
        $this->clearSessionVariables();
      }
    }
  }

  private function processGetDeleteGame($id) {
    if(!isset($id) || !is_numeric($id)) {
      $this->setFailedMessage("Da lief wohl etwas schief");
      return;
    }

    $this->brdb->deleteGame($id);
    if ($this->brdb->hasError()) {
      $this->setFailedMessage($this->brdb->getError());
      return;
    }
    $this->setSuccessMessage(self::SUCCESS_GAME_DELETE);
    return;
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
    if (
      ! $this->issetPostVariable(self::FORM_GAME_DATE) ||
      ! $this->issetPostVariable(self::FORM_GAME_TIME) ||
      ! $this->issetPostVariable(self::FORM_GAME_PLAYER_A_1) ||
      ! $this->issetPostVariable(self::FORM_GAME_PLAYER_B_1) ||
      ! $this->issetPostVariable(self::FORM_GAME_PLAYER_A_2) ||
      ! $this->issetPostVariable(self::FORM_GAME_PLAYER_B_2) ||
      ! $this->issetPostVariable(self::FORM_GAME_SET_1) ||
      ! $this->issetPostVariable(self::FORM_GAME_SET_2) ||
      ! $this->issetPostVariable(self::FORM_GAME_SET_3)
    ) {
      $this->setFailedMessage(self::ERROR_GAME_MISSING_INFORMATION);
      return;
    }

    $date	 	  = strval(trim($this->getPostVariable(self::FORM_GAME_DATE)));
    $time 		= strval(trim($this->getPostVariable(self::FORM_GAME_TIME)));

    $playerA1 = strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_A_1)));
    $playerB1 = strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_B_1)));
    $playerA2 = strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_A_2)));
    $playerB2 = strval(trim($this->getPostVariable(self::FORM_GAME_PLAYER_B_2)));

    $set1 		= trim($this->getPostVariable(self::FORM_GAME_SET_1));
    $set2 		= trim($this->getPostVariable(self::FORM_GAME_SET_2));
    $set3 		= trim($this->getPostVariable(self::FORM_GAME_SET_3));

    if(
      preg_match("/[0-9]{1,2}:[0-9]{1,2}/",$set1) !== 1) {
        $this->setFailedMessage("Sätze stimmen nicht.1");
        return;
    }


    $set1 = explode(":", $set1);
    $set2 = explode(":", $set2);

    if(!$this->compare($set1) || !$this->compare($set2)) {
      $this->setFailedMessage("Sätze stimmen nicht.2");
      return;
    }

    // set winner
    $a = 0;
    $b = 0;
    if($set1[0] > $set1[0]) {
        $a++;
    } else {
        $b++;
    }
    if($set2[0] > $set2[0]) {
        $a++;
    } else {
        $b++;
    }

    //  3. Satz
    if ($a == $b) {
      if (!isset($set3)) {
        $this->setFailedMessage("Sätze stimmen nicht.3");
        return;
      }
      $set3 = explode(":", $set3);
      if (!$this->compare($set3)) {
        $this->setFailedMessage("Sätze stimmen nicht.4");
        return;
      }

      if($set3[0] > $set3[0]) {
          $a++;
      } else {
          $b++;
      }

    } else {
      $set3 = array(0,0);
    }
    $winner = $a > $b ? "Side A" : "Side B";

    // set datetime
    $dateTime 	= date("Y-m-d H:i:00", strtotime("$date $time"));

    $playerA1Id = is_numeric($playerA1) ? $playerA1 : $this->getUserIdByFullName($playerA1);
    $playerB1Id = is_numeric($playerB1) ? $playerB1 : $this->getUserIdByFullName($playerB1);

    $playerA2Id = is_numeric($playerA2) ? $playerA2 : $this->getUserIdByFullName($playerA2);
    $playerB2Id = is_numeric($playerB2) ? $playerB2 : $this->getUserIdByFullName($playerB2);

    if (!isset($playerA1) || $playerA1Id == 0) {
      $this->setFailedMessage(ERROR_GAME_FAILED_TO_GET_USER_ID . " 1 " . $playerA1);
      return;
    }
    if (!isset($playerB1) || $playerB1Id == 0) {
      $this->setFailedMessage(ERROR_GAME_FAILED_TO_GET_USER_ID . " 2 " . $playerA12);
      return;
    }

    $res = $this->brdb->getLatestAutoIncrement("GameMatch");
    $id  = $res->fetch_array();
    $matchId = 'NewGame';
    $this->brdb->insertGame('NewGame', $dateTime,
        $playerA1Id, $playerB1Id,
        $playerA2Id, $playerB2Id,
        $set1[0], $set1[1],
        $set2[0], $set2[1],
        $set3[0], $set3[1],
        $winner);

    if ($this->brdb->hasError()) {
      $this->setFailedMessage($this->brdb->getError());
      return;
    }

    $this->setSuccessMessage("Das Spiel wurde eingetragen.");
    return;

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
      $this->setSessionVariable(self::FORM_GAME_PLAYER_A_1, $this->getUserIdByFullName($playerA1));
      $this->setSessionVariable(self::FORM_GAME_PLAYER_B_1, $this->getUserIdByFullName($playerB1));
      $this->setSessionVariable(self::FORM_GAME_PLAYER_A_2, $this->getUserIdByFullName($playerA2));
      $this->setSessionVariable(self::FORM_GAME_PLAYER_B_2, $this->getUserIdByFullName($playerB2));
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




  private function compare($set) {
    if(!isset($set) || !is_array($set) || count($set) != 2) {
      return false;
    }
    $min = $set[0];
    $max = $set[1];
    if($min > $max) {
      $x   = $max;
      $max = $min;
      $min = $x;
      unset($x);
    }
    if($max < 21 || $max > 30 || $min < 0 || $max == $min || $max-1 == $min) {
      return false;
    }

    return true;
  }
}
?>
