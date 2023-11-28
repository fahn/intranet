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
namespace Badtra\Intranet\Html;

use \Badtra\Intranet\Html\BrdbHtmlPage;

class Staff extends BrdbHtmlPage
{


    public function __construct()
    {
        parent::__construct();
    }//end __construct()

    public function listView(): string
    {
        $this->smarty->assign(
            [
                "row" => $this->getTeam(),
            ]
        );

        return $this->smartyFetchWrap("team/list.tpl");
    }//end TMPL_showTeam()


    private function getTeam(): array
    {
        $teamList = $this->brdb->getStaffList();
        $data     = [];

        if (isset($teamList) && !empty($teamList)) {
            foreach ($teamList as $dataSet) {
                if (isset($dataSet["row"]) && $dataSet["row"] > 0) {
                    $data[$dataSet["row"]][] = $dataSet;
                }
            }
        }

        return $data;
        unset($data, $dataSet, $teamList);
    }//end getTeam()
}//end class
