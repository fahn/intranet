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
    public function profilView(): string
    {
        $user = $this->brdb->selectUserById($this->id);
        // $club = $this->brdb->selectGetClubById($user["clubId"]);

        // Widgets
        $widgetTournament = new \Badtra\Intranet\Widget\TournamentWidget();
        $widgetRanking =new \Badtra\Intranet\Widget\RankingWidget();

        // render
        $this->smarty->assign(
            [
                "user"       => $user,
                // "club"       => $club,
                "tournament" => $this->getLatestTournamentFromUserId($this->id),
                // "games"      => $this->getRankedGamesByUser(),
                "latestTournament"     => $widgetTournament->showWidget("latestTournaments"),
                "latestGamesInRanking" => $widgetRanking->showWidget("latestGames"),
            ]
        );

        return $this->smartyFetchWrap("user/profil.tpl");
    }//end loadContent()

    private function getLatestTournamentFromUserId($id): array
    {
        return $this->brdb->selectGetLatestTournamentFromUserId($id);
    }//end getLatestTournamentFromUserId()
}//end class
