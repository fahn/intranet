<?php


require_once('default.widget.php');

class TeamWidget extends Widget {

    public function __construct() {
        parent::__construct();
    }

    public function showWidget($name) {
        switch ($name) {
            case 'showAdminsAndReporter':
                return $this->TMPL_AdminsAndReporter();
                break;

            case 'showTeam':
                return $this->TMPL_ShowTeam();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    private function TMPL_AdminsAndReporter() {
        $data = $this->getAdminsAndReporter();

        $this->smarty->assign(array(
          'data' => $data,
        ));

        return $this->smarty->fetch('team/widgetList.tpl');
    }


    private function getAdminsAndReporter() {
        $data = array();
        $res = $this->brdb->selectGetStaff();
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


    private function TMPL_ShowTeam() {
        $data = $this->getTeam();

        $this->smarty->assign(array(
          'data' => $data,
        ));

        return $this->smarty->fetch('team/widgetList.tpl');
    }

    private function getTeam() {
        $data = array();
        $res = $this->brdb->selectGetStaff();
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $data[] = $dataSet;
            }
        }
        return $data;
    }


}
