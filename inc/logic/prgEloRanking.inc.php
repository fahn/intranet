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
    const FORM_DELETE_MATCH = "deleteEloMatch";

    /**
     * construct
     */
    public function __construct() {
        parent::__construct("eloRanking");
        // load DB
        $this->db = new BrankDB();

        // load Tools
        $this->tools = new Tools();

        $this->registerPostSessionVariable(self::FORM_FORM_ACTION);
        $this->registerPostSessionVariable(self::FORM_INSERT_MATCH);
        #die(print_r($_POST));
    }

    public function __loadPattern($prgElementLogin) {
        $this->prgElementLogin = $prgElementLogin;
    }

    /********************************************* POST ***********************/

    public function processPost() {

        #$isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        #$isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        #$isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

        #if ( ! $isUserLoggedIn || ! $isAdmin || ! $isReporter) {
        #    return;
        #}


        $form = strval(trim($this->getPostVariable(self::FORM_FORM_ACTION)));
        switch ($form) {
            case self::FORM_INSERT_MATCH:
                $this->insertMatch();
                break;
            case self::FORM_DELETE_MATCH:
                $this->deleteMatch();
                break;

            default:
                // code...
                break;
        }

    }



    private function insertMatch() {
        $player   = $this->getPostVariable('player');
        $opponent = $this->getPostVariable('opponent');
        die($player);
        // @TODO: Check if users exists
        $sets = array();
        for($i=1; $i<=3; $i++) {
            $fielda = sprintf('set%s%s', 'A', $i);
            $fieldb = sprintf('set%s%s', 'B', $i);
            if ($this->getPostVariable($fielda) && $this->getPostVariable($fieldb)) {
                $sets[] = implode(":", array($this->getPostVariable($fielda), $this->getPostVariable($fieldb)));
            }
        }


        if (! $player || ! $opponent || count($sets) < 2) {
            $this->setFailedMessage("Die Angaben stimmen nicht.");
            return false;
        }

        // INSERT MATCH
        $res = $this->db->insertEloMatch($player, $opponent, $sets, $winner);
        if ( $this->db->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return false;
        }

        // Points
        $a1 = $this->getPointsByUserId($player);
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

        $this->setSuccessMessage("Das Spiel wurde eingetragen und die Punkte wurden berechnet.");
        $this->tools->customRedirect(array('page' => 'eloRanking.php'));
        return true;

    }

    private function deleteMatch() {
        $id = $this->tools->get('id') ? $this->tools->get('id') : '';
        if (! $id) {


        }

        $this->db->deleteEloMatch($id);
        if ($this->db->hasError()) {
            $this->setFailedMessage("Das Spiel konnte nicht gelöscht werden");
            $this->tools->customRedirect(array('page' => 'eloRanking.php'));
        }
        if (! $this->newRanking() ) {
            $this->setFailedMessage("Die Rankgliste konnte nicht erstellt werden.");
            $this->tools->customRedirect(array('page' => 'eloRanking.php'));
        }

        $this->setSuccessMessage("Das Spiel wurde gelöscht & die Rangliste wurde neu erstellt.");
        $this->tools->customRedirect(array('page' => 'eloRanking.php'));
        return true;
    }

    /********************************************** GET ***********************/

    function processGet() {
        #$isUserLoggedIn = $this->prgElementLogin->isUserLoggedIn();
        #$isUserAdmin    = $this->prgElementLogin->getLoggedInUser()->isAdmin();
        #$isUserReporter = $this->prgElementLogin->getLoggedInUser()->isReporter();

        #if ( ! $isUserLoggedIn || ! $isAdmin || ! $isReporter) {
                        #return;
#        }


        #$form = strval(trim($this->getGetVariable('action')));
        $action = $this->tools->get('action');
        switch ($action) {
            case 'renewRanking':
                $this->getNewRanking();
                break;
            default:
                // code...
                break;
        }
    }

    private function getNewRanking() {
        if ( ! $this->newRanking()) {
            $this->setFailedMessage("Die Rankgliste konnte nicht erstellt werden.");
        } else {
            $this->setSuccessMessage("Die Rangliste wurde neu erstellt.");
        }
        $this->tools->customRedirect(array('page' => 'eloRanking.php'));
        return true;
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



  /**
   * get points
   */
  private function getPointsByUserId($userId) {
      // GET POINTS
      $res = $this->db->selectEloPoints($userId);
      $row = $res->fetch_row();
      return  $row[0] == 0 ? 1000 : $row[0];
  }

  /**
   * update Points from user
   */
  private function updateEloPoints($userId, $points, $win) {
      error_log ($userId ." - ". $points ." - ". $win ."<br>");
      $loss = ($win == 1 ? 0 : 1);
      $upd = $this->db->updateEloPoints($userId, $points, $win, $loss);
      if (! $this->db->hasError() ) {
          return true;
      }
      return false;
  }

  /**
   * calc Match
   */
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

  /**
   * getNewPoints
   */
   private function getNewPoints($points, $win, $weak) {
    return (int) ($points + 15 * (($win == true ? 1 : 0) - $weak));
  }

  /**
   * generate new Ranking
   */
  public function newRanking() {
      // clear Ranking
      $this->db->deleteEloRanking();

      // get all matches and recalc
      $res = $this->db->statementGetEloMatches();
      if ( $this->db->hasError() ) {
          return false;
      }

      $games = 0;
      echo "<pre>";
      while($dataSet = $res->fetch_assoc()) {

          print_r($dataSet);
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
      die("1");
      return true;
  }

}
