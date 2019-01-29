<?php

include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/db/user.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

class PrgPatternElementEloRanking extends APrgPatternElement {
  // DB
  private $db;

  const FORM_FORM_ACTION = "eloRanking";


  public function __construct() {
        $this->db = new BrankDB();
  }

  function processPost() {

    switch ($this->issetPostVariable(self::FORM_FORM_ACTION)) {
      case 'insert':
        #$this->insertMatch();
        break;

      default:
        // code...
        break;
    }

  }



  function widgetShowLatestGames($userId, $smarty) {
      $uid = $userId->userId;

      $data = array();
      $res  = $this->db->selectLatestGamesByPlayerId($uid);
      if (! $this->db->hasError() ) {
          while ($dataSet = $res->fetch_assoc()) {
              // chicken
              if($uid == $dataSet['winner']) {
                $chicken = '<i class="fas fa-arrow-circle-up text-success"></i>';
              } else {
                $chicken = '<i class="fas fa-arrow-circle-down text-danger"></i>';
              }

              $result = "1:0";
              $opponent = "AA";

              $data[]         = array(
                  'result'   => $result,
                  'opponent' => $opponent,
                  'datetime' => $dataSet['time'],
                  'chicken'  => $chicken,
              );
          }
      }

      $smarty->assign(array(
        'data' => $data,
        'link' => Tools::linkTo(array('page' => 'eloRanking.php')),
      ));

      return $smarty->fetch('elo/widgethShowLatestGames.tpl');
  }


  private function insertGame() {

  }

  private function calcRanking($userA, $userB, $winner = "A") {
    if ( $winner != "A" ) {
      return array_reverse($this->calcRanking($userB, $userA, "A"));
    }

  }

  private function updateRank($userId) {
    $this->db->selectEloPoints($userId);
    if ($this->db->hasError() ) {
      $points = array('userId' => $userId, 1000, 0, 0);
    } else {
      $dataSet = $res->fetch_assoc();
      $points = $dataSet;
    }

  }

  public function calcMatch($a, $b, $winnerA = true) {

    echo "<br>";
    echo "A: ". $a ."<br>";
    echo "B: ". $b ."<br>";
    echo "winner ". ($winnerA == true ? "A" : "B") ."<br>";

        if ($winnerA == false) {
          return array_reverse($this->calcMatch($b, $a, true));
        }
        $eloDiff = $b - $a;
        $elo = 1 / (1 + pow(10, ($eloDiff/200)));

        $a1 = (int) ($a + 15 * (1 - $elo));

        $b1 = (int) ($b + 15 * (0 - $elo));

        return array($a1, $b1);



  }

}
