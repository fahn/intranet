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
require($path .'/brdbHtmlPage.inc.php');
include_once BASE_DIR .'/inc/logic/prgSync.inc.php';
// load models
include_once BASE_DIR .'/inc/model/club.inc.php';
include_once BASE_DIR .'/inc/model/player.inc.php';
// load logic
include_once BASE_DIR .'/inc/logic/prgClub.inc.php';
include_once BASE_DIR .'/inc/logic/prgPlayer.inc.php';

class BrdbHtmlAdminSyncPage extends BrdbHtmlPage {
    private $prgPatternElementSync;

    private $_page;

    public function __construct($page = null) 
    {
        parent::__construct();

        $this->prgPatternElementSync = new PrgPatternElementSync($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementSync);

        if ($page != null) {
             $this->_page = $page;
        }

        # load links
        $links = array(
            'startSync' => $this->prgPatternElementSync->linkTo(array('page' => $this->_page, 'action' => 'sync')),
        );

        $this->smarty->assign('links', $links);
    }


    public function htmlBody(): void
    {
        switch ($this->action) {
            case 'sync':

            default:
                $content = $this->loadContent();
                break;
        }

        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent(): string
    {
        $this->smarty->assign(array(

        ));
        return $this->smarty->fetch('sync/status.tpl');
    }
}
?>