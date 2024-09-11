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

use \Badtra\Intranet\Widget\DefaultWidget;

use Badtra\Intranet\Db\BrankDB;

class TournamentWidget extends DefaultWidget
{

    private string $_linkToTournament;

    protected $brdb = null;

    protected $smarty = null;

    private int $_MAX_TOURNAMENT = 5;


    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->brdb = new BrankDB();
        $this->smarty = new \Smarty();
        //parent::__construct();

        // set link
        // $this->_linkToTournament = $this->linkTo(
        //     ["page" => "tournament"]
        // );

    }//end __construct()


    /**
     * Template: get latests torunaments
     *
     * @return string
     */
    public function latestTournamentView(): string
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
    public function upcomingTournamentView(): string
    {
        $data = $this->getUpcomingTournaments();

        $this->smarty->assign([
                "data"              => $data,
                // "_linkToTournament" => $this->_linkToTournament,
                "linkToTournament" => "/tournament",
                
            ]);

        return $this->smarty->fetch("tournament/widget/upcoming.tpl");

    }//end upcomingView()


    /**
     * Summary of latest Torunament
     * @return array
     */
    private function getLatestTournament():array
    {
        $tournamtList = $this->brdb->selectLatestTournamentList($this->_MAX_TOURNAMENT);

        if (isset($tournamtList) && !empty($tournamtList)) {
            foreach ($tournamtList as $dataSet) {
                // $dataSet["classification"] = $this->formatClassification($dataSet["classification"]);
                // $dataSet["linkTo"]         = $this->linkTo(["page" => "tournament.php", "action" => "details", "id" => $dataSet["tournamentId"]]);

                $data[] = $dataSet;
            }
        }
        return $data;

    }//end getLatestTournament()


    /**
     * 
     * Get upcoming Tournaments
     * @return array
     */
    private function getUpcomingTournaments():array
    {
        $tournamtList = $this->brdb->selectUpcomingTournamentList($this->_MAX_TOURNAMENT);
      
        if (isset($tournamtList) && !empty($tournamtList)) {
            foreach ($tournamtList as $dataSet) {
                // $dataSet["classification"] = $this->formatClassification($dataSet["classification"]);
                // $dataSet["linkTo"]         = $this->linkTo(["page" => "tournament.php", "action" => "details", "id" => $dataSet["tournamentId"]]);
                // get unique player
                $players = $this->brdb->selectUpcomingTournamentPlayer($dataSet["tournamentId"]);
              
                // $dataSet["participant"] = $players->num_rows;

                $data[] = $dataSet;
            }
        }
        return $data;
        //unset($data, $dataSet, $tournamtList);

    }//end getUpcomingTournaments()
}//end class
