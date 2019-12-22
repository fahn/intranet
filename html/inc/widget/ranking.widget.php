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

class RankingWidget extends Widget {
    private $userId;

    public function __construct($userId = null) {
        if (isset($userId) && is_numeric($userId)) {
            $this->userId = $userId;
        }
        parent::__construct();
    }

    public function showWidget($name) {
        switch ($name) {
            case 'latestGames':
                return $this->TPML_latestGames();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    private function TPML_latestGames() {
        $data = $this->getLatestGames();

        $this->smarty->assign(array(
          'data' => $data,
          'link' => $this->tools->linkTo(array('page' => 'ranking.php')),
        ));

        return $this->smarty->fetch('ranking/widgetShowLatestGames.tpl');
    }

    private function getLatestGames() {
        if ($this->userId == null) {
            return;
        }

        $data = array();
        $res  = $this->brdb->selectLatestRankingGamesByPlayerId($this->userId);
        if (! $this->brdb->hasError() ) {
            while ($dataSet = $res->fetch_assoc()) {
                // chicken
                if($this->userId == $dataSet['winnerId']) {
                    $chicken = '<i class="fas fa-arrow-circle-up text-success"></i>';
                } else {
                    $chicken = '<i class="fas fa-arrow-circle-down text-danger"></i>';
                }

                $dataSet['chicken'] = $chicken;
                $dataSet['sets']    = $this->convertSets($dataSet['sets']);

                $data[] = $dataSet;
            }

        }
        return $data;
    }

    private function convertSets($sets) {
        return implode(" - ", unserialize($sets));
    }
}
?>
