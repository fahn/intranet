<?php

require_once('default.widget.php');


class tournamentWidget extends Widget {

    public function __construct() {
        parent::__construct();
    }

    public function showWidget($name) {
        switch ($name) {
            case 'latestTournament':
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

        $this->smarty->assign('data', $data);

        return $this->smarty->fetch('tournament/widget_upcomging.tpl');
    }



    private function getLatestTournament() {
        $data = array();
        $res = $this->brdb->selectLatestTournamentList(5);
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $dataSet['classification'] = $this->tools->formatClassification($dataSet['classification']);
                $data[]                    = $dataSet;
            }
        }
        return $data;
    }

    private function getUpcomingTournaments() {
        $data = array();
        $res = $this->brdb->selectUpcomingTournamentList(5);
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $dataSet['classification'] = $this->tools->formatClassification($dataSet['classification']);
                $data[]                     = $dataSet;
            }
        }
        return $data;
    }


}
