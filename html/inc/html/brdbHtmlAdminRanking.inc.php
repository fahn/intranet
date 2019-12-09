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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgGame.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminRanking extends BrdbHtmlPage {
  private $prgElementGame;
  private $vars;

  public function __construct() {
    parent::__construct();
    $this->prgElementGame = new PrgPatternElementGame($this->brdb, $this->prgPatternElementLogin);
    $this->prgPattern->registerPrg($this->prgElementGame);

    // TOOLS
    $this->tools = new Tools();
  }

  public function processPage() {
    // Call all prgs and process them all
    parent::processPage();

  }

  protected function htmlBody() {
    $action = $this->tools->get("action");

    switch ($action) {
      case 'add':
        $content = $this->TMPL_AddGame();
        break;

      case 'edit':
        $content = $this->TMPL_EditGame();
        break;

      case 'delete':
        $content = $this->TMPL_deleteGame();

      default:
        $content = $this->TMPL_ListGames();
        break;
    }

    //

    $this->smarty->assign(array(
      'content' => $content,
    ));
    $this->smarty->display('index.tpl');
  }

  private function TMPL_AddGame() {
    $this->smarty->assign(array(
      'players'  => $this->getAllPlayerDataList(),
      'reporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
      'game'     => array(
        'datetime' => date("d.m.Y H:I"),
      ),
    ));

    return $this->smarty->fetch('ranking/updateGame.tpl');
  }




  private function TMPL_EditGame() {
    $id   = $this->tools->get("id");
    $game = $this->brdb->selectGameById($id);
    $gameArr = $game->fetch_assoc();
    $gameArr['set1'] = $gameArr['setA1'] .":". $gameArr['setB1'];
    $gameArr['set2'] = $gameArr['setA2'] .":". $gameArr['setB2'];
    if(isset($gameArr['setA3']) && $gameArr['setA3'] > 0 && isset($gameArr['setB3']) && $gameArr['setB3'] > 0) {
        $gameArr['set3'] = $gameArr['setA3'] .":". $gameArr['setB3'];
    }
    $this->smarty->assign(array(
      'players'  => $this->getAllPlayerDataList(),
      'reporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
      'game'     => $gameArr,
      'action'   => 'update',
    ));

    return $this->smarty->fetch('ranking/updateGame.tpl');
  }


  private function TMPL_ListGames() {
    $this->smarty->assign(array(
      'games'      => $this->getRankedGames(),
      'error'      => $this->brdb->getError(),
      'isReporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
    ));

    return $this->smarty->fetch('ranking/list.tpl');
  }

  private function TMPL_DeleteGame() {
    $this->smarty->assign(array(
      'games'      => $this->getRankedGames(),
      'error'      => $this->brdb->getError(),
      'isReporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
    ));

    return $this->smarty->fetch('ranking/list.tpl');
  }

  private function getAllPlayerDataList() {
    $res = $this->brdb->selectAllPlayerByOurClub();
    $data = array();
    if (!$this->brdb->hasError()) {
      while ($dataSet = $res->fetch_assoc()) {
        $data[] 		= array(
          'userId'   => $dataSet['userId'],
          'fullName' => $dataSet['fullName'],
        );
      }
    }

    return $data;
  }

  /**
    * Get all ranked Games
  */
  private function getRankedGames() {
    $tmp = array();
    $res = $this->brdb->selectAllGames();
    if (!$this->brdb->hasError()) {
      while ($dataSet = $res->fetch_assoc()) {
        $tmp[] = $dataSet;
      }
    }
    return $tmp;
  }
}

?>
