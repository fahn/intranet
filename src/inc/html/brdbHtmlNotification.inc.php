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
require_once BASE_DIR."/inc/logic/prgNotification.inc.php";

class BrdbHtmlNotification extends BrdbHtmlPage
{

    private PrgPatternElementNotification $prgElementNotificattion;


    public function __construct()
    {
        parent::__construct();

        $this->prgElementNotificattion = new PrgPatternElementNotification($this->prgPatternElementLogin);
        $this->prgPattern->registerPrg($this->prgElementNotificattion);
    }//end __construct()


    protected function htmlBody(): void
    {
        $content = $this->TMPL_showList();

        $this->smarty->assign(
            ["content" => $content]
        );
        $this->smarty->display("index.tpl");
    }//end htmlBody()


    private function TMPL_showList(): string
    {
        $this->smarty->assign(
            [
                "row" => $this->getNotification(),
            ]
        );

        return $this->smarty->fetch("notification/list.tpl");
    }//end TMPL_showList()


    public function getNotification(): array
    {
        $user = $this->prgPatternElementLogin->getLoggedInUser();
       
        return $user->userId > 0 ? $this->brdb->statementGetNotificationByUserId($user->userId) : [];
    }//end getNotification()
}//end class
