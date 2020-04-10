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
$path=dirname(dirname(__FILE__));
require_once($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgPlayer.inc.php';
include_once BASE_DIR .'/inc/logic/prgClub.inc.php';

class BrdbHtmlAdminAllPlayer extends BrdbHtmlPage 
{
    private PrgPatternElementPlayer $prgPatternElementPlayer;

    private array $info;
    private $_page;


    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
            $this->_page = $page;
        }

        $this->prgPatternElementPlayer = new PrgPatternElementPlayer($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementPlayer);

        // load Club
        $this->prgPatternElementClub = new PrgPatternElementClub($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementClub);

        $this->info  = array('firstName', 'lastName', 'email', 'gender', 'bday', 'phone', 'playerId', 'clubId');
    }

    protected function showProtectedArea() {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }


    public function htmlBody() {
        $content = "";
        // check if Admin
        if(!$this->prgPatternElementLogin->getLoggedInUser()->isAdmin()) {
            $content = $this->smarty->fetch('no_access.tpl');
            return;
        }

        switch ($this->action) {
            case 'add_player':
                $content = $this->TMPL_updatePlayer('add');
                break;

            case 'edit':
                $content = $this->TMPL_updatePlayer('edit');
                break;

            case 'delete':
                $content = $this->TMPL_deletePlayer();
                break;

            default:
                $content = $this->TMPL_listPlayer();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function TMPL_listPlayer():string
    {

        $this->smarty->assign(array(
            'player'     => $this->loadPlayerList(),
            'error'      => $this->brdb->getError(),
            #'pagination' => $this->getPageination($page),
        ));
        return $this->smarty->fetch('player/list.tpl');
    }

    private function TMPL_updatePlayer($action = 'add'):string
    {
        try {
            $this->info = $this->id > 0 ? $this->brdb->selectUserById($this->id) : array();
        } catch (Exception $e) {
            $this->info = array();
        }

        $this->smarty->assign(array(
            'clubs'  => $this->prgPatternElementClub->list(),
            'info'   => $this->info,
            'hidden' => $this->action == 'add' ? $this->prgPatternElementPlayer::FORM_PLAYER_ACTION_INSERT : $this->prgPatternElementPlayer::FORM_PLAYER_ACTION_UPDATE,
            'task'   => $this->action,
         ));

        return $this->smarty->fetch('player/adminUpdate.tpl');
        unset($id, $action);
    }


    private function TMPL_deletePlayer(): string
    {
        $user = $this->brdb->selectUserById($this->id);
        if (isset($user) && !empty($user) ) {
            $this->smarty->assign(array(
                'user'   => $this->user,
                'hidden' => "Delete User",
            ));

            return $this->smarty->fetch('admin/UserDelete.tpl');
        } else {
            //@TODO: switch back and message
        }
    }

    private function loadPlayerList(): ?array
    {
        return $this->brdb->selectGetAllPlayer();

    }
}
?>
