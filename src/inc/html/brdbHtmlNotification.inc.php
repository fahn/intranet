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
require_once BASE_DIR ."/inc/logic/prgNotification.inc.php";

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
            "content" => $content,
        ));
        $this->smarty->display("index.tpl");
    }

    private function TMPL_showList(): string
    {
        $this->smarty->assign(array(
            "row"    => $this->getNotification(),
        ));

        return $this->smarty->fetch("notification/list.tpl");
    }

    public function getNotification(): array
    {
        $user = $this->prgPatternElementLogin->getLoggedInUser();
       
        return $user->userId > 0 ? $this->brdb->statementGetNotificationByUserId($user->userId) : array();
    }


}

