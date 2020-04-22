<?php
/*******************************************************************************
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
require_once "default.widget.php";

class TeamWidget extends Widget
{

    public function __construct()
    {
        parent::__construct();
    }

    public function showWidget(?string $name)
    {
        switch ($name)
        {
            case "showAdminsAndReporter":
                return $this->TMPL_AdminsAndReporter();
                break;

            case "showTeam":
                return $this->TMPL_ShowTeam();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }

    private function TMPL_AdminsAndReporter():string
    {
        $this->smarty->assign(array(
            "data" => $this->brdb->getStaffList(),
        ));

        return $this->smarty->fetch("team/widgetList.tpl");
    }

    private function TMPL_ShowTeam():string
    {
        $this->smarty->assign(array(
            "data" => $this->brdb->getStaffList(),
        ));

        return $this->smarty->fetch("team/widgetList.tpl");
    }

}
