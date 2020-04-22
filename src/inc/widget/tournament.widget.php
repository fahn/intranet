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


class TournamentWidget extends Widget
{
    private string $_linkToTournament;

    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();

        // set link
        $this->_linkToTournament = $this->linkTo(
            array(
                "page" => "tournament.php",
            )
        );
    }

    /**
     * Decider which template has to shown up
     *
     * @param string|null $name
     * @return string
     */
    public function showWidget(?string $name):string
    {
        switch ($name)
        {
            case "latestTournaments":
                return $this->TMPL_LatestTournament();
                break;

            case "upcomingTournaments":
                return $this->TMPL_UpcomgingTournament();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    /**
     * Template: get latests torunaments
     *
     * @return string
     */
    private function TMPL_LatestTournament(): string
    {
        $data = $this->getLatestTournament();

        $this->smarty->assign("data", $data);

        return $this->smarty->fetch("tournament/widget_latest.tpl");
    }


    /**
     * Template: get upcomging Tournaments
     *
     * @return string
     */
    private function TMPL_UpcomgingTournament(): string
    {
        $data = $this->getUpcomingTournaments();

        $this->smarty->assign(
            array(
                "_linkToTournament" => $this->_linkToTournament,
                "data" => $data
            )
        );

        return $this->smarty->fetch("tournament/widget_upcomging.tpl");
    }


    /**
     * GEt latest Tournaments
     *
     * @return array
     */
    private function getLatestTournament():array
    {
        $data = array();
        $tournamtList = $this->brdb->selectLatestTournamentList(5);

        if (isset($tournamtList) && !empty($tournamtList))
        {
            foreach ($tournamtList as $dataSet)
            {
                $dataSet["classification"] = $this->formatClassification($dataSet["classification"]);
                $dataSet["linkTo"]         = $this->linkTo(array("page" => "tournament.php", "action" => "details", "id" => $dataSet["tournamentId"]));

                $data[]                    = $dataSet;
            }
        }
        return $data;
        unset($data, $dataSet, $tournamtList);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    private function getUpcomingTournaments():array
    {
        $data = array();
        $tournamtList = $this->brdb->selectUpcomingTournamentList(5);
      
        if (isset($tournamtList) && !empty($tournamtList))
        {
            foreach ($tournamtList as $dataSet)
            {
                $dataSet["classification"] = $this->formatClassification($dataSet["classification"]);
                $dataSet["linkTo"]         = $this->linkTo(array("page" => "tournament.php", "action" => "details", "id" => $dataSet["tournamentId"]));
                // get unique player
                $players = $this->brdb->selectUpcomingTournamentPlayer($dataSet["tournamentId"]);
              
                $dataSet["participant"] = $players->num_rows;

                $data[]                 = $dataSet;
            }
        }
        return $data;
        unset($data, $dataSet, $tournamtList);
    }
}
