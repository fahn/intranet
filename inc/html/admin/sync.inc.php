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
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgSync.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/tools.inc.php';

// load models
include_once $_SERVER['BASE_DIR'] .'/inc/model/club.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/model/player.inc.php';

// load logic
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgClub.inc.php';
include_once $_SERVER['BASE_DIR'] .'/inc/logic/prgPlayer.inc.php';

class BrdbHtmlAdminSyncPage extends BrdbHtmlPage {
    private $prgPatternElementSync;
    private $prgPatternElementClub;

    public function __construct($page = null) {
        parent::__construct();

        if ($page != null) {
             $this->_page = $page;
        }

        # load links
        $links = array(
            'add' => $this->tools->linkTo(array('page' => $this->_page, 'action' => 'add')),
            'list' => $this->tools->linkTo(array('page' => $this->_page, 'action' => 'add')),
        );

        $this->smarty->assign('links', $links);

        $this->prgPatternElementSync = new PrgPatternElementSync($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementSync);


        $this->prgPatternElementClub = new PrgPatternElementClub($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementClub);

        $this->prgPatternElementPlayer = new PrgPatternElementPlayer($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementPlayer);
    }


    public function htmlBody() {
        $action = $this->tools->get("action");
        $id     = $this->tools->get("id");

        //$this->syncClubs();
        $this->syncPlayer();

        switch ($action) {
          default:
            $content = $this->loadContent();
            break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent() {
        $this->smarty->assign(array(

        ));
        return $this->smarty->fetch('sync/status.tpl');
    }


    




}

?>
