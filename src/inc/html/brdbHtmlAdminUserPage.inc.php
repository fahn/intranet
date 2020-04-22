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
require_once "brdbHtmlPage.inc.php";
require_once BASE_DIR ."/inc/logic/prgUser.inc.php";

class BrdbHtmlAdminUserPage extends BrdbHtmlPage
{
    private $prgElementUser;

    public function __construct()
    {
        parent::__construct();

        $this->prgElementUser = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementUser);
    }

    protected function showProtectedArea(): bool
    {
        return $this->prgPatternElementLogin->getLoggedInUser()->isAdmin();
    }

    protected function htmlBodyProtectedArea() {}

    public function htmlBody(): void
    {
        $this->smarty->assign(array(
            "content" => $this->loadContent(),
        ));

        $this->smarty->display("index.tpl");
    }


    private function loadContent(): string
    {
        return $this->smarty->fetch("admin/users.tpl");
    }
}


