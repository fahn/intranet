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

class NewsWidget extends Widget {

    public function __construct() {
        parent::__construct();
    }

    public function showWidget($name) {
        switch ($name) {
            case 'latestNews':
                return $this->TPML_latestNews();
                break;

            default:
                return "no name / or not exists";
                break;
        }

        return;
        unset($name);

    }

    private function TPML_latestNews() {
        $this->smarty->assign('data', $this->getLatestNews());

        return $this->smarty->fetch('news/widgetLatest.tpl');
    }

    private function getLatestNews() {
        $tmp = array();
        $newsList = $this->brdb->selectLatestNews(5);
        if (isset($newsList) && !empty($newsList)) {
            foreach ($newsList as $dataSet) {
                $dataSet['linkTo']         = $this->tools->linkTo(array('page' => 'news.php', 'action' => 'details', 'id' => $dataSet['tournamentId']));

                $tmp[]                    = $dataSet;
            }
        }

        return $tmp;
        unset($newsList, $tmp, $dataSet);
    }
}
