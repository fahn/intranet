<?php
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2020
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

// load logic
include_once BASE_DIR .'/inc/logic/prgUser.inc.php';


class BrdbHtmlUserInformation extends BrdbHtmlPage 
{
    private $prgElementUser;

    public function __construct() 
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }

    protected function htmlBody(): void
    {
        $content  = $this->loadContent();

        // widget
        $widgetTournament = new TournamentWidget();
        $widgetRanking    = new RankingWidget();

        $this->smarty->assign(array(
            'content'              => $content,
            'latestTournament'     => $widgetTournament->showWidget('latestTournaments'),
            'latestGamesInRanking' => $widgetRanking->showWidget('latestGames'),
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent(): string
    {
        $user = $this->brdb->selectUserById($this->id);
        //$club = $this->brdb->selectGetClubById($user['clubId']);
        $this->smarty->assign(array(
            'user'       => $user,
            //'club'       => $club,
            'tournament' => $this->getLatestTournamentFromUserId($this->id),
            #'games'      => $this->getRankedGamesByUser(),
        ));

        return $this->smarty->fetch('user/profil.tpl');
    }

    public function getLatestTournamentFromUserId($id): array
    {
        return $this->brdb->selectGetLatestTournamentFromUserId($id);
    }

}
?>
