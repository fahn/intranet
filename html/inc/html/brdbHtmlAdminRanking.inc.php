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
include_once('brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgRanking.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminRanking extends BrdbHtmlPage 
{
    private $prgElementGame;

    public function __construct(): void
    {
        parent::__construct();
        $this->prgElementRanking = new PrgPatternElementRanking($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementRanking);

        // TOOLS
        $this->tools = new Tools();
    }

    public function processPage(): void
    {
        // Call all prgs and process them all
        parent::processPage();
    }

    protected function htmlBody(): void
    {
        $action = $this->tools->get("action");

        switch ($action) 
        {
            case 'add':
                $content = $this->TMPL_AddGame();
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

    private function TMPL_AddGame(): string
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
        $id   = $this->tools->get("id");
        $game = $this->brdb->getGameById($id);

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
        $playerList = $this->brdb->selectAllPlayerByOurClub($this->tools->getHomeClub());
        $data = array();

        if (isset($playerList) && !empty($playerList)) {
            foreach ($playerList as $dataSet) {
                $data[] 		= array(
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

?>
