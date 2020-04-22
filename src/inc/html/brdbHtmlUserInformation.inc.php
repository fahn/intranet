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
// load logic
require_once BASE_DIR."/inc/logic/prgUser.inc.php";


class BrdbHtmlUserInformation extends BrdbHtmlPage
{

    private $prgElementUser;


    public function __construct()
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }//end __construct()


    protected function htmlBody(): void
    {
        $content = $this->loadContent();

        // widget
        $widgetTournament = new TournamentWidget();
        $widgetRanking    = new RankingWidget();

        $this->smarty->assign(
            [
                "content"              => $content,
                "latestTournament"     => $widgetTournament->showWidget("latestTournaments"),
                "latestGamesInRanking" => $widgetRanking->showWidget("latestGames"),
            ]
        );

        $this->smarty->display("index.tpl");
    }//end htmlBody()


    private function loadContent(): string
    {
        $user = $this->brdb->selectUserById($this->id);
        // $club = $this->brdb->selectGetClubById($user["clubId"]);
        $this->smarty->assign(
            [
                "user"       => $user,
            // "club"       => $club,
                "tournament" => $this->getLatestTournamentFromUserId($this->id),
            // "games"      => $this->getRankedGamesByUser(),
            ]
        );

        return $this->smarty->fetch("user/profil.tpl");
    }//end loadContent()


    public function getLatestTournamentFromUserId($id): array
    {
        return $this->brdb->selectGetLatestTournamentFromUserId($id);
    }//end getLatestTournamentFromUserId()
}//end class
