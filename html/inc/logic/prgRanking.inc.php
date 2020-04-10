<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
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

class PrgPatternElementRanking extends APrgPatternElement 
{
    // const
    const __TABLE__ = "Ranking";

    protected PrgPatternElementLogin $prgElementLogin;

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
    public function __construct(PrgPatternElementLogin $prgElementLogin) 
    {
        parent::__construct("ranking");

        $this->prgElementLogin = $prgElementLogin;

        $this->registerPostSessionVariable(self::FORM_FORM_ACTION);
        $this->registerPostSessionVariable(self::FORM_INSERT_MATCH);
    }

    public function __loadPattern(): void
    {
        
    }

    /********************************************* POST ***********************/

    public function processPost(): void
    {
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



    private function insertMatch(): bool
    {
        $requireFields = array(self::FORM_ITEM_PLAYER, self::FORM_ITEM_OPPONENT, self::FORM_ITEM_GAMETIME, self::FORM_ITEM_SET1, self::FORM_ITEM_SET2);
        if (! $this->prgElementLogin->checkRequiredFields($requireFields)) 
        {
            $this->setFailedMessage("Bitte alle Informationen angeben.");
            return false;
        }

        $player   = $this->getPostVariableString(self::FORM_ITEM_PLAYER);
        $opponent = $this->getPostVariableString(self::FORM_ITEM_OPPONENT);
        $gameTime = $this->getPostVariableString(self::FORM_ITEM_GAMETIME);

        $sets = array();
        try {
            $set1 = strval(trim(implode(":", $this->getPostVariableArray(self::FORM_ITEM_SET1))));
            $sets[] = $set1;

            $set2 = strval(trim(implode(":", $this->getPostVariableArray(self::FORM_ITEM_SET2))));
            $sets[] = $set2;
        
            $set3Arr = $this->getPostVariable(self::FORM_ITEM_SET3);
            if (count($set3Arr) != 2 || empty($set3Arr[0]) || empty($set3Arr[1])) {
                throw new Exception("Value must be 1 or below");
            }
            $set3 = strval(trim(implode(":", $set3Arr)));
            $sets[] = $set3;
        
            // serialize sets
            $sets = serialize($sets);

            // set winner
            $winner = $this->getWinner($sets);

            // date to timestamp
            $gameTime = strtotime($gameTime);

            // INSERT MATCH
            if (!$this->db->insertMatch($player, $opponent, $sets, $winner, $gameTime)) {
                throw new Exception("Das Spiel konnte nicht hingefügt werden.");
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
            if (!$this->updatePoints($opponent, $points[1], $win)) {
                throw new Exception(sprintf("Punkte konnten von %s nicht aktualisiert werden", $opponent));
            }


            $this->setSuccessMessage("Das Spiel wurde eingetragen und die Punkte wurden berechnet.");
            $this->customRedirectArray(array('page' => 'ranking.php'));
            return true;

        } catch (Exception $e) {
            $this->log($this->__TABLE__, sprintf("Cannot insert Match. Details: %s", $e->getMessage()), "", "POST", "");
            $this->setFailedMessage($e->getMessage());
            return false;
        }

    }

    private function deleteMatch(): bool
    {
        try {

            $id = intval(trim($this->getGetVariable('id')));
            if (! $id) {
                throw new Exception("Cannot identify id %s". strval($id));
            }
            
            if (!$this->db->deleteMatch($id)) 
            {
                throw new Exception("Cannot delete Event");
            }
        } 
        catch (Exception $e) 
        {
            // LOG EVENT
            $this->log($this->__TABLE__, sprintf("Cannot delete Match. ID: %i. Details %s", $id, $e->getMessage()), "", "GET", "");

            $this->setFailedMessage("Das Spiel konnte nicht gelöscht werden");
            $this->customRedirectArray(array('page' => 'ranking.php'));
            return false;
        }
        

        try {
            if (! $this->newRanking())  {
                throw new Exception("Cannot renew Ranking");
            }

        }
        catch (Exception $e) 
        {
            $this->log($this->__TABLE__, sprintf("Cannot delete Match. ID: %i. Details %s", $id, $e->getMessage()), "", "GET", "");

            $this->setFailedMessage("Die Rankgliste konnte nicht erstellt werden.");
            $this->customRedirectArray(array('page' => 'ranking.php'));
            return false;

        }
        
        $this->setSuccessMessage("Das Spiel wurde gelöscht & die Rangliste wurde neu erstellt.");
        $this->customRedirectArray(array('page' => 'ranking.php'));

        return true;
    }

    /********************************************** GET ***********************/
    function processGet(): void 
    {
        $this->prgElementLogin->redirectUserIfNotLoggindIn();

        $action = $this->getGetVariable('action');
        switch ($action) 
        {
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

    private function getNewRanking(): bool
    {
        if (! $this->newRanking()) 
        {
            $this->setFailedMessage("Die Rankgliste konnte nicht erstellt werden.");
        } 
        else 
        {
            $this->setSuccessMessage("Die Rangliste wurde neu erstellt.");
        }
        $this->customRedirectArray(array('page' => 'ranking.php'));
        return true;
    }

    private function getWinner(string $sets): bool
    {
        $a = 0;
        $b = 0;
        $sets = unserialize($sets);
        foreach ($sets as $set) 
        {
            $set = explode(":", $set);
            if ($set[0] > $set[1]) 
            {
                $a++;
            } 
            else 
            {
                $b++;
            }
        }

        return $a == 2 ? true : false;
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
     * Update Points from User
     *
     * @param integer $playerId
     * @param integer $points
     * @param integer $win
     * @return boolean
     */
    private function updatePoints(int $playerId, int $points, int $win): bool
    {

        $loss = ($win == 1 ? 0 : 1);
        $this->db->updatePoints($playerId, $points, $win, $loss);

        if (! $this->db->hasError() ) {
            return true;
        }

        return false;
    }

    /**
     * Calc match
     *
     * @param int $a
     * @param int $b
     * @param boolean $winnerA
     * @return void
     */
    public function calcMatch(int $userA, int $userB, bool $winnerA = true): array
    {
        if ($winnerA == false) {
            return array_reverse($this->calcMatch($userB, $userA, true));
        }
        $pointDiff = $userB - $userA;
        $weak = 1 / (1 + pow(10, ($pointDiff/200)));

        // NEW POINTS
        $a1 = $this->getNewPoints($userA, $winnerA, $weak);
        $b1 = $this->getNewPoints($userB, !$winnerA, $weak);

        return array($a1, $b1);
        unset($userB, $userA);

    }

    /**
     * get new Points
     *
     * @param integer $points
     * @param boolean $win
     * @param integer $weak
     * @return integer
     */
    private function getNewPoints(int $points, bool $win, int $weak): int 
    {
        return (int) ($points + 15 * (($win == true ? 1 : 0) - $weak));
    }

    /**
     * generate new Ranking
     */
    public function newRanking(): bool
    {
        // clear Ranking
        $this->db->truncateRanking();

        // get all matches and recalc
        $gameList = $this->db->getMatches();
        if (!isset($gameList) || empty($gameList) ) {
            return false;
        }

        $games = 0;
        foreach ($gameList as $dataSet) {
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
?>