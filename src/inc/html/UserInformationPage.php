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

use \Badtra\Intranet\Logic\PrgPatternElementUser;

// use \Badtra\Intranet\Widget\TournamentWidget;
// use \Badtra\Intranet\Widget\RankingWidget;

class UserInformationPage extends BrdbHtmlPage
{

    private PrgPatternElementUser $prgElementUser;


    public function __construct()
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }//end __construct()

    /*************** 
        VIEW
    ****************/

    /**
     * show profil
     *
     * @return string
     */
    public function profilView($id): string
    {
        $user = $this->brdb->selectUserById($id);
        // $club = $this->brdb->selectGetClubById($user["clubId"]);

        // Widgets
        // $widgetTournament = new TournamentWidget();
        // $widgetRanking = new RankingWidget();

        // render
        $this->smarty->assign(
            [
                "user"       => $user,
                // "club"       => $club,
                "tournament" => $this->getLatestTournamentFromUserId($this->id),
                // "games"      => $this->getRankedGamesByUser(),
                // "latestTournament"     => $widgetTournament->showWidget("latestTournaments"),
                // "latestGamesInRanking" => $widgetRanking->showWidget("latestGames"),
            ]
        );

        return $this->smartyFetchWrap("user/profil.tpl");
    }//end loadContent()


    public function showMyAccountView() {
        // @TODO replace 1 with USER_ID
        return $this->profilView(1);
    }

    private function getLatestTournamentFromUserId($id): array
    {
        return $this->brdb->selectGetLatestTournamentFromUserId($id);
    }//end getLatestTournamentFromUserId()
}//end class
