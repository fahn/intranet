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
$path=dirname(dirname(__FILE__));
require($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgStaff.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminStaff extends BrdbHtmlPage {
    private $prgPatternElementStaff;
    private $countRows;

    private $page;


    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
            $this->page = $page;
        }

        $this->prgPatternElementStaff = new PrgPatternElementStaff($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementStaff);

    }

    protected function showProtectedArea() {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }


    public function htmlBody() {
        $content = "";
        // check if Admin
        if(!$this->prgPatternElementLogin->getLoggedInUser()->isAdmin()) {
            #throw new BadtraException('No rights');
            return;
        }

        $action = $this->tools->get('action');
        switch ($action) {
            case 'add':
                $content = $this->TMPL_updatePlayer('add');
                break;

            case 'edit':
                $content = $this->TMPL_updatePlayer('edit');
                break;

            case 'delete':
                $content = $this->TMPL_deletePlayer();
                break;

            default:
                $content = $this->TMPL_listStaff();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }

    private function TMPL_listStaff() {
        $this->smarty->assign(array(
            'staff'     => $this->loadStaffList(),
            'error'      => $this->brdb->getError(),
        ));

        return $this->smarty->fetch('staff/list.tpl');
    }

    private function TMPL_updatePlayer($laction) {
        $data = $laction == 'edit' ? $this->getStaffById() : array();
        $this->smarty->assign(array(
            'rowOption' => array('1' => 'Reihe 1', '2' => 'Reihe 2', '3' => 'Reihe 3'),
            'colOption' => array('1' => 'Spalte 1', '2' => 'Spalte 2', '3' => 'Spalte 3'),
            'data'      => $data,
            'laction'   => $laction,
        ));

        return $this->smarty->fetch('staff/update.tpl');
    }

    /**************************************************************************/


    private function loadStaffList() {
        $res = $this->brdb->selectGetStaff();
        $data = array();
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $data[] = $dataSet;
            }
        }
        return $data;
    }

    private function getStaffById() {
        $id = Tools::get('id');
        $res = $this->brdb->selectGetStaffById($id);
        if ($this->brdb->hasError()) {
            throw new BadtraException('Staff not exists');
        }
        return $res->fetch_assoc();
        /*while($dataRow = $res->fetch_assoc()) {

            print_r($dataRow);
        }
        return "1";*/
    }

}
?>
