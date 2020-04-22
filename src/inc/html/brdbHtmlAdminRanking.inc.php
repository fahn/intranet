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
require_once BASE_DIR .'/inc/logic/prgRanking.inc.php';

class BrdbHtmlAdminRanking extends BrdbHtmlPage
{
    private PrgPatternElementRanking $prgElementRanking;

    public function __construct()
    {
        parent::__construct();

        $this->prgElementRanking = new PrgPatternElementRanking($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementRanking);
    }
    protected function htmlBody(): void
    {

        switch ($this->action)
        {
            case 'add':
                $content = $this->_TMPL_AddGame();
                break;

            case 'edit':
                $content = $this->TMPL_EditGame();
                break;

            case 'delete':
                $content = $this->TMPL_deleteGame();

            default:
                $content = $this->TMPL_ListGames();
                break;
        }

        $this->smarty->assign(array(
        'content' => $content,
        ));
   
        $this->smarty->display('index.tpl');
    }

    private function _TMPL_AddGame(): string
    {
        $this->smarty->assign(array(
            'players'  => $this->getAllPlayerDataList(),
            'reporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
            'game'     => array(
                'datetime' => date("d.m.Y H:I"),
                ),
        ));

        return $this->smarty->fetch('ranking/updateGame.tpl');
    }




    private function TMPL_EditGame(): string
    {
        $game = $this->brdb->getGameById($this->id);

        $game['set1'] = $game['setA1'] .":". $game['setB1'];
        $game['set2'] = $game['setA2'] .":". $game['setB2'];
        if (isset($game['setA3']) && $game['setA3'] > 0 && isset($game['setB3']) && $game['setB3'] > 0) {
            $game['set3'] = $game['setA3'] .":". $game['setB3'];
        }

        $this->smarty->assign(array(
            'players'  => $this->getAllPlayerDataList(),
            'reporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
            'game'     => $game,
            'action'   => 'update',
        ));

        return $this->smarty->fetch('ranking/updateGame.tpl');
    }


    private function TMPL_ListGames(): string
    {
        $this->smarty->assign(array(
            'games'      => $this->getRankedGames(),
            'error'      => $this->brdb->getError(),
            'isReporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
        ));

        return $this->smarty->fetch('ranking/list.tpl');
    }

    private function TMPL_DeleteGame(): string
    {
        $this->smarty->assign(array(
            'games'      => $this->getRankedGames(),
            'error'      => $this->brdb->getError(),
            'isReporter' => $this->prgPatternElementLogin->getLoggedInUser()->isReporter(),
        ));

        return $this->smarty->fetch('ranking/list.tpl');
    }

    private function getAllPlayerDataList(): array
    {
        // @todo update this to backend Setting
        $playerList = $this->brdb->selectAllPlayerByOurClub(2);
        $data = array();

        if (isset($playerList) && !empty($playerList)) {
            foreach ($playerList as $dataSet) {
                $data[] = array(
                    'userId'   => $dataSet['userId'],
                    'fullName' => $dataSet['fullName'],
                );
            }
        }

        return $data;
    }

    /**
        * Get all ranked Games
    */
    private function getRankedGames(): array
    {
        return $this->brdb->getMatches();
    }
}


