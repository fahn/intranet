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

class BrdbHtmlAdminAllUserPage extends BrdbHtmlPage {
	private $prgPatternElementUser;

  const INPUT_RADIO_ATTRIBUTE_CHECKED = 'checked = "checked"';


	public function __construct() {
		parent::__construct();
		$this->prgPatternElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
		$this->prgPattern->registerPrg($this->prgPatternElementUser);
	}

	protected function showProtectedArea() {
		return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
	}



	protected function htmlBodyProtectedArea() {
		$variableNameAdminUserId			= $this->prgPatternElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ADMIN_USER_ID);
		$variableNameAction 				= $this->prgPatternElementUser->getPrefixedName(PrgPatternElementUser::FORM_USER_ACTION);
		$variableNameActionSelectAccount 	= PrgPatternElementUser::FORM_USER_ACTION_SELECT_ACCOUNT;
		$variableNameActionDeleteAccount 	= PrgPatternElementUser::FORM_USER_ACTION_DELETE_ACCOUNT;
	}

  public function htmlBody() {

    $this->smarty->assign(array(
      'content' => $this->loadContent(),
    ));

    $this->smarty->display('index.tpl');
  }


  private function loadContent() {
    $this->smarty->assign(array(
      'users' => $this->loadUserList(),
      'error' => $this->brdb->getError(),
    ));
    return $this->smarty->fetch('admin/UserList.tpl');
  }

  private function loadUserList() {
    $res = $this->brdb->selectAllUser();
		if (!$this->brdb->hasError()) {
			while ($dataSet = $res->fetch_assoc()) {
        // print_r($dataSet);
        $user = new User($dataSet);
        $radioId 		= $variableNameAdminUserId . "_" . $loopUser->userId;
				$isLoggedInUser = ($this->prgPatternElementLogin->getLoggedInUser()->userId == $loopUser->userId);
        $dataSet['radioId'] = $radioId;
        $dataSet['isLoggedInUser'] = $isLoggedInUser;
        $dataSet['isAdmin'] = $user->isAdmin();
        $dataSet['isReporter'] = $user->isReporter();
        $dataSet['isPlayer'] = $user->isPlayer();
				$loopUser[] = $dataSet; //new User($dataSet);

      }
    }

    return $loopUser;
  }
}

?>
