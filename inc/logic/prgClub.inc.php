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
include_once '../inc/logic/prgPattern.inc.php';

/**
 * This prg pattern ahndles all the post and get actions
 * to insert, delete or update a game in the data base.
 * @author philipp
 *
 */
class PrgPatternElementClub extends APrgPatternElement {

	private $brdb;
	const FORM_FIELD_ID          = "clubId";
	const FORM_FIELD_NAME        = "name";
	const FORM_FIELD_NUMBER 	   = "clubNumber";
	const FORM_FIELD_ASSOCIATION = "association";

	const FORM_CLUB_ACTION 						 = "formAction";
	const FORM_CLUB_ACTION_INSERT_GAME = "Insert Club";
	const FORM_CLUB_ACTION_UPDATE_GAME = "Update Club";
	const FORM_CLUB_ACTION_DELETE_GAME = "Delete Club";


	// Errors that can be set by methods of this class
	const SUCCESS_CLUB_INSERT 					 = "Succesfully inserted club!";
	const SUCCESS_CLUB_UPDATED           = "Succesfully updated club!";
	const SUCCESS_CLUB_DELETE 		 			 = "Succesfully deleted club!";

	const ERROR_CLUB_MISSING_INFORMATION = "Please provide all required information!";
	const ERROR_CLUB_FAILED   		       = "Could not identify user!";
	const ERROR_NO_IMPLEMENTATION        = "Noch nicht implementiert";

	protected $prgElementLogin;

	public function __construct(BrankDB $brdb, PrgPatternElementLogin $prgElementLogin) {
		parent::__construct("club");
		$this->brdb = $brdb;
		$this->prgElementLogin = $prgElementLogin;
		$this->registerPostSessionVariable(self::FORM_FIELD_NAME);
		$this->registerPostSessionVariable(self::FORM_FIELD_NUMBER);
		$this->registerPostSessionVariable(self::FORM_FIELD_ASSOCIATION);
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

		if ($this->issetPostVariable(self::FORM_CLUB_ACTION)) {
#die("HARD2");
			$loginAction = strval(trim($this->getPostVariable(self::FORM_CLUB_ACTION)));

			if ($isUserReporter && ($loginAction === self::FORM_CLUB_ACTION_INSERT_GAME)) {
				$this->processPostInsertClub();
			} else if ($isUserReporter && ($loginAction === self::FORM_CLUB_ACTION_DELETE_GAME)) {
				$this->processPostDeleteClub();
			} else if ($isUserReporter && ($loginAction === self::FORM_CLUB_ACTION_UPDATE_GAME)) {
				$this->processPostUpdateClub();
			}
		}
	}

	public function processPostDeleteClub() {
			$this->setFailedMessage(self::ERROR_NO_IMPLEMENTATION);
	}

	public function processPostInsertClub() {
		#echo $this->getPostVariable(self::FORM_FIELD_NAME), $this->getPostVariable(self::FORM_FIELD_NUMBER),  $this->getPostVariable(self::FORM_FIELD_ASSOCIATION);
		#die();

		// Check that all information has been posted
		if ($this->issetPostVariable(self::FORM_FIELD_NAME) &&
			$this->issetPostVariable(self::FORM_FIELD_NUMBER) &&
			$this->issetPostVariable(self::FORM_FIELD_ASSOCIATION)) {

			$name	      	= strval(trim($this->getPostVariable(self::FORM_FIELD_NAME)));
			$number	      = strval(trim($this->getPostVariable(self::FORM_FIELD_NUMBER)));
			$assosiation 	= strval(trim($this->getPostVariable(self::FORM_FIELD_ASSOCIATION)));

			$this->brdb->insertClub($name, $number, $assosiation);

			if ($this->brdb->hasError()) {
				$this->setFailedMessage($this->brdb->getError());
				return;
			}

			$this->setSuccessMessage(self::SUCCESS_CLUB_INSERT);
			return;

		} else {
			$this->setFailedMessage(self::ERROR_CLUB_MISSING_INFORMATION);
		}
	}



	/**
	 * This post method just rpocesses if the admin match id is set.
	 * If it is the emthod asks the DB for a given game and reads it.
	 * It also stores the game information into the session, hence the
	 * insert game page will show the details.
	 */
	public function processPostUpdateClub() {
		// Check that all information has been posted
		if ($this->issetPostVariable(self::FORM_FIELD_ID) &&
		    $this->issetPostVariable(self::FORM_FIELD_NAME) &&
				$this->issetPostVariable(self::FORM_FIELD_NUMBER) &&
				$this->issetPostVariable(self::FORM_FIELD_ASSOCIATION)) {

			$id           = intval(trim($this->getPostVariable(self::FORM_FIELD_ID)));
			$name	      	= strval(trim($this->getPostVariable(self::FORM_FIELD_NAME)));
			$number	      = strval(trim($this->getPostVariable(self::FORM_FIELD_NUMBER)));
			$association 	= strval(trim($this->getPostVariable(self::FORM_FIELD_ASSOCIATION)));

			// get the admin ID and try to read the corresponding game from the
			// data base, process the rror in case of
			$res = $this->brdb->updateClubById($id, $name, $number, $association);
			if ($this->brdb->hasError()) {
				$this->setFailedMessage($this->brdb->getError());
				return;
			}

			// if no error occurred than read the game and write the
			// results to the session of the server

			//$this->setSessionVariable(self::FORM_GAME_WINNER	, $game->winner);

			$this->setSuccessMessage(self::SUCCESS_CLUB_UPDATED);
			return;
		} else {
			$this->setFailedMessage(self::ERROR_CLUB_MISSING_INFORMATION);
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see IPrgPatternElement::processGet()
	 */
	public function processGet() {
		return;
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