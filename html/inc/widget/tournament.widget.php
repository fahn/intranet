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
require_once('default.widget.php');

class TournamentWidget extends Widget {
    private $linkToTournament;

    public function __construct() {
        parent::__construct();

        // set link
        $this->linkToTournament = $this->tools->linkTo(array(
            'page' => 'tournament.php',
        ));
    }

    public function showWidget($name) {
        switch ($name) {
            case 'latestTournaments':
                return $this->TPML_latestTournament();
                break;

            case 'upcomingTournaments':
                return $this->TPML_upcomgingTournament();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    private function TPML_latestTournament() {
        $data = $this->getLatestTournament();

        $this->smarty->assign('data', $data);

        return $this->smarty->fetch('tournament/widget_latest.tpl');
    }

    /**
     *  latest tournament
     */
    private function TPML_upcomgingTournament() {
        $data = $this->getUpcomingTournaments();

        $this->smarty->assign(array(
            'linkToTournament' => $this->linkToTournament,
            'data' => $data
        ));

        return $this->smarty->fetch('tournament/widget_upcomging.tpl');
    }



    private function getLatestTournament() {
        $data = array();
        $res = $this->brdb->selectLatestTournamentList(5);

        if (!$this->brdb->hasError()) {
            while ($dataSet = $res) {
                $dataSet['classification'] = $this->tools->formatClassification($dataSet['classification']);
                $dataSet['linkTo']         = $this->tools->linkTo(array('page' => 'tournament.php', 'action' => 'details', 'id' => $dataSet['tournamentId']));

                $data[]                    = $dataSet;
            }
        }
        return $data;
    }

    private function getUpcomingTournaments() {
        $data = array();
        $res = $this->brdb->selectUpcomingTournamentList(5);
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res) {
                $dataSet['classification'] = $this->tools->formatClassification($dataSet['classification']);
                $dataSet['linkTo']         = $this->tools->linkTo(array('page' => 'tournament.php', 'action' => 'details', 'id' => $dataSet['tournamentId']));
                // get unique player
                $players = $this->brdb->selectUpcomingTournamentPlayer($dataSet['tournamentId']);
                
                $dataSet['participant'] = $players->num_rows;

                $data[]                 = $dataSet;
            }
        }
        return $data;
    }


}
