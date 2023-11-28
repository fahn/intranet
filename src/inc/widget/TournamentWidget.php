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

class TournamentWidget extends \Badtra\Intranet\Widget\DefaultWidget
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
            ["page" => "tournament.php"]
        );

    }//end __construct()


    /**
     * Decider which template has to shown up
     *
     * @param  string|null $name
     * @return string
     */
    public function showWidget(?string $name):string
    {
        switch ($name) {
        case "latestTournaments":
                return $this->latestView();
                break;

        case "upcomingTournaments":
                return $this->upcomingView();
                break;

        default:
                return "no name / or not exists";
                break;
        }
    }//end showWidget()


    /**
     * Template: get latests torunaments
     *
     * @return string
     */
    private function latestView(): string
    {
        $data = $this->getLatestTournament();

        $this->smarty->assign("data", $data);

        return $this->smarty->fetch("tournament/widget/latest.tpl");

    }//end latestView()


    /**
     * Template: get upcomging Tournaments
     *
     * @return string
     */
    private function upcomingView(): string
    {
        $data = $this->getUpcomingTournaments();

        $this->smarty->assign(
            [
                "_linkToTournament" => $this->_linkToTournament,
                "data"              => $data,
            ]
        );

        return $this->smarty->fetch("tournament/widget/upcoming.tpl");

    }//end upcomingView()


    /**
     * GEt latest Tournaments
     *
     * @return array
     */
    private function getLatestTournament():array
    {
        $data         = [];
        $tournamtList = $this->brdb->selectLatestTournamentList(5);

        if (isset($tournamtList) && !empty($tournamtList)) {
            foreach ($tournamtList as $dataSet) {
                $dataSet["classification"] = $this->formatClassification($dataSet["classification"]);
                $dataSet["linkTo"]         = $this->linkTo(["page" => "tournament.php", "action" => "details", "id" => $dataSet["tournamentId"]]);

                $data[] = $dataSet;
            }
        }
        return $data;
        unset($data, $dataSet, $tournamtList);

    }//end getLatestTournament()


    /**
     * Undocumented function
     *
     * @return array
     */
    private function getUpcomingTournaments():array
    {
        $data         = [];
        $tournamtList = $this->brdb->selectUpcomingTournamentList(5);
      
        if (isset($tournamtList) && !empty($tournamtList)) {
            foreach ($tournamtList as $dataSet) {
                $dataSet["classification"] = $this->formatClassification($dataSet["classification"]);
                $dataSet["linkTo"]         = $this->linkTo(["page" => "tournament.php", "action" => "details", "id" => $dataSet["tournamentId"]]);
                // get unique player
                $players = $this->brdb->selectUpcomingTournamentPlayer($dataSet["tournamentId"]);
              
                $dataSet["participant"] = $players->num_rows;

                $data[] = $dataSet;
            }
        }
        return $data;
        unset($data, $dataSet, $tournamtList);

    }//end getUpcomingTournaments()
}//end class
