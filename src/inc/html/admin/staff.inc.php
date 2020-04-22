<?php
/**
 * Badminton Intranet System
 * Copyright 2017-2020
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * PHP versions 7
 *
 * @category  BadtraIntranet
 * @package   BadtraIntranet
 * @author    Stefan Metzner <stmetzner@gmail.com>
 * @copyright 2017-2020 Badtra
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      https://www.badtra.de
 ******************************************************************************/
require_once "brdbHtmlPage.inc.php";
require_once BASE_DIR ."/inc/logic/prgStaff.inc.php";

class BrdbHtmlAdminStaff extends BrdbHtmlPage
{
    private $prgPatternElementStaff;

    private string $_page;


    public function __construct($page = null)
    {
        parent::__construct();

        $this->prgPatternElementStaff = new PrgPatternElementStaff($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementStaff);

        $this->_page = $page != null ?: $page;
    }

    protected function showProtectedArea(): bool
    {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }


    public function htmlBody() {
        $content = "";
        // check if Admin
        if (!$this->prgPatternElementLogin->getLoggedInUser()->isAdmin())
        {
            #throw new BadtraException('No rights');
            return;
        }

        switch ($this->action) {
            case 'add':
                $content = $this->TMPL_updatePlayer('add');
                break;

            case 'edit':
                $content = $this->TMPL_updatePlayer('edit');
                break;
/*
            case 'delete':
                $content = $this->TMPL_deletePlayer();
                break;
*/
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
        ));

        return $this->smarty->fetch('staff/list.tpl');
    }

    private function TMPL_updatePlayer($laction) {
        $data = $laction == 'edit' ? $this->getStaffById($this->id) : array();
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
        return $this->brdb->getStaffList();
    }

    private function getStaffById(int $id) {
        return $id > 0 ? $this->brdb->selectGetStaffById($id) : array();

    }
}

