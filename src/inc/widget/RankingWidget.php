<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
namespace Badtra\Intranet\Widget;

use Badtra\Intranet\DB\BrankDB;

use \Smarty;

class RankingWidget 
{
    private int $userId;

    protected $brdb = null;

    protected $smarty = null;

    private int $_MAX_TOURNAMENT = 5;

    public function __construct() {
        //$this->userId = isset($userId) && is_numeric($userId) ?: $userId;
        // @TODO FIX
        $this->userId = 1;
        $this->brdb = new BrankDB();
        $this->smarty = new Smarty();

    }
    public function showWidget(?string $name) {
        switch ($name) {
            case "latestGames":
                return $this->TPML_latestGames();

            default:
                return "no name / or not exists";
        }

    }

    private function TPML_latestGames():string
    {
        $data = $this->getLatestGames();

        $this->smarty->assign(array(
          "data" => $data,
          //"link" => $this->linkTo(array("page" => "ranking.php")),
        ));

        return $this->smarty->fetch("ranking/widget/latestGames.tpl");
    }

    private function getLatestGames(): ?array
    {

        if ($this->userId == null) {
            return array();
        }

        $res  = $this->brdb->selectLatestRankingGamesByPlayerId($this->userId);
        try {
            while ($dataSet = $res)
            {
                // chicken
                if ($this->userId == $dataSet["winnerId"]) {
                    $chicken = "<i class=\"fas fa-arrow-circle-up text-success\"></i>";
                } else {
                    $chicken = "<i class=\"fas fa-arrow-circle-down text-danger\"></i>";
                }

                $dataSet["chicken"] = $chicken;
                $dataSet["sets"]    = $this->convertSets($dataSet["sets"]);

                return $dataSet;
            }
        } catch (\Exception $e) {
           
        }

        return array();
        
    }

    private function convertSets(string $sets):string
    {
        return implode(" - ", unserialize($sets));
    }
}

