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


class BrdbHtmlUserInformation extends BrdbHtmlPage {
  private $prgElementUser;
  private $vars;
  private $id;

  public function __construct() {
    parent::__construct();

    $this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
    $this->prgPattern->registerPrg($this->prgElementUser);
  }

  public function processPage() {
    parent::processPage();
  }


  protected function htmlBody() {
    $id = $this->getGetVariable('id');
    $content = $this->loadContent($id);

    $this->smarty->assign(array(
      'content' => $content,
    ));

    $this->smarty->display('index.tpl');
  }


  private function loadContent($id) {
    if(!isset($id) or !is_numeric($id)) {
      return "";
    }

    $user = $this->brdb->selectUserById($id)->fetch_assoc();
    $club = $this->brdb->selectGetClubById($user['clubId'])->fetch_assoc();
    $this->smarty->assign(array(
      'user'       => $user,
      'club'       => $club,
      'tournament' => $this->getLatestTournamentFromUserId($id),
      'games'      => $this->getRankedGamesByUser(),

    ));

    return $this->smarty->fetch('user/profil.tpl');
  }


  public function getGetVariable($variableName) {
    return Tools::escapeInput($_GET[$variableName]);
  }

  public function getLatestTournamentFromUserId($id) {

    $res = $this->brdb->selectGetLatestTournamentFromUserId($id);
    $loop = array();
    if (!$this->brdb->hasError()) {
      while ($dataSet = $res->fetch_assoc()) {
        $loop[] = $dataSet; //new User($dataSet);

      }
    }

    return $loop;
  }

  private function getRankedGamesByUser() {
      $tools     = new Tools();
      $data      = array();
      $user_id   = $tools->get("id");
      $res       = $this->brdb->selectLatestGamesByPlayerId($user_id);

      // User
      $user_res  = $this->brdb->selectUserById($user_id);
      $user      = $user_res->fetch_assoc();
      $username  = $user['firstName'] ." ". $user['lastName'];

      if (!$this->brdb->hasError()) {
          while ($dataSet = $res->fetch_assoc()) {
              // OPPONENT
              if(!empty($dataSet['playerA1']) && !empty($dataSet['playerA2']) && strpos($user_name, $dataSet['playerA1']) !== false   && strpos($user_name, $dataSet['playerA2']) !== false) {
                  $opponent = $dataSet['playerA1'] .(strlen($dataSet['playerA2']) > 0 ? ' // '. $dataSet['playerA2'] : '');
              } else {
                  $opponent = $dataSet['playerB1'] .(strlen($dataSet['playerB2']) > 0  ? ' // '. $dataSet['playerB2'] : '');
              }
              // RESULT
              $result = $dataSet['setA1'] .':'. $dataSet['setB1'] .' '. $dataSet['setA2'] .':'. $dataSet['setB2'];
              if(isset($dataSet['setA3']) && is_numeric($dataSet['setA3'])) {
                  $result .= ' '. $dataSet['setA3'] .':'. $dataSet['setB3'];
              }

              // chicken
              if((strpos($username, $dataSet['playerA1']) === 0 || (isset($dataSet['playerA2']) && strpos($username, $dataSet['playerA2']) === 0)) && $dataSet['side'] == 'Side A') {
                $chicken = '<i class="fas fa-arrow-circle-up text-success"></i>';
              } else {
                $chicken = '<i class="fas fa-arrow-circle-down text-danger"></i>';
              }


              $data[]         = array(
                  'result'   => $result,
                  'opponent' => $opponent,
                  'datetime' => $dataSet['datetime'],
                  'chicken'  => $chicken,
              );
          }
      }
      return $data;
  }
}
?>
