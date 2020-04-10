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
include_once('brdbHtmlPage.inc.php');
include_once BASE_DIR .'/inc/logic/prgNotification.inc.php';

class BrdbHtmlNotification extends BrdbHtmlPage 
{
    private PrgPatternElementNotification $prgElementNotificattion;

    public function __construct() 
    {
        parent::__construct();

        $this->prgElementNotificattion = new PrgPatternElementNotification($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementNotificattion);
    }

    protected function htmlBody(): void
    {
        $content = $this->TMPL_showList();

        $this->smarty->assign(array(
            'content' => $content,
        ));
        $this->smarty->display('index.tpl');
    }

    private function TMPL_showList(): string
    {
        $this->smarty->assign(array(
            'row'    => $this->getNotification(),
        ));

        return $this->smarty->fetch('notification/list.tpl');
    }

    public function getNotification(): array
    {
        $user = $this->prgPatternElementLogin->getLoggedInUser();
        
        return $user->userId > 0 ? $this->brdb->statementGetNotificationByUserId($user->userId) : array();
    }


}
?>
