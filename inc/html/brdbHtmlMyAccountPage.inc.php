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
include_once '../inc/logic/prgUser.inc.php';
include_once '../inc/logic/tools.inc.php';

class BrdbHtmlMyAccountPage extends BrdbHtmlPage {
	private $prgElementUser;

	const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';

	public function __construct() {
		parent::__construct();
		$this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgElementUser);
	}

	public function htmlBody() {
		$variableName['Email'] 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_EMAIL);
		$variableName['FName'] 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_FNAME);
		$variableName['LName'] 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_LNAME);
		$variableName['Passw'] 			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD);
		$variableName['Passw2']			= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_PASSWORD2);
		$variableName['Action'] 		= $this->prgElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
		$variableName['ActionLogin'] 	= PrgPatternElementUser::FORM_USER_ACTION_UPDATE_MY_ACCOUNT;
		$variableName['GenderMale']		= PrgPatternElementUser::FORM_USER_GENDER_MALE;
		$variableName['GenderFemale']	= PrgPatternElementUser::FORM_USER_GENDER_FEMALE;

		$variableName['EmailValue']		  = strval($this->prgPatternElementLogin->getLoggedInUser()->email);
		$variableName['FNameValue']		  = strval($this->prgPatternElementLogin->getLoggedInUser()->firstName);
		$variableName['LNameValue']		  = strval($this->prgPatternElementLogin->getLoggedInUser()->lastName);
		$variableName['playerIdValue']  = strval($this->prgPatternElementLogin->getLoggedInUser()->playerId);
		$variableName['clubIdValue']		= strval($this->prgPatternElementLogin->getLoggedInUser()->clubId);
		$variableName['phoneValue']		  = strval($this->prgPatternElementLogin->getLoggedInUser()->phone);
		$variableName['bdayValue']		  = strval($this->prgPatternElementLogin->getLoggedInUser()->bday);
		$variableName['genderValue']		= strval($this->prgPatternElementLogin->getLoggedInUser()->gender);
		// get Club
		$clubName 												= $this->getClubById($variableName['clubIdValue']);
		$variableName['clubNameValue']		= strval($clubName['name']);

		$content = $this->loadContent($variableName);

		$this->smarty->assign(array(
			'content' => $content,
		));

		$this->smarty->display('index.tpl');
	}

	private function loadContent($vars) {
		$this->smarty->assign(array(
			'vars' => $vars,
		));
		return $this->smarty->fetch('myaccount.tpl');
	}

	/**
	  *
		*/
	private function getClubById($id) {
		if($id && is_numeric($id)) {
			$res = $this->brdb->selectGetClubById($id);
			if (!$this->brdb->hasError()) {
				return $res->fetch_assoc();
			}

			return "";
		}
	}


}

?>
