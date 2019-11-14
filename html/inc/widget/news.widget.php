<?php

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

    }

    private function TPML_latestNews() {
        $this->smarty->assign('data', $this->getLatestNews());

        return $this->smarty->fetch('news/widgetLatest.tpl');
    }

    private function getLatestNews() {
        $data = array();
        $res = $this->brdb->selectLatestNews(5);
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $dataSet['linkTo']         = $this->tools->linkTo(array('page' => 'news.php', 'action' => 'details', 'id' => $dataSet['tournamentId']));

                $data[]                    = $dataSet;
            }
        }
        return $data;
    }
}
