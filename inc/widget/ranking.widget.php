<?php


require_once('default.widget.php');

class RankingWidget extends Widget {

    public function __construct() {
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

    private function getLatestGames($userId = null) {
        if ($userId == null) {
            $uid = 1;
        } else {
            $uid = $userId->userId;
        }

        $data = array();
        $res  = $this->brdb->selectLatestRankingGamesByPlayerId($uid);
        if (! $this->brdb->hasError() ) {
            while ($dataSet = $res->fetch_assoc()) {
                // chicken
                if($uid == $dataSet['winner']) {
                    $chicken = '<i class="fas fa-arrow-circle-up text-success"></i>';
                } else {
                    $chicken = '<i class="fas fa-arrow-circle-down text-danger"></i>';
                }

                $dataSet['chicken'] = $chicken;
                $dataSet['sets']    = $this->convertSets($dataSet['sets']);

                $data[] = $dataSet;
                #die(print_r($data));
            }

        }
        return $data;
    }

    private function convertSets($sets) {
        return implode(" - ", unserialize($sets));
    }
}
?>
