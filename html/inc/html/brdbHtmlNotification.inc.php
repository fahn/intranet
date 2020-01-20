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
include_once('brdbHtmlPage.inc.php');
#include_once BASE_DIR .'/inc/logic/prgPattern.inc.php';
#include_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlNotification extends BrdbHtmlPage {

    public function __construct() {
        parent::__construct();

        $this->tools->secure_array($_GET);
    }

    public function processPage() {
        parent::processPage();
    }

    protected function htmlBody() {
        $content = $this->TMPL_showList();

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function TMPL_showList() {
        $this->smarty->assign(array(
            'row'    => $this->getTeam(),
        ));

        return $this->smarty->fetch('team/list.tpl');
    }

    public function getNotification() {
        $user = $this->prgPatternElementLogin->getLoggedInUser();
        
        return $user->$userId > 0 ? $this->brdb->statementGetNotifationByUserId($user->userId) : array();
    }

}
?>
