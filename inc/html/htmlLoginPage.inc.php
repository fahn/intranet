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

include_once '../inc/html/htmlPage.inc.php';
include_once '../inc/logic/prgLogin.inc.php';
include_once '../inc/logic/tools.inc.php';
include_once '../inc/db/brdb.inc.php';


/**
 * Implements an html page with login capabilities.
 * It also provides a protected content are which is only
 * displayed on sucessful login. the class also handles the login
 * and logout dialog. All pages that use this class and derive from it
 * will have support for the login functionality
 *
 * @author philipp
 *
 */
abstract class AHtmlLoginPage extends HtmlPageProcessor {

	protected $brdb;
	protected $prgPattern;

	protected $prgPatternElementLogin;


	/**
	 * Standard Constructor for the HTML Login page. It
	 * takes care of creating the connection to the data base
	 * and stores the DB connection object into the member variable
	 * $brdb. it is protected and therefore accessible by all derived
	 * classes (pages)
	 */
	public function __construct() {
		parent::__construct();



        /* SQL CONNECTION */
		$this->brdb = new BrankDB();
		$this->brdb->connectAndSelectDB();
		$this->brdb->prepareCommands();

		$this->prgPatternElementLogin = new PrgPatternElementLogin($this->brdb);

		$this->prgPattern = new PrgPattern();
		$this->prgPattern->registerPrg($this->prgPatternElementLogin);

		// goto Login
		$basename = basename($_SERVER['SCRIPT_URL']);
		if($basename != "index.php" && !$this->prgPatternElementLogin->isUserLoggedIn()) {
			header("Location: https://rl.weinekind.de/pages/index.php");
			die();
		}
	}

	public function processPage() {
		$this->getMessages();

		$isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();
		if($isUserLoggedIn AND $this->smarty) {
			$currentUserName = $this->prgPatternElementLogin->getLoggedInUser()->getFullName();

			$user = $this->prgPatternElementLogin->getLoggedInUser();
			$this->smarty->registerObject('user', $user);

			$this->smarty->assign(array(
					'currentUserName' => $currentUserName,
					'isUserLoggedIn'  => $isUserLoggedIn,
					'isAdmin'         => $this->prgPatternElementLogin->getLoggedInUser()->isAdmin(),
					'userId'          => $this->prgPatternElementLogin->getLoggedInUser()->getID(),
			));
		}

		// Call all prgs and process them all
		$this->prgPattern->processPRG();
		parent::processPage();

    #$this->htmlBody();
	}

	/**
	 * This method hands back the currently logged in user
	 * @return User the currently logged in user or null in
	 * noone is logged in
	 */
	public function getLoggedInUser() {
		return $this->prgPatternElementLogin->getLoggedInUser();
	}

	/**
	 * This method handles the display of the content
	 * depending on the current user being logged in.
	 * The method also provides rendering of the protected
	 * content. This content is onyl displayed if a user is
	 * successfully logged in and other additional criteria
	 * are met.
	 */
	protected function htmlBody() {
		$isUserLoggedIn = $this->prgPatternElementLogin->isUserLoggedIn();

		$this->getMessages();


		// Now decide the content on if use ris logged in or not
		if($isUserLoggedIn) {
			$this->smarty->assign(array(
					'content' => $this->loadContent(),
			));




			// in case there is a logged in user show the logout dialog
			// and display the body area with the protected content
			$this->smarty->display('index.tpl');
		} else {
			// if there is no user logged in, then show the content to
			// to perform a new login
			$variableNameEmail 			 = $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
			$variableNameEmailValue	 = $this->prgPatternElementLogin->safeGetSessionVariable(PrgPatternElementLogin::FORM_LOGIN_EMAIL);
			$variableNamePassw 			 = $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_PASSWORD);
			$variableNameAction 		 = $this->prgPatternElementLogin->getPrefixedName(PrgPatternElementLogin::FORM_LOGIN_ACTION);
			$variableNameActionLogin = PrgPatternElementLogin::FORM_LOGIN_ACTION_LOGIN;

			$this->smarty->assign(array(
					'variableNameEmail'       => $variableNameEmail,
					'variableNamePassw'       => $variableNamePassw,
					'formTO'                  => BrdbHtmlPage::PAGE_INDEX,
					'variableNameAction'      => $variableNameAction,
					'variableNameActionLogin' => $variableNameActionLogin,
			));
			$this->smarty->display('login.tpl');
		}
	}

	protected function getMessages() {
		if ($this->prgPattern->hasStatus()) {
			foreach ($this->prgPattern->getRegisteredPrgElements() as $prg) {
				$messages[] = $prg->getStatusMessage();
			}
			$this->smarty->assign('messages', $messages);
		}
	}

	private function loadContent() {
		$this->smarty->assign(array(
				'games'         => $this->getGamesByUser(),
				'tournaments'   => $this->getLatestTournament(),
				'users'         => $this->getAdminsAndReporter(),
		));
		return $this->smarty->fetch('default.tpl');
	}

	private function getAdminsAndReporter() {
		$data = array();
		$res = $this->brdb->GetActiveAndReporterOrAdminPlayer();
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= array(
					'userId'   => $dataSet['userId'],
					'fullName' => $dataSet['firstName'] .' '. $dataSet['lastName'],
				);
			}
		}
		return $data;
	}

	private function getLatestTournament() {
		$data = array();
		$res = $this->brdb->selectTournamentListMax(5);
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				$data[] 		= array(
					'tournamentID' => $dataSet['tournamentID'],
					'name'         => $dataSet['name'],
					'startdate'    => $dataSet['startdate'],
					'enddate'      => $dataSet['enddate'],
					'deadline'     => $dataSet['deadline'],
				);
			}
		}
		return $data;
	}

	private function getGamesByUser() {
		$data      = array();
		$user_id   = $this->prgPatternElementLogin->getLoggedInUser()->getID();
		$user_name = $this->prgPatternElementLogin->getLoggedInUser()->getFullName();
		$res       = $this->brdb->selectLatestGamesByPlayerId($user_id);
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
				// OPPONENT
				if(!empty($dataSet['playerA1']) && !empty($dataSet['playerA"']) && strpos($user_name, $dataSet['playerA1']) !== false   && strpos($user_name, $dataSet['playerA2']) !== false) {
					$opponent = $dataSet['playerA1'] .(strlen($dataSet['playerA2']) > 0 ? ' // '. $dataSet['playerA2'] : '');
				} else {
					$opponent = $dataSet['playerB1'] .(strlen($dataSet['playerB2']) > 0  ? ' // '. $dataSet['playerB2'] : '');
				}
				// RESULT
				$result = $dataSet['setA1'] .':'. $dataSet['setB1'] .' '. $dataSet['setA2'] .':'. $dataSet['setB2'];
				if(isset($dataSet['setA3']) && is_numeric($dataSet['setA3'])) {
					$result .= ' '. $dataSet['setA3'] .':'. $dataSet['setB3'];
				}
				$data[] 		= array(
					'result' => $result,
					'opponent' => $opponent,
					'datetime' => $dataSet['datetime'],
				);
			}
		}
		return $data;
	}
}
?>
