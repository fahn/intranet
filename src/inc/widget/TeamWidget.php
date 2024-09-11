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

use \Badtra\Intranet\Widget\DefaultWidget;
use Badtra\Intranet\DB\BrankDB;
use \Smarty;

class TeamWidget extends DefaultWidget
{


    public function __construct(Smarty $smarty, BrankDB $brdb)
    {
        parent::__construct($smarty, $brdb);
    }//end __construct()


    /**
     * Show Team Widget
     * @param mixed $name
     * @return string
     */
    public function showWidget(?string $name)
    {
        switch ($name) {
            case "showAdminsAndReporter":
                return $this->adminView();

            case "showTeam":
                return $this->teamView();

            default:
                return "no name / or not exists";
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
        $this->smarty->assign([
            "data" => $this->brdb->getStaffList(),
        ]);

        return $this->smarty->fetch("team/widget/list.tpl");
    }//end teamView()
}//end class
