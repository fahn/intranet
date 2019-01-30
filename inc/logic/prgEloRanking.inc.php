<?php

include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPattern.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/db/brdb.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/db/user.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

class PrgPatternElementEloRanking extends APrgPatternElement {
  // DB
  private $db;

  // tools
  private $tools;

  protected $prgElementLogin;

  // generell
  const FORM_FORM_ACTION  = "formAction";

  // Forms
  const FORM_INSERT_MATCH = "insertEloMatch";

  // Fields


  public function __construct() {
      // load DB
      $this->db = new BrankDB();

      // load Tools
      $this->tools = new Tools();
  }

  public function __loadPattern($prgElementLogin) {
      $this->prgElementLogin = $prgElementLogin;
  }

  function processPost() {

    $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
    $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
    $isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

    if ( ! $isUserLoggedIn || ! $isAdmin || ! $isReporter) {
        return;
    }

    switch ($this->issetPostVariable(self::FORM_INSERT_MATCH)) {
      case 'insert':
        #$this->insertMatch();
        break;

      default:
        // code...
        break;
    }

  }

  function processGet() {
    $isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
    $isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
    $isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

    if ( ! $isUserLoggedIn || ! $isAdmin || ! $isReporter) {
        return;
    }

    $action = $_GET['action'];

    if ( $action == 'test' ) {

    }
  }



  function widgetShowLatestGames($userId, $smarty) {
      // insert Match
      #$sets = serialize(array('21:12', '12:21', '21:12'));
      #$winner = $this->getWinner($sets) == 'true' ? 1 : 2;
      #$this->insertGame(1, 2, $sets, $winner);

      $uid = $userId->userId;

      $data = array();
      $res  = $this->db->selectEloLatestGamesByPlayerId($uid);
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
                  'name'     => $dataSet['name'],
                  'datetime' => $dataSet['time'],
                  'chicken'  => $chicken,
                  'sets'     => implode(", ", unserialize($dataSet['sets'])),
              );
          }
      }

      $smarty->assign(array(
        'data' => $data,
        'link' => $this->tools->linkTo(array('page' => 'eloRanking.php')),
      ));

      return $smarty->fetch('elo/widgethShowLatestGames.tpl');
  }

  private function getWinner($sets) {
    $a = 0;
    $b = 0;
    $sets = unserialize($sets);
    foreach($sets as $set) {
      $set = explode(":", $set);
      if ($set[0] > $set[1]) {
        $a++;
      } else {
        $b++;
      }
      if ($a == 2) {
        return true;
      } elseif ($b == 2) {
        return false;
      }
    }
  }

  private function insertGame($a, $b, $sets, $winner) {

      // INSERT MATCH
      $res = $this->db->insertEloMatch($a, $b, $sets, $winner);
      if ( $this->db->hasError()) {
          $this->setFailedMessage($this->brdb->getError());
          return false;
      }

      // Points
      $a1 = $this->getPointsByUserId($a);
      $b1 = $this->getPointsByUserId($b);

      // calc Points
      $points = $this->calcMatch($a1, $b1, $winner);

      // update points
      // player A
      $win  = ($winner == 1 ? 1 : 0);
      $this->updateEloPoints($a, $points[0], $win);

      // player B
      $win  = $win  == 1 ? 0 : 1;
      $this->updateEloPoints($b, $points[1], $win);

      if ($this->db->hasError() ) {
        $this->setFailedMessage($this->brdb->getError());
        return false;
      }

      $this->setSuccessMessage("Das Spiel wurde eingetragen.");
      $this->tools->customRedirect(array('page' => 'eloRanking.php'));
      return true;

  }

  private function getPointsByUserId($userId) {
      // GET POINTS
      $res = $this->db->selectEloPoints($userId);
      $row = $res->fetch_row();
      return  $row[0] == 0 ? 1000 : $row[0];
  }

  private function updateEloPoints($userId, $points, $win) {
      error_log ($userId ." - ". $points ." - ". $win ."<br>");
      $loss = ($win == 1 ? 0 : 1);
      $upd = $this->db->updateEloPoints($userId, $points, $win, $loss);
      if (! $this->db->hasError() ) {
          return true;
      }
      return false;
  }

  public function calcMatch($a, $b, $winnerA = true) {
      if ($winnerA == false) {
          return array_reverse($this->calcMatch($b, $a, true));
      }
      $pointDiff = $b - $a;
      $weak = 1 / (1 + pow(10, ($pointDiff/200)));

      // NEW POINTS
      $a1 = $this->getNewPoints($a, $winnerA, $weak);
      $b1 = $this->getNewPoints($b, !$winnerA, $weak);

      return array($a1, $b1);

  }

  private function getNewPoints($points, $win, $weak) {
    return (int) ($points + 15 * (($win == true ? 1 : 0) - $weak));
  }

  public function newRanking() {
    // clear Ranking
    $this->db->deleteEloRanking();

    // get all matches and recalc
    $res = $this->db->statementGetEloMatches();
    if ( $this->db->hasError() ) {
        $this->setFailedMessage("Da ist wohl etwas schief gegangen.");
        return false;
    }

    $games = 0;

    while($dataSet = $res->fetch_assoc()) {
      // SET PLAYERS
      $a = $dataSet['playerId'];
      $b = $dataSet['opponentId'];

      $a1 = $this->getPointsByUserId($a);
      $b1 = $this->getPointsByUserId($b);
      $winner = $this->getWinner($dataSet['sets']);

      // calc Points
      $points = $this->calcMatch($a1, $b1, $winner);

      // update points
      // player A
      $win  = ($winner == 1 ? 1 : 0);
      // @TODO: change timestamp if recalc
      $this->updateEloPoints($a, $points[0], $win);

      // player B
      $win  = $win  == 1 ? 0 : 1;
      $this->updateEloPoints($b, $points[1], $win);

      $games++;
    }

    $this->setSuccessMessage("Das Ranking wurde komplett neu erstellt.");
    return true;
  }

}
