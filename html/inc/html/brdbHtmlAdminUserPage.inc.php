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

require_once BASE_DIR .'/inc/logic/prgUser.inc.php';
require_once BASE_DIR .'/inc/logic/tools.inc.php';

class BrdbHtmlAdminUserPage extends BrdbHtmlPage 
{
    private $prgElementUser;

    public function __construct(): void
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->brdb, $this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }

    protected function showProtectedArea(): void
    {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }

    protected function htmlBodyProtectedArea() {}

    public function htmlBody(): void
    {
        $this->smarty->assign(array(
            'content' => $this->loadContent(),
        ));

        $this->smarty->display('index.tpl');
    }


    private function loadContent(): string
    {
        return $this->smarty->fetch('admin/users.tpl');
    }
}

?>
