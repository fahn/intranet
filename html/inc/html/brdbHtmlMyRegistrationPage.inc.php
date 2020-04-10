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
include_once BASE_DIR .'/inc/logic/prgUser.inc.php';

class BrdbHtmlMyRegistrationPage extends BrdbHtmlPage 
{
    private PrgPatternElementUser $prgPatternElementRegister;

    public function __construct() 
    {
        parent::__construct();

        $this->prgPatternElementRegister = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementRegister);
    }


    public function htmlBody(): void
    {
        $content = $this->loadContent();
        $this->smarty->assign(array(
            'content' => $content,
        ));

        $this->smarty->display('index.tpl');

    }

    private function loadContent(): string
    {
        $this->smarty->assign(array(
        ));
        return $this->smarty->fetch('login/register.tpl');
    }
}

?>