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
require_once "default.widget.php";

class RankingWidget extends Widget
{
    private int $userId;

    public function __construct(?int $userId = null) {
        $this->userId = isset($userId) && is_numeric($userId) ?: $userId;

        parent::__construct();
    }

    public function showWidget(?string $name) {
        switch ($name) {
            case "latestGames":
                return $this->TPML_latestGames();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    private function TPML_latestGames():string
    {
        $data = $this->getLatestGames();

        $this->smarty->assign(array(
          "data" => $data,
          //"link" => $this->linkTo(array("page" => "ranking.php")),
        ));

        return $this->smarty->fetch("ranking/widgetShowLatestGames.tpl");
    }

    private function getLatestGames(): ?array
    {
        if ($this->userId == null) {
            return array();
        }

        $data = array();
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

                $data[] = $dataSet;
            }
        } catch (Exception $e) {
           
        }

        return $data;
    }

    private function convertSets(string $sets):string
    {
        return implode(" - ", unserialize($sets));
    }
}

