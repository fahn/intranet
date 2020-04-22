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
require_once BASE_DIR ."/inc/logic/prgCup.inc.php";
require_once BASE_DIR ."/vendor/autoload.php";

class Cup extends BrdbHtmlPage
{
    private PrgPatternElementCup $prgElementCup;


    public function __construct()
    {
        parent::__construct();

        $this->prgElementCup = new PrgPatternElementCup($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementCup);
    }

    protected function htmlBody(): void
    {
        switch ($this->action)
        {
            default:
                $content = $this->TMPL_showList();
                break;
        }


        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }



    private function TMPL_showList(): string
    {
        /*
        $stats  = $this->getRankingGroupedByDate();
        $labels = implode(",", array_map(array($this, 'add_quotes'), $stats[0]));

        $this->smarty->assign(array(
            'ranking'     => $this->getRanking(),
            'games'       => $this->getGames(),
            'labels'      => $labels,
            'options'     => $stats[1],
            'print'       => $print,
            'stats'       => $this->prgElementRanking->getSettingBool('RANKING_STATS_ENABLE'),
        ));
        */

        $cupList = $this->brdb->getAllCups();
        $this->smarty->assign(array(
            'cups' => $cupList,
        ));

        return $this->smarty->fetch('cup/list.tpl');
    }
}