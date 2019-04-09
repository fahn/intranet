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

include_once $_SERVER['BASE_DIR'] .'/inc/html/brdbHtmlPage.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgUser.inc.php';

// load widgets
include_once $_SERVER['BASE_DIR'] .'/inc/widget/tournament.widget.php';
include_once $_SERVER['BASE_DIR'] .'/inc/widget/ranking.widget.php';


class BrdbHtmlUserInformation extends BrdbHtmlPage {
    private $prgElementUser;
    private $vars;
    private $id;

    public function __construct() {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        #$this->prgPattern->registerPrg($this->prgElementUser);
    }

    public function processPage() {
        parent::processPage();
    }


    protected function htmlBody() {
        $id = $this->getGetVariable('id');
        $content = $this->loadContent($id);
        
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


    private function loadContent($id) {
        if(!isset($id) or !is_numeric($id)) {
            return "";
        }

        $user = $this->brdb->selectUserById($id)->fetch_assoc();
        $club = $this->brdb->selectGetClubById($user['clubId'])->fetch_assoc();
        $this->smarty->assign(array(
            'user'       => $user,
            'club'       => $club,
            'tournament' => $this->getLatestTournamentFromUserId($id),
            #'games'      => $this->getRankedGamesByUser(),
        ));

        return $this->smarty->fetch('user/profil.tpl');
    }


    public function getGetVariable($variableName) {
        return Tools::escapeInput($_GET[$variableName]);
    }

    public function getLatestTournamentFromUserId($id) {
        $res = $this->brdb->selectGetLatestTournamentFromUserId($id);
        $loop = array();
        if (!$this->brdb->hasError()) {
            while ($dataSet = $res->fetch_assoc()) {
                $loop[] = $dataSet; //new User($dataSet);
            }
        }
        return $loop;
    }

}
?>
