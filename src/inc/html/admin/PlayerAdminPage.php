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
use \Badtra\Intranet\Logic\PrgPatternElementPlayer;
use \Badtra\Intranet\Logic\PrgPatternElementClub;

class PlayerAdminPage extends BrdbHtmlPage
{
    private PrgPatternElementPlayer $prgPatternElementPlayer;
    private PrgPatternElementClub $prgPatternElementClub;
    private array $info;

    public function __construct(?string $page = null)
    {
        if (!$this->prgPatternElementLogin->getLoggedInUser()->isAdmin())
        {
            // @TODO Link to no access
        }


        parent::__construct();


        $this->prgPatternElementPlayer = new PrgPatternElementPlayer($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementPlayer);

        // load Club
        $this->prgPatternElementClub = new PrgPatternElementClub($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementClub);

        $this->info  = array("firstName", "lastName", "email", "gender", "bday", "phone", "playerId", "clubId");
    }

    

    public function listView():string
    {

        $this->smarty->assign([
            "player"     => $this->brdb->selectGetAllPlayer(),
        ]);
        return $this->smartyFetchWrap("player/list.tpl");
    }

    

    public function addView():string
    {
        return $this->updatePlayer("add");
    }

    public function editView():string
    {
        return $this->updatePlayer("edit");
    }

    public function deleteView(): string
    {
        $user = $this->brdb->selectUserById($this->id);
        if (isset($user) && !empty($user) ) {
            $this->smarty->assign(array(
                "user"   => $this->user,
                "hidden" => "Delete User",
            ));

            return $this->smarty->fetch("admin/UserDelete.tpl");
        } else {
            //@TODO: switch back and message
        }
    }


    private function updatePlayer($action = "add"):string
    {
        try {
            $this->info = $this->id > 0 ? $this->brdb->selectUserById($this->id) : array();
        } catch (\Exception $e) {
            $this->info = array();
        }

        $this->smarty->assign(array(
            "clubs"  => $this->prgPatternElementClub->list(),
            "info"   => $this->info,
            "hidden" => $this->action == "add" ? $this->prgPatternElementPlayer::FORM_PLAYER_ACTION_INSERT : $this->prgPatternElementPlayer::FORM_PLAYER_ACTION_UPDATE,
            "task"   => $this->action,
         ));

        return $this->smarty->fetch("player/adminUpdate.tpl");
        unset($action);
    }
}

