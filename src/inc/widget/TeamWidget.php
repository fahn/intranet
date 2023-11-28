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
namespace Badtra\Intranet\Widget;

class TeamWidget extends \Badtra\Intranet\Widget\DefaultWidget
{


    public function __construct()
    {
        parent::__construct();
    }//end __construct()


    public function showWidget(?string $name)
    {
        switch ($name) {
            case "showAdminsAndReporter":
                return $this->adminView();
                break;

            case "showTeam":
                return $this->teamView();
                break;

            default:
                return "no name / or not exists";
                break;
        }

    }//end showWidget()


    private function adminView():string
    {
        $this->smarty->assign(
            [
                "data" => $this->brdb->getAdminAndReporter(),
            ]
        );

        return $this->smarty->fetch("team/widget/list.tpl");
    }//end adminView()


    private function teamView():string
    {
        $this->smarty->assign(
            [
                "data" => $this->brdb->getStaffList(),
            ]
        );

        return $this->smarty->fetch("team/widget/list.tpl");
    }//end teamView()
}//end class
