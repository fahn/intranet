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
        $this->smarty->assign(array(
            'data' => $this->brdb->selectGetStaff(),
        ));

        return $this->smarty->fetch('team/widgetList.tpl');
    }

    private function TMPL_ShowTeam() {
        $this->smarty->assign(array(
            'data' => $this->brdb->selectGetStaff(),
        ));

        return $this->smarty->fetch('team/widgetList.tpl');
    }

}
