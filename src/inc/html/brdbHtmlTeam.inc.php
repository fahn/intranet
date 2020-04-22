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
require_once BASE_DIR ."/inc/logic/prgPattern.inc.php";

class BrdbHtmlTeam extends BrdbHtmlPage
{


    public function __construct()
    {
        parent::__construct();
    }

    protected function htmlBody(): void
    {
        $content = $this->TMPL_showTeam();

        $this->smarty->assign(array(
            "content" => $content,
        ));

        $this->smarty->display("index.tpl");
        unset($content);
    }

    private function TMPL_showTeam(): string
    {
        $this->smarty->assign(array(
            "row"    => $this->getTeam(),
        ));

        return $this->smarty->fetch("team/list.tpl");
    }

    private function getTeam(): array
    {
        $teamList = $this->brdb->getStaffList();
        $data = array();

        if (isset($teamList) && !empty($teamList))
        {
            foreach ($teamList as $dataSet)
            {
                if (isset($dataSet["row"]) && $dataSet["row"] > 0)
                {
                    $data[$dataSet["row"]][] = $dataSet;
                }
            }
        }

        return $data;
        unset($data, $dataSet, $teamList);
    }

}

