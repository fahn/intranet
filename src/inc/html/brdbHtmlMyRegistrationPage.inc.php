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

class BrdbHtmlMyRegistrationPage extends BrdbHtmlPage
{
    private PrgPatternElementUser $prgPatternElementRegister;

    public function __construct()
    {
        parent::__construct();

        $this->prgPatternElementRegister = new PrgPatternElementUser($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgPatternElementRegister);
    }


    public function htmlBody(): void
    {
        $content = $this->loadContent();
        $this->smarty->assign(array(
            "content" => $content,
        ));

        $this->smarty->display("index.tpl");

    }

    private function loadContent(): string
    {
        $this->smarty->assign(array(
        ));
        return $this->smarty->fetch("login/register.tpl");
    }
}

