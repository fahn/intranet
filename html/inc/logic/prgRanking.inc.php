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
include_once 'prgPattern.inc.php';

include_once BASE_DIR .'/inc/db/brdb.inc.php';
include_once BASE_DIR .'/inc/model/user.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class PrgPatternElementRanking extends APrgPatternElement {
    // DB
    private $db;

    // tools
    private $tools;

    protected $prgElementLogin;

    // generell
    const FORM_FORM_ACTION  = "formAction";

    // Forms
    const FORM_INSERT_MATCH = "insertMatch";
    const FORM_DELETE_MATCH = "deleteMatch";


    // Items in FORM
    const FORM_ITEM_PLAYER   = "player";
    const FORM_ITEM_OPPONENT = "opponent";
    const FORM_ITEM_GAMETIME = "gameTime";
    const FORM_ITEM_SET1     = "set1";
    const FORM_ITEM_SET2     = "set2";
    const FORM_ITEM_SET3     = "set3"; // optional


    /**
     * construct
     */
    public function __construct() {
        parent::__construct("ranking");
        // load DB
        $this->db = new BrankDB();

        // load Tools
        $this->tools = new Tools();

        $this->registerPostSessionVariable(self::FORM_FORM_ACTION);
        $this->registerPostSessionVariable(self::FORM_INSERT_MATCH);
    }

    public function __loadPattern($prgElementLogin) {
        $this->prgElementLogin = $prgElementLogin;
    }

    /********************************************* POST ***********************/

    public function processPost() {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        // ADMIN AREA
        $this->prgElementLogin->redirectUserIfnoRights(array('reporter', 'admin'), 'or');

        if (! $this->issetPostVariable(self::FORM_FORM_ACTION)) {
            $this->setFailedMessage("Kein Formular.");
            return;
        }


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
        if (! $this->issetPostVariable(self::FORM_ITEM_PLAYER) ||
            ! $this->issetPostVariable(self::FORM_ITEM_OPPONENT) ||
            ! $this->issetPostVariable(self::FORM_ITEM_GAMETIME) ||
            ! $this->issetPostVariable(self::FORM_ITEM_SET1) ||
            ! $this->issetPostVariable(self::FORM_ITEM_SET2)) {
              $this->setFailedMessage("Bitte alle Informationen angeben.");
        }

        $player   = strval(trim($this->getPostVariable(self::FORM_ITEM_PLAYER)));
        $opponent = strval(trim($this->getPostVariable(self::FORM_ITEM_OPPONENT)));
        $gameTime = strval(trim($this->getPostVariable(self::FORM_ITEM_GAMETIME)));

        $sets = array();
        try {
            $set1 = strval(trim(implode(":", $this->getPostVariable(self::FORM_ITEM_SET1))));
            $sets[] = $set1;

            $set2 = strval(trim(implode(":", $this->getPostVariable(self::FORM_ITEM_SET2))));
            $sets[] = $set2;
        } catch (Exception $e) {
            return false;
        }

        try {
            $set3Arr = $this->getPostVariable(self::FORM_ITEM_SET3);
            if (count($set3Arr) != 2 || empty($set3Arr[0]) || empty($set3Arr[1])) {
                throw new Exception("Value must be 1 or below");
            }
            $set3 = strval(trim(implode(":", $set3Arr)));
            $sets[] = $set3;
        } catch (Exception $e) {
            $set3 = "";
        }
        // serialize sets
        $sets = serialize($sets);

        // set winner
        $winner = $this->getWinner($sets);

        // date to timestamp
        $gameTime = strtotime($gameTime);

        // INSERT MATCH
        $this->db->insertMatch($player, $opponent, $sets, $winner, $gameTime);
        if ( $this->db->hasError()) {
            $this->setFailedMessage($this->brdb->getError());
            return false;
        }

        // Points
        $a1 = $this->getPointsByUserId($player);
        $b1 = $this->getPointsByUserId($opponent);

        // calc Points
        $points = $this->calcMatch($a1, $b1, $winner);

        // update points
        // player A
        $win  = ($winner == 1 ? 1 : 0);
        $this->updatePoints($player, $points[0], $win);

        // player B
        $win  = $win  == 1 ? 0 : 1;
        $this->updatePoints($opponent, $points[1], $win);

        if ($this->db->hasError() ) {
          $this->setFailedMessage($this->brdb->getError());
          return false;
        }

        $this->setSuccessMessage("Das Spiel wurde eingetragen und die Punkte wurden berechnet.");
        $this->tools->customRedirect(array('page' => 'ranking.php'));
        return true;

    }

    private function deleteMatch() {
        $id = strval(trim($this->getGetVariable('id')));
        if (! $id) {
            return;
        }


        $this->db->deleteMatch($id);
        if ($this->db->hasError()) {
            $this->setFailedMessage("Das Spiel konnte nicht gelöscht werden");
            $this->tools->customRedirect(array('page' => 'ranking.php'));
        }
        if (! $this->newRanking() ) {
            $this->setFailedMessage("Die Rankgliste konnte nicht erstellt werden.");
            $this->tools->customRedirect(array('page' => 'ranking.php'));
        }

        $this->setSuccessMessage("Das Spiel wurde gelöscht & die Rangliste wurde neu erstellt.");
        $this->tools->customRedirect(array('page' => 'ranking.php'));
        return true;
    }

    /********************************************** GET ***********************/
    function processGet() {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();



        $action = $this->tools->get('action');
        switch ($action) {
            case 'renewRanking':
                // ADMIN AREA
                $this->prgElementLogin->redirectUserIfnoRights(array('reporter', 'admin'), 'or');
                
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
        $this->tools->customRedirect(array('page' => 'ranking.php'));
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
    private function getPointsByUserId($playerId) {
        // GET POINTS
        try {
            $res = $this->db->selectPoints($playerId);
            $row = $res->fetch_row();
            return  $row[0] == 0 ? 1000 : $row[0];
        } catch(Exception $e) {
            return 1000;
        }
    }

  /**
   * update Points from user
   */
  private function updatePoints($playerId, $points, $win) {
      error_log ($playerId ." - ". $points ." - ". $win ."<br>");
      $loss = ($win == 1 ? 0 : 1);
      $upd = $this->db->updatePoints($playerId, $points, $win, $loss);
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
      $this->db->deleteRanking();

      // get all matches and recalc
      $res = $this->db->statementGetMatches();
      if ( $this->db->hasError() ) {
          return false;
      }

      $games = 0;
      while($dataSet = $res->fetch_assoc()) {

          #print_r($dataSet);
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
          $this->updatePoints($a, $points[0], $win);

          // player B
          $win  = $win  == 1 ? 0 : 1;
          $this->updatePoints($b, $points[1], $win);

          $games++;
      }
      return true;
  }

}
