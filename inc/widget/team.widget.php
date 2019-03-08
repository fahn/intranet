<?php


require_once('default.widget.php');

class teamWidget extends Widget {

    public function __construct() {
        parent::__construct();
    }

    public function showWidget($name) {
        switch ($name) {
            case 'showTeam':
                return $this->TPML_showTeam();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    private function TPML_showTeam() {
        $data = $this->getAdminsAndReporter();

        $this->smarty->assign(array(
          'data' => $data,
        ));

        return $this->smarty->fetch('team/widgetList.tpl');
    }


    private function getAdminsAndReporter() {
        $data = array();
        $res = $this->brdb->GetActiveAndReporterOrAdminPlayer();
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $data[]         = array(
                    'userId'   => $dataSet['userId'],
                    'fullName' => $dataSet['firstName'] .' '. $dataSet['lastName'],
                );
            }
        }
        return $data;
    }
}
