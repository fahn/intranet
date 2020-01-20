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
$path=dirname(dirname(__FILE__));
require_once($path .'/brdbHtmlPage.inc.php');

include_once BASE_DIR .'/inc/logic/prgPlayer.inc.php';
include_once BASE_DIR .'/inc/logic/prgClub.inc.php';
include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminAllPlayer extends BrdbHtmlPage {
    private $prgPatternElementPlayer;

    private $info;

    const MAX_ENTRIES = 50;

    private $page;


    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
            $this->page = $page;
        }

        $this->prgPatternElementPlayer = new PrgPatternElementPlayer($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementPlayer);

        // load Club
        $this->prgPatternElementClub = new PrgPatternElementClub($this->brdb, $this->prgPatternElementLogin);

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

        $action = $this->tools->get('action');
        switch ($action) {
            case 'add_player':
                $content = $this->TMPL_updatePlayer('add');
                break;

            case 'sync':
                $content = $this->TMPL_sync();
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


    private function TMPL_listPlayer() {
        #$page = $this->tools->get("page");
        #$page = isset($page) && is_numeric($page) && $page > 0 ? $page-1 : 0;

        $this->smarty->assign(array(
            'player'     => $this->loadPlayerList(),
            'error'      => $this->brdb->getError(),
            #'pagination' => $this->getPageination($page),
        ));
        return $this->smarty->fetch('player/list.tpl');
    }

    private function TMPL_updatePlayer($action = 'add') {
         $id  = $this->tools->get('id');

        try {
            $this->info = $id > 0 ? $this->brdb->selectUserById($id) : array();
        } except(Exception $e1) {
            $this->info = array();;
        }

        $this->smarty->assign(array(
            'clubs'  => $this->prgPatternElementClub->list(),
            'info'   => $this->info,
            'hidden' => $action == 'add' ? $this->prgPatternElementPlayer::FORM_PLAYER_ACTION_INSERT : $this->prgPatternElementPlayer::FORM_PLAYER_ACTION_UPDATE,
            'task'   => $action,
         ));

        return $this->smarty->fetch('player/adminUpdate.tpl');
        usnet($id, $action);
    }


    private function TMPL_deletePlayer() {
        $id  = $this->tools->get('id');
        $user = $this->brdb->selectUserById($id);
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

    private function TMPL_sync() {
        $this->smarty->assign(array(
            'statistics'   => $this->syncPlayer(),
        ));

        return $this->smarty->fetch('player/adminSync.tpl');
    }

    /**
    PAGINATION
    */
    private function loadPlayerList() {
        #$this->countRows = $this->brdb->selectAllUser()->num_rows;
        #$max = self::MAX_ENTRIES*(1+$page);
        #$min = $max - self::MAX_ENTRIES;

        $playerList = $this->brdb->selectGetAllPlayer();
        $data = array();
        return (isset($playerList) && !empty($playerList)) ? $playerList : array();
    }
}
?>
